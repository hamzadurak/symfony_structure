<?php

namespace App\Repository\Hotel;

use App\Entity\HotelCategory;
use App\Interface\Hotel\HotelCategoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HotelCategory>
 *
 * @method HotelCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method HotelCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method HotelCategory[]    findAll()
 * @method HotelCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelCategoryRepository extends ServiceEntityRepository implements HotelCategoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HotelCategory::class);
    }

    /**
     * @param HotelCategory $entity
     * @param bool $flush
     * @return void
     */
    public function storeUpdate(HotelCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param HotelCategory $entity
     * @param bool $flush
     * @return void
     */
    public function destroy(HotelCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
