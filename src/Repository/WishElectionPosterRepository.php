<?php

namespace App\Repository;

use App\Entity\WishElectionPoster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<WishElectionPoster>
 *
 * @method WishElectionPoster|null find($id, $lockMode = null, $lockVersion = null)
 * @method WishElectionPoster|null findOneBy(array $criteria, array $orderBy = null)
 * @method WishElectionPoster[]    findAll()
 * @method WishElectionPoster[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishElectionPosterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WishElectionPoster::class);
    }

    public function save(WishElectionPoster $wishElectionPoster): void
    {
        $this->_em->persist($wishElectionPoster);
        $this->_em->flush();
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllActive(): array
    {
        return $this->findBy(['active' => true]);
    }

    /**
     * @return array<array-key, array<array-key, string>>
     */
    public function findAllDistrictsAndCities(): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->distinct(true)
            ->select('wep.address.district', 'wep.address.city')
            ->where('wep.active = 1');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<array-key, string>
     */
    public function findAllCities(): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->distinct(true)
            ->select('wep.address.city')
            ->where('wep.active = 1');

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @return array<array-key, string>
     */
    public function findAllDistricts(): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->distinct(true)
            ->select('wep.address.district')
            ->where('wep.active = 1')
            ->andWhere($qb->expr()->isNotNull('wep.address.district '))
            ->andWhere('wep.address.district != \'\'');

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @return array<array-key, string>
     */
    public function findAllDistrictsByCityAndState(string $state, string $city): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->distinct(true)
            ->select('wep.address.district as district')
            ->where('wep.active = 1')
            ->andWhere('wep.address.city = :city')
            ->andWhere('wep.address.state = :state')
            ->andWhere($qb->expr()->isNotNull('wep.address.district '))
            ->setParameter('state', $state)
            ->setParameter('city', $city);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllActiveByDistrictAndCityAndState(string $district, string $city, string $state): array
    {
        return $this->findBy(['active' => true, 'address.district' => $district, 'address.city' => $city, 'address.state' => $state]);
    }

    /**
     * @return array<string, string>
     */
    public function findAllActiveStates(): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->distinct()
           ->select('wep.address.state as state')
           ->where('wep.active = 1')
           ->andWhere('wep.address.state NOT IN(:berlin)')
           ->setParameter('berlin', 'Berlin');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<string, string>
     */
    public function findAllActiveStatesWithBerlin(): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->distinct()
           ->select('wep.address.state as state')
           ->where('wep.active = 1');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllActiveByStateAndCity(string $city, string $state): array
    {
        return $this->findBy(['active' => true, 'address.state' => $state, 'address.city' => $city]);
    }

    /**
     * @return array<array-key, string>
     */
    public function findAllCitiesByState(string $state): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->distinct(true)
            ->select('wep.address.city as city')
            ->where('wep.active = 1')
            ->andWhere('wep.address.state = :state')
            ->andWhere($qb->expr()->isNotNull('wep.address.city'))
            ->setParameter('state', $state);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllByState(string $state, string $activeOnly): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->andWhere('wep.address.state = :state')
            ->setParameter('state', $state);
        if ($activeOnly === 'active') {
            $qb->andWhere('wep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllByCity(string $city, string $activeOnly): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->andWhere('wep.address.city = :city')
            ->setParameter('city', $city);
        if ($activeOnly === 'active') {
            $qb->andWhere('wep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllByCityAndDistrict(string $city, string $district, string $activeOnly): array
    {
        $qb = $this->createQueryBuilder('wep');
        $qb->andWhere('wep.address.city = :city')
            ->andWhere('wep.address.district = :district')
            ->setParameter('city', $city)
            ->setParameter('district', $district);
        if ($activeOnly === 'active') {
            $qb->andWhere('wep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllByUser(?string $userName, string $activeOnly): array
    {
        if ($userName === null) {
            return [];
        }

        $qb = $this->createQueryBuilder('wep');
        $qb->andWhere('wep.createdBy = :user')
            ->setParameter('user', $userName);
        if ($activeOnly === 'active') {
            $qb->andWhere('wep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllForOverview(string $activeOnly): array
    {
        $qb = $this->createQueryBuilder('eep');
        if ($activeOnly === 'active') {
            $qb->andWhere('eep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }
}
