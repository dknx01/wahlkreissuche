<?php

namespace App\Repository;

use App\Entity\Wahllokal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Wahllokal>
 *
 * @method Wahllokal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wahllokal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wahllokal[]    findAll()
 * @method Wahllokal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WahllokalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wahllokal::class);
    }

    public function save(Wahllokal $wahllokal): void
    {
        $this->_em->persist($wahllokal);
        $this->_em->flush();
    }

    /**
     * @return Wahllokal[]
     */
    public function findLimit(int $limit): array
    {
        return $this->findBy([], null, $limit);
    }
}
