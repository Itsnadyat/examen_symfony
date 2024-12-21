<?php

namespace App\Service;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;

class CommandeService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function creerCommande(array $articles, $client): Commande
    {
        $commande = new Commande();
        $commande->setClient($client);
        $commande->setDate(new \DateTime());

        foreach ($articles as $articleData) {
            $article = $articleData['article'];
            $quantite = $articleData['quantite'];
            $prix = $articleData['prix'];

            if ($article->getQuantiteDisponible() < $quantite) {
                throw new \Exception('Quantité insuffisante pour ' . $article->getNom());
            }

            $article->setQuantiteDisponible($article->getQuantiteDisponible() - $quantite);

            $articleCommande = new ArticleCommande();
            $articleCommande->setArticle($article);
            $articleCommande->setQuantite($quantite);
            $articleCommande->setPrix($prix);
            $articleCommande->setCommande($commande);

            $this->em->persist($articleCommande);
        }

        $this->em->persist($commande);
        $this->em->flush();

        return $commande;
    }
}
