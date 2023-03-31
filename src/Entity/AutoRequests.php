<?php

namespace App\Entity;

use App\Repository\AutoRequestsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AutoRequestsRepository::class)]
class AutoRequests
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private array $header = [];

    #[ORM\Column(nullable: true)]
    private array $body = [];

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function setHeader(?array $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function setBody(?array $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
