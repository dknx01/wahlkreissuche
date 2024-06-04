<?php

namespace App\Repository;

use App\Entity\ElectionPoster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<ElectionPoster>
 *
 * @method ElectionPoster|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElectionPoster|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElectionPoster[]    findAll()
 * @method ElectionPoster[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElectionPosterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ElectionPoster::class);
    }

    public function save(ElectionPoster $electionPoster): void
    {
        $this->_em->persist($electionPoster);
        $this->_em->flush();
    }

    /**
     * @return ElectionPoster[]
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
        $qb = $this->createQueryBuilder('ep');
        $qb->distinct(true)
            ->select('ep.address.district', 'ep.address.city')
            ->where('ep.active = 1');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<array-key, string>
     */
    public function findAllCities(): array
    {
        $qb = $this->createQueryBuilder('ep');
        $qb->distinct(true)
            ->select('ep.address.city')
            ->where('ep.active = 1');

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @return array<array-key, string>
     */
    public function findAllDistricts(): array
    {
        $qb = $this->createQueryBuilder('ep');
        $qb->distinct(true)
            ->select('ep.address.district')
            ->where('ep.active = 1')
            ->andWhere($qb->expr()->isNotNull('ep.address.district '))
            ->andWhere('ep.address.district != \'\'');

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @return array<array-key, string>
     */
    public function findAllDistrictsByCityAndState(string $state, string $city): array
    {
        $qb = $this->createQueryBuilder('ep');
        $qb->distinct(true)
            ->select('ep.address.district as district')
            ->where('ep.active = 1')
            ->andWhere('ep.address.city = :city')
            ->andWhere('ep.address.state = :state')
            ->andWhere($qb->expr()->isNotNull('ep.address.district '))
            ->setParameter('state', $state)
            ->setParameter('city', $city);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return ElectionPoster[]
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
        $qb = $this->createQueryBuilder('ep');
        $qb->distinct()
           ->select('ep.address.state as state')
           ->where('ep.active = 1')
           ->andWhere('ep.address.state NOT IN(:berlin)')
           ->setParameter('berlin', 'Berlin');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<string, string>
     */
    public function findAllActiveStatesWithBerlin(): array
    {
        $qb = $this->createQueryBuilder('ep');
        $qb->distinct()
           ->select('ep.address.state as state')
           ->where('ep.active = 1');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return ElectionPoster[]
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
        $qb = $this->createQueryBuilder('ep');
        $qb->distinct(true)
            ->select('ep.address.city as city')
            ->where('ep.active = 1')
            ->andWhere('ep.address.state = :state')
            ->andWhere($qb->expr()->isNotNull('ep.address.city'))
            ->setParameter('state', $state);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return ElectionPoster[]
     */
    public function findAllByState(string $state, string $activeOnly): array
    {
        $qb = $this->createQueryBuilder('ep');
        $qb->andWhere('ep.address.state = :state')
            ->setParameter('state', $state);
        if ($activeOnly === 'active') {
            $qb->andWhere('ep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return ElectionPoster[]
     */
    public function findAllByCity(string $city, string $activeOnly): array
    {
        $qb = $this->createQueryBuilder('ep');
        $qb->andWhere('ep.address.city = :city')
            ->setParameter('city', $city);
        if ($activeOnly === 'active') {
            $qb->andWhere('ep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return ElectionPoster[]
     */
    public function findAllByCityAndDistrict(string $city, string $district, string $activeOnly): array
    {
        $qb = $this->createQueryBuilder('ep');
        $qb->andWhere('ep.address.city = :city')
            ->andWhere('ep.address.district = :district')
            ->setParameter('city', $city)
            ->setParameter('district', $district);
        if ($activeOnly === 'active') {
            $qb->andWhere('ep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return ElectionPoster[]
     */
    public function findAllByUser(?string $userName, string $activeOnly, ?string $year): array
    {
        if ($userName === null) {
            return [];
        }

        $qb = $this->createQueryBuilder('ep');
        $qb->andWhere('ep.createdBy = :user')
            ->setParameter('user', $userName);
        if ($activeOnly === 'active') {
            $qb->andWhere('ep.active = :active')
                ->setParameter('active', true);
        }
        if ($year !== null) {
            $qb->andWhere('YEAR(ep.createdAt) = :year')
                ->setParameter('year', $year);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return string[]
     */
    public function findAllYearsByUser(?string $userName, string $activeOnly): array
    {
        if ($userName === null) {
            return [];
        }

        $qb = $this->createQueryBuilder('ep');
        $qb->select('DISTINCT DATE_FORMAT(ep.createdAt, \'%Y\')');
        $qb->andWhere('ep.createdBy = :user')
            ->setParameter('user', $userName);
        if ($activeOnly === 'active') {
            $qb->andWhere('ep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @return ElectionPoster[]
     */
    public function findAllForOverview(string $activeOnly): array
    {
        $qb = $this->createQueryBuilder('ep');
        if ($activeOnly === 'active') {
            $qb->andWhere('ep.active = :active')
                ->setParameter('active', true);
        }

        return $qb->getQuery()->getResult();
    }
}
