<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findByTelephone(string $telephone): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.telephone = :telephone')
            ->setParameter('telephone', $telephone)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
