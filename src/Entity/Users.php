<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getUserData', 'automationNestedData'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Groups(['getUserData', 'automationNestedData'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['getUserData', 'automationNestedData'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Automations::class, orphanRemoval: true)]
    private Collection $automations;

    public function __construct()
    {
        $this->automations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Automations>
     */
    public function getAutomations(): Collection
    {
        return $this->automations;
    }

    public function addAutomation(Automations $automation): self
    {
        if (!$this->automations->contains($automation)) {
            $this->automations->add($automation);
            $automation->setUserId($this);
        }

        return $this;
    }

    public function removeAutomation(Automations $automation): self
    {
        if ($this->automations->removeElement($automation)) {
            // set the owning side to null (unless already changed)
            if ($automation->getUserId() === $this) {
                $automation->setUserId(null);
            }
        }

        return $this;
    }
}
