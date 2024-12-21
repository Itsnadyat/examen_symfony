<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\ArticleCommande;
use App\Form\ClientSearchType;
use App\Form\ArticleCommandeType;
use App\Repository\ArticleRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class CommandeController extends AbstractController
{
  
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

#[Route('/commande/ajouter', name: 'ajouter_commande')]
public function ajouterCommande(Request $request, EntityManagerInterface $em, ArticleRepository $articleRepository): Response
{
    $articles = $articleRepository->findAll();
    $clients = $em->getRepository(Client::class)->findAll();

    $client = null;
    $commande = new Commande();
    $commande->setDate(new \DateTime()); 

    $clientSearchForm = $this->createForm(ClientSearchType::class);
    $clientSearchForm->handleRequest($request);

    if ($clientSearchForm->isSubmitted() && $clientSearchForm->isValid()) {
        $data = $clientSearchForm->getData();
        $client = $em->getRepository(Client::class)->findOneBy(['telephone' => $data['telephone']]);
        
        if ($client) {
            $commande->setClient($client); 
        } else {
            $this->addFlash('error', 'Aucun client trouvé.');
        }
    }

    $articleCommande = new ArticleCommande();
    $articleForm = $this->createForm(ArticleCommandeType::class, $articleCommande, [
        'article_repository' => $articleRepository,
    ]);
    $articleForm->handleRequest($request);

    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $commande->addArticle($articleCommande);  
        $articleCommande->setCommande($commande); 
    }

    if ($request->isMethod('POST') && $client && count($commande->getArticles()) > 0) {
        $em->persist($commande);  
        $em->flush(); 

        $this->addFlash('success', 'Commande enregistrée avec succès.');

        return $this->redirectToRoute('commande_liste'); 
    }

    return $this->render('commande/ajouter.html.twig', [
        'clients' => $clients,
        'client' => $client,
        'articles' => $articles,
        'clientSearchForm' => $clientSearchForm->createView(),
        'articleForm' => $articleForm->createView(),
        'commande' => $commande,
    ]);
}

#[Route('/commande/valider', name: 'valider_commande', methods: ['POST'])]
public function validerCommande(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!$data) {
        $logger->error('Requête invalide : aucune donnée reçue');
        return new JsonResponse(['message' => 'Requête invalide'], 400);
    }

    $logger->info('Données reçues', $data);

    $clientId = $data['client_id'] ?? null;
    $articles = $data['articles'] ?? [];

    if (!$clientId || empty($articles)) {
        $logger->error('Client ou articles manquants', [
            'client_id' => $clientId,
            'articles' => $articles
        ]);
        return new JsonResponse(['message' => 'Client ou articles manquants.'], 400);
    }

    $client = $entityManager->getRepository(Client::class)->find($clientId);
    if (!$client) {
        $logger->error("Client introuvable : ID {$clientId}");
        return new JsonResponse(['message' => 'Client non trouvé.'], 404);
    }

    $commande = new Commande();
    $commande->setClient($client);
    $commande->setDate(new \DateTime());

    foreach ($articles as $articleData) {
        $article = $entityManager->getRepository(Article::class)->find($articleData['article_id']);
        if (!$article) {
            $logger->error('Article introuvable', ['article_id' => $articleData['article_id']]);
            return new JsonResponse(['message' => 'Article non trouvé.'], 404);
        }

        $quantite = $articleData['quantite'] ?? 0;
        if ($quantite <= 0 || $quantite > $article->getQuantiteDisponible()) {
            $logger->error('Quantité invalide ou stock insuffisant', [
                'article_id' => $article->getId(),
                'quantite' => $quantite,
                'stock_disponible' => $article->getQuantiteDisponible()
            ]);
            return new JsonResponse(['message' => 'Stock insuffisant pour l\'article ' . $article->getNom()], 400);
        }

        $articleCommande = new ArticleCommande();
        $articleCommande->setCommande($commande)
                        ->setArticle($article)
                        ->setQuantite($quantite);

        $article->setQuantiteDisponible($article->getQuantiteDisponible() - $quantite);

        $commande->addArticle($articleCommande);
    }

    $entityManager->persist($commande);
    $entityManager->flush();

    $logger->info('Commande validée avec succès', [
        'commande_id' => $commande->getId()
    ]);

    return new JsonResponse(['message' => 'Commande validée avec succès.'], 200);
}
}
