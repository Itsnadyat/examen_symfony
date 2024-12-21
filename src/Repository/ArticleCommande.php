<?php

namespace App\Repository;

use App\Entity\ArticleCommande;
use App\Repository\ArticleRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends 
 */
class ArticleCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleCommande::class);
    }
    
    public function findCommandeArticles(int $commandeId): array
    {
        return $this->createQueryBuilder('ac')
            ->innerJoin('ac.commande', 'c')
            ->where('c.id = :commandeId')
            ->setParameter('commandeId', $commandeId)
            ->getQuery()
            ->getResult();
    }
}
