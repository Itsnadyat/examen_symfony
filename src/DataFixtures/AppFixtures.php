<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $articles = [
            ['nom' => 'Ordinateur Portable', 'prix' => 800.0, 'quantiteDisponible' => 50],
            ['nom' => 'Smartphone', 'prix' => 600.0, 'quantiteDisponible' => 100],
            ['nom' => 'Casque Audio', 'prix' => 150.0, 'quantiteDisponible' => 200],
            ['nom' => 'Clavier Mécanique', 'prix' => 80.0, 'quantiteDisponible' => 75],
            ['nom' => 'Souris Gamer', 'prix' => 50.0, 'quantiteDisponible' => 150],
            ['nom' => 'Écran 4K', 'prix' => 400.0, 'quantiteDisponible' => 30],
        ];

        foreach ($articles as $data) {
            $article = new Article();
            $article->setNom($data['nom']);
            $article->setPrix($data['prix']);
            $article->setQuantiteDisponible($data['quantiteDisponible']);
            $manager->persist($article);
        }

        $clients = [
            ['nom' => 'Ndiaye', 'prenom' => 'Fatou', 'telephone' => '778671011', 'adresse' => 'Dakar | Point E | Villa001'],
            ['nom' => 'Fall', 'prenom' => 'Mamadou', 'telephone' => '776545789', 'adresse' => 'Dakar | Yoff | Villa045'],
            ['nom' => 'Diallo', 'prenom' => 'Aminata', 'telephone' => '775678123', 'adresse' => 'Dakar | Ouakam | Villa200'],
            ['nom' => 'Sow', 'prenom' => 'Abdoulaye', 'telephone' => '778999234', 'adresse' => 'Dakar | Mermoz | Villa056'],
            ['nom' => 'Diop', 'prenom' => 'Marie', 'telephone' => '777123678', 'adresse' => 'Dakar | Liberté 6 | Villa120'],
        ];

        foreach ($clients as $data) {
            $client = new Client();
            $client->setNom($data['nom']);
            $client->setPrenom($data['prenom']);
            $client->setTelephone($data['telephone']);
            $client->setAdresse($data['adresse']);
            $manager->persist($client);
        }

        $manager->flush();
    }
}
