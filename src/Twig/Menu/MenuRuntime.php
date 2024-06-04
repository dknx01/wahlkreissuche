<?php

declare(strict_types=1);

namespace App\Twig\Menu;

use App\EventListener\MobileDetectListener;
use App\Options\BtwKreise;
use App\Repository\ElectionPosterRepository;
use App\Repository\WahlkreisRepository;
use App\Repository\WishElectionPosterRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

readonly class MenuRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private ElectionPosterRepository $posterRepo,
        private WishElectionPosterRepository $wishElectionPosterRepo,
        private WahlkreisRepository $wahlkreisRepo,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getPosterStates(): array
    {
        return $this->posterRepo->findAllActiveStates();
    }

    /**
     * @return array<string, string>
     */
    public function getLtwStates(): array
    {
        return $this->wahlkreisRepo->findAllStatesByType('LTW');
    }

    /**
     * @return array<string, string>
     */
    public function getWishPosterStates(): array
    {
        return $this->wishElectionPosterRepo->findAllActiveStates();
    }

    /**
     * @return array<string, string>
     */
    public function getPosterStatesWithBerlin(): array
    {
        return $this->posterRepo->findAllActiveStatesWithBerlin();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function getPosterCityDistrict(): array
    {
        $entries = [];
        foreach ($this->posterRepo->findAllDistrictsAndCities() as $poster) {
            if (!array_key_exists($poster['address.city'], $entries)) {
                $entries[$poster['address.city']] = [];
            }
            if ($poster['address.district'] !== '') {
                $entries[$poster['address.city']][] = $poster['address.district'];
            }
        }

        return $entries;
    }

    /**
     * @return array<string, float>
     */
    public function getStateCenter(string $state): array
    {
        return BtwKreise::getConfig($state);
    }

    public function isMobile(): bool
    {
        return $this->requestStack->getCurrentRequest()
                ->attributes->get('isMobile') === MobileDetectListener::Mobile;
    }
}
