<?php

namespace App\Entity;

use App\Repository\AutomationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AutomationsRepository::class)]
class Automations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['automationData', 'automationNestedData'])]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'automations', targetEntity: AutoRequests::class)]
    #[Groups(['automationNestedData'])]
    private Collection $autorequest_id;

    #[ORM\Column(length: 255)]
    #[Groups(['automationData', 'automationNestedData'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['automationData', 'automationNestedData'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['automationData', 'automationNestedData'])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'automations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['automationNestedData'])]
    private ?Users $user_id = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['automationData', 'automationNestedData'])]
    private ?string $alert_user_method = null;

    #[ORM\Column(length: 255)]
    #[Groups(['automationData', 'automationNestedData'])]
    private ?string $cron_task = null;

    public function __construct()
    {
        $this->autorequest_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, AutoRequests>
     */
    public function getAutorequestId(): Collection
    {
        return $this->autorequest_id;
    }

    public function addAutorequestId(AutoRequests $autorequestId): self
    {
        if (!$this->autorequest_id->contains($autorequestId)) {
            $this->autorequest_id->add($autorequestId);
            $autorequestId->setAutomations($this);
        }

        return $this;
    }

    public function removeAutorequestId(AutoRequests $autorequestId): self
    {
        if ($this->autorequest_id->removeElement($autorequestId)) {
            // set the owning side to null (unless already changed)
            if ($autorequestId->getAutomations() === $this) {
                $autorequestId->setAutomations(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUserId(): ?Users
    {
        return $this->user_id;
    }

    public function setUserId(?Users $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getAlertUserMethod(): ?string
    {
        return $this->alert_user_method;
    }

    public function setAlertUserMethod(?string $alert_user_method): self
    {
        $this->alert_user_method = $alert_user_method;

        return $this;
    }

    public function getCronTask(): ?string
    {
        return $this->cron_task;
    }

    public function setCronTask(string $cron_task): self
    {
        $this->cron_task = $cron_task;

        return $this;
    }
}
