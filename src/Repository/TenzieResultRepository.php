<?php

namespace App\Repository;

use App\Entity\TenzieResult;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TenzieResult>
 *
 * @method TenzieResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method TenzieResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method TenzieResult[]    findAll()
 * @method TenzieResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TenzieResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TenzieResult::class);
    }

    /**
     * @param TenzieResult $entity
     * @param bool $flush
     * @return void
     */
    public function add(TenzieResult $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param TenzieResult $entity
     * @param bool $flush
     * @return void
     */
    public function remove(TenzieResult $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
    //     * @param User $user
    //     * @return TenzieResult[]
    //     *
     */
    public function getActiveUserTenzieResults(User $user):array{

        $qb = $this->createQueryBuilder('tr')
            ->leftJoin("tr.user","u")
            ->andWhere("u.id = :user")
            ->andWhere("tr.deleted = :deleted")
            ->setParameter("user", $user->getId()->toBinary())
            ->setParameter("deleted", false);

        $query = $qb->getQuery();

        return $query->execute();
    }

    /**\
     *
     * @return TenzieResult[]
     */
//TenzieResult[]
    public function getBestTenzieResults(int $level):array{

        $qb = $this->createQueryBuilder('tr')
            ->leftJoin("tr.user","u")
            ->where("tr.deleted = :deleted")
            ->andWhere("tr.level = :level")
            ->setParameter("deleted", false)
            ->orderBy("tr.level","DESC")
            ->setParameter(":level",$level)
            ->setFirstResult(0)
            ->setMaxResults(10);


        $query = $qb->getQuery();

        return $query->execute();
    }
//    /**
//     * @return TenzieResult[] Returns an array of TenzieResult objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TenzieResult
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
