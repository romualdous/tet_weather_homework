<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity): void
    {
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();

    }

    public function remove(User $entity): void
    {
        $this->getEntityManager()->remove($entity);

        $this->getEntityManager()->flush();
    }

    public function findIp(string $ip)
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findBy(['ip_adress' => $ip]);
        if (!$user) {
            return false;
        }
        else {
            return $user[0];
        }
    }

    public function refreshUser(User $user,string $ip_adress, string $city, string $latitude, string $longitude)
    {
        $user->setIpAdress($ip_adress);
        $user->setCity($city);
        $user->setLatitude($latitude);
        $user->setLongitude($longitude);

        $this->getEntityManager()->flush();

    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
