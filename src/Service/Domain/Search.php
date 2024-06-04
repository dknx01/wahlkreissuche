<?php

declare(strict_types=1);

namespace App\Service\Domain;

use App\Entity\Wahlkreis\Btw;
use App\Repository\WahlkreisRepository;
use App\Service\Domain\Search\AddressData;
use App\Service\Domain\Search\Geofence;
use App\Service\GeoJsonReader;
use Ramsey\Collection\Collection;
use Ramsey\Collection\CollectionInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Search
{
    private const UNSET_ADDRESS_VALUES = [
        'house_number' => 'houseNumber',
        'postcode' => 'postCode',
        'country_code' => 'countryCode',
    ];
    private const CITY_FIELDS = ['city', 'town', 'village'];

    public function __construct(
        private GeoJsonReader $reader,
        private WahlkreisRepository $wahlkreisRepository,
        private CacheInterface $cache,
        private AsciiSlugger $slugger,
        private HttpClientInterface $client,
    ) {
    }

    /**
     * @return CollectionInterface<Geofence>
     *
     * @throws \JsonException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function searchByAddress(string $address, string $state): CollectionInterface
    {
        $key = $this->slugger->slug($address . ' ' . $state)->camel()->title()->toString();
        /** @var CollectionInterface<Geofence> $data */
        $data = $this->cache->get($key, $this->getContent($state, $address));

        if ($data->isEmpty()) {
            $this->cache->delete($key);
        }

        return $data;
    }

    private function getContent(string $state, string $address): \Closure
    {
        return function (CacheItem $item) use ($address, $state) {
            $addresses = $this->getAddressData(sprintf('%s %s', $address, $state));
            $data = $this->getGeoFences($state)->filter($this->getGeoFenceFilter($addresses));

            return $this->removeDuplicateEntries($data);
        };
    }

    /**
     * @return Collection<Geofence>
     */
    private function getGeoFences(string $state): Collection
    {
        $areas = $this->wahlkreisRepository->findByState($state);

        $geoFences = new Collection(Geofence::class);

        foreach ($areas as $area) {
            $geometry = $this->reader->read($area->getGeometry()->getGeometry()->toJson());

            $geoFences->add(new Geofence($area, $geometry));
        }

        return $geoFences;
    }

    /**
     * @param Collection<AddressData> $searchPoints
     */
    private function getGeoFenceFilter(Collection $searchPoints): \Closure
    {
        return static function (Geofence $geofence) use ($searchPoints) {
            /** @var AddressData $searchPoint */
            foreach ($searchPoints as $searchPoint) {
                if ($geofence->getPolygon()->contains($searchPoint->getPoint())) {
                    $geofence->setAddress($searchPoint);

                    return true;
                }
            }

            return false;
        };
    }

    /**
     * @return Collection<AddressData>
     *
     * @throws \JsonException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getAddressData(string $address): Collection
    {
        $url = sprintf(
            'https://nominatim.openstreetmap.org/?q=%s&format=json&addressdetails=1&dedupe=1',
            urlencode($address)
        );
        $response = $this->client->request('GET', $url, [
            'headers' => ['Accept-Language' => 'de-DE'],
        ]);
        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        array_walk(
            $content,
            static function ($value, $key) use (&$content) {
                foreach (self::UNSET_ADDRESS_VALUES as $field => $newField) {
                    if (!array_key_exists($field, $value['address'])) {
                        continue;
                    }
                    $value['address'][$newField] = $value['address'][$field];
                    unset($value['address'][$field]);
                }
                $value['address']['lat'] = $value['lat'];
                $value['address']['lon'] = $value['lon'];

                foreach (self::CITY_FIELDS as $cityField) {
                    if (!empty($value['address']['city'])) {
                        continue;
                    }
                    if (array_key_exists($cityField, $value['address'])) {
                        $value['address']['city'] = $value['address'][$cityField];
                    }
                }

                $fields = [
                    'houseNumber' => '',
                    'road' => '',
                    'suburb' => '',
                    'borough' => '',
                    'city' => '',
                    'state' => '',
                    'postCode' => '',
                    'country' => '',
                    'countryCode' => '',
                    'lat' => '',
                    'lon' => '',
                ];
                $value['address'] = array_intersect_key($value['address'], $fields);
                $value['address'] = array_merge($fields, $value['address']);
                $content[$key] = $value;
            }
        );

        $content = array_map(
            static function ($data) {
                return new AddressData(...$data['address']);
            },
            $content
        );

        $collection = new Collection(AddressData::class);
        foreach ($content as $item) {
            if ($collection->filter(
                fn (AddressData $addressData): bool => $addressData->equals($item) && !$addressData->getId()->equals($item->getId())
            )
                    ->count() === 0
            ) {
                $collection->add($item);
            }
        }

        return $collection;
    }

    /**
     * @param CollectionInterface<Geofence> $collection
     *
     * @return CollectionInterface<Geofence>
     */
    private function removeDuplicateEntries(CollectionInterface $collection): CollectionInterface
    {
        /**
         * @var Geofence $item */
        foreach ($collection as $item) {
            $filter = function (Geofence $entry) use ($item) {
                return ($entry->getAddressString() === $item->getAddressString())
                    && !$entry->getWahlkreis()->getId()->equals($item->getWahlkreis()->getId());
            };
            if (($otherEntries = $collection->filter($filter))->count() === 0) {
                continue;
            }
            /** @var Geofence $geoFence */
            foreach ($otherEntries as $geoFence) {
                if ($item->getWahlkreis()->isAgh()
                    && $geoFence->getWahlkreis()->isBtw()
                ) {
                    $btw = new Btw(
                        number: $geoFence->getWahlkreis()->getBtw()->getNumber(),
                        name: $geoFence->getWahlkreis()->getBtw()->getName(),
                        stateName: $geoFence->getWahlkreis()->getBtw()->getStateName(),
                        stateNumber: $geoFence->getWahlkreis()->getBtw()->getStateNumber(),
                    );
                    $item->getWahlkreis()->setBtw($btw);
                    $collection->remove($geoFence);
                }
            }
        }

        return new Collection($collection->getType(), array_merge([], $collection->toArray()));
    }
}
