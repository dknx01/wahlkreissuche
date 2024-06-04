<?php

namespace App\Repository;

use App\Entity\Wahlkreis;
use Brick\Geo\Point;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Wahlkreis>
 *
 * @method Wahlkreis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wahlkreis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wahlkreis[]    findAll()
 * @method Wahlkreis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WahlkreisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wahlkreis::class);
    }

    public function save(Wahlkreis $wahlkreis): void
    {
        $this->_em->persist($wahlkreis);
        $this->_em->flush();
    }

    public function saveMultiPolygon(\LongitudeOne\Spatial\PHP\Types\Geometry\MultiPolygon $multiPolygon): void
    {
        $this->_em->persist($multiPolygon);
    }

    //    public function findBy2(array $array)
    //    {
    //        $sql = 'select st_asgeojson(geometry_geometry), btw_name from wahlkreis where btw_number = :number';
    //        $rs = $this->_em->getConnection()->executeQuery(
    //            $sql,
    //            ['number' => 58]
    //        );
    //
    //        return $rs->fetchAllAssociative();
    //    }

    /**
     * @return array<array-key, array<string, mixed>>
     */
    public function findByPoint(Point $point): array
    {
        $columns = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)
            ->getClassMetadata()->getColumnNames();
        $sql = 'SELECT %s FROM %s WHERE ST_Contains(geometry_geometry, ST_GEOMFROMTEXT(:point))';
        $columns = implode(', ', $columns);
        $sql = sprintf($sql, $columns, $this->getClassMetadata()->getTableName());

        $result = $this->_em->getConnection()->executeQuery($sql, ['point' => $point->asText()]);

        return $result->fetchAllAssociative();
    }

    /**
     * @return Wahlkreis[]
     */
    public function findByState(string $state): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->Where('w.state = :state')
            ->andWhere($qb->expr()->in('w.type', $this->getTypeByState($state)))
            ->setParameter('state', $state);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return string[]
     */
    private function getTypeByState(string $state): array
    {
        return match ($state) {
            'Berlin' => ['AGH', 'BTW'],
            default => ['BTW']
        };
    }

    /**
     * @return array<string, string>
     */
    public function findAllStatesByType(string $type): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->distinct()
            ->select('w.state')
            ->andWhere('w.type = :type')
            ->setParameter('type', $type);

        return $qb->getQuery()->getResult();
    }
}
