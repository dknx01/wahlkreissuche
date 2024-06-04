<?php

declare(strict_types=1);

namespace App\UnitTests\Service\Domain;

use App\Repository\WahlkreisRepository;
use App\Service\Domain\Search;
use App\Service\Domain\Search\AddressData;
use App\Service\GeoJsonReader;
use App\UnitTests\Misc\WahlkreisHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Collection\Collection;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SearchTest extends TestCase
{
    use ProphecyTrait;

    private const STATE = 'Berlin';
    private const ADDRESS = 'Panoramastraße 1A, 10178 Berlin';
    private AsciiSlugger $slugger;
    private GeoJsonReader|ObjectProphecy $reader;
    private WahlkreisRepository|ObjectProphecy $repo;
    private CacheInterface $cache;
    private HttpClientInterface $client;

    protected function setUp(): void
    {
        $this->slugger = new AsciiSlugger();
        $this->repo = $this->prophesize(WahlkreisRepository::class);
        $this->reader = $this->prophesize(GeoJsonReader::class);
        $this->cache = new ArrayAdapter();
        $this->client = new MockHttpClient();
    }

    public function testSearchByAddressWithResultFromCache(): void
    {
        $element = $this->createCacheItem();

        $this->cache->get(
            $this->slugger->slug(self::ADDRESS . ' ' . self::STATE)->camel()->title()->toString(),
            static function (CacheItem $item) use ($element) {
                $data = new Collection(AddressData::class);

                $data->add($element);

                return $data;
            }
        );
        $search = $this->getSearch();

        $result = $search->searchByAddress(self::ADDRESS, self::STATE);
        $this->assertCount(1, $result);
        $this->assertEquals($element, $result->first());
    }

    public function testSearchByAddressWithResultFromCacheWithEmptyData(): void
    {
        $this->cache->get(
            $this->slugger->slug(self::ADDRESS . ' ' . self::STATE)->camel()->title()->toString(),
            static function (CacheItem $item) {
                return new Collection(AddressData::class);
            }
        );
        $search = $this->getSearch();

        $result = $search->searchByAddress(self::ADDRESS, self::STATE);
        $this->assertTrue($result->isEmpty());
    }

    public function testSearchByAddressWithResult(): void
    {
        $this->client->setResponseFactory(
            [new MockResponse(
                <<<JSON
[
  {
    "lat": "52.5205791",
    "lon": "13.4089041",
    "display_name": "1A, Panoramastraße, Mitte, Mitte, Mitte, Berlin, 10178, Deutschland",
    "class": "place",
    "type": "house",
    "address": {
      "house_number": "1A",
      "road": "Panoramastraße",
      "state": "Berlin",
      "neighbourhood": "Mitte",
      "suburb": "Mitte",
      "borough": "Mitte",
      "city": "Berlin",
      "ISO3166-2-lvl4": "DE-BE",
      "postcode": "10178",
      "country": "Deutschland",
      "country_code": "de",
      "town": "Berlin"
    }
  }
]
JSON
            )]
        );
        $this->repo->findByState(self::STATE)->willReturn(WahlkreisHelper::getWahlkreise()->toArray());
        $this->reader = new GeoJsonReader();

        $search = $this->getSearch();

        $result = $search->searchByAddress(self::ADDRESS, self::STATE);
        $this->assertSame($result->count(), 1);
    }

    private function createCacheItem(): AddressData
    {
        return new AddressData(
            houseNumber: '1',
            road: 'Lindenstr.',
            suburb: 'Köpenick',
            borough: 'Köpenick',
            city: 'Berlin',
            state: self::STATE,
            postCode: '12345',
            country: 'Deutschland',
            countryCode: 'DE',
            lat: '13.23425',
            lon: '51.234'
        );
    }

    private function getSearch(): Search
    {
        return new Search(
            reader: $this->reader instanceof ObjectProphecy ? $this->reader->reveal() : $this->reader,
            wahlkreisRepository: $this->repo->reveal(),
            cache: $this->cache,
            slugger: $this->slugger,
            client: $this->client
        );
    }
}
