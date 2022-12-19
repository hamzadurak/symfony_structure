<?php

namespace App\Repository\Hotel;

use App\Entity\HotelZone;
use App\Interface\Hotel\HotelZoneInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HotelZone>
 *
 * @method HotelZone|null find($id, $lockMode = null, $lockVersion = null)
 * @method HotelZone|null findOneBy(array $criteria, array $orderBy = null)
 * @method HotelZone[]    findAll()
 * @method HotelZone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelZoneRepository extends ServiceEntityRepository implements HotelZoneInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HotelZone::class);
    }

    /**
     * @param HotelZone $entity
     * @param bool $flush
     * @return void
     */
    public function storeUpdate(HotelZone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param HotelZone $entity
     * @param bool $flush
     * @return void
     */
    public function destroy(HotelZone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $requestAll
     * @return float|int|mixed|string
     */
    public function getHotels($requestAll): mixed
    {
        $query = $this->createQueryBuilder('h');
        if (isset($requestAll['country'])) {
            $query->where('h.country LIKE :country')
                ->setParameter('country', '%' . $requestAll['country'] . '%');
        }
        if (isset($requestAll['city'])) {
            $query->where('h.city LIKE :city')
                ->setParameter('city', '%' . $requestAll['city'] . '%');
        }
        return $query
            ->getQuery()
            ->getResult();
    }
}
