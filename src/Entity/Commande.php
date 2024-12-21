<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection; 

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    private Client $client;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $date;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: \App\Entity\ArticleCommande::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(ArticleCommande $articleCommande): self
    {
        if (!$this->articles->contains($articleCommande)) {
            $this->articles->add($articleCommande);
        }
        return $this;
    }

    public function removeArticle(ArticleCommande $articleCommande): self
    {
        $this->articles->removeElement($articleCommande);
        return $this;
    }
}
