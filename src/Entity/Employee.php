<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Enum\ContractName;
use App\Enum\RoleName;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Employee implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private ?array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    
    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // Firstname 
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: "Le prénom est obligatoire."
    )]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le prénom doit avoir au moins {{ limit }} caractères.",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères. "
    )]
    // #[Assert\Regex(
    //     pattern: "/^[A-Z][a-zA-Z'-]+$/",
    //     message: "Le prénom ne doit contenir que des lettres."
    // )]
    
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit avoir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères. "
    )]
    #[Assert\Regex(
        pattern: "/^[A-Z][a-zA-Z'-]+$/",
        message: "Le nom ne doit contenir que des lettres et commence par un majuscule."
    )]
    private ?string $lastname = null;

    // Email 
    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide.")]
    #[Assert\Email(
        message: "Votre email '{{ value }}' est invalide",
        mode: "strict"
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'email ne peut pas dépasser {{ limit }} caractères. "
    )]
    private ?string $email = null;

    // StartDate
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\LessThanOrEqual(
        value: 'today',
        message: 'La date d\'entrée ne peut pas être postérieure à aujourd\'hui.'
    )]
    private ?\DateTimeInterface $startDate = null;

    // Contract Statut
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: [ContractName::PermanentContract, ContractName::FixedTermContract, ContractName::Freelancer],
        message: 'Merci de sélectionner un statut valide.'
    )]
    private ?ContractName $contract = null;


    #[ORM\Column(length: 255)]
    private ?RoleName $role = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isActif = null;
    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'employees')]
    #[ORM\JoinTable(name: 'employee_project')]
    private Collection $project;

    /**
     * @var Collection<int, timeslot>
     */
    #[ORM\OneToMany(targetEntity: Timeslot::class, mappedBy: 'employee')]
    private Collection $timeslot;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'employee')]
    private Collection $task;

    public function __construct()
    {
        $this->project = new ArrayCollection();
        $this->timeslot = new ArrayCollection();
        $this->task = new ArrayCollection();
    }

    

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getContract(): ?ContractName
    {
        return $this->contract;
    }

    public function setContract(ContractName $contract): static
    {
        $this->contract = $contract;

        return $this;
    }

    public function getRole(): ?RoleName
    {
        return $this->role;
    }

    public function setRole(RoleName $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): static
    {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProject(): Collection
    {
        return $this->project;
    }

    public function addProject(Project $project): static
    {
        if (!$this->project->contains($project)) {
            $this->project->add($project);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        $this->project->removeElement($project);

        return $this;
    }

    /**
     * @return Collection<int, timeslot>
     */
    public function getTimeslot(): Collection
    {
        return $this->timeslot;
    }

    public function addTimeslot(timeslot $timeslot): static
    {
        if (!$this->timeslot->contains($timeslot)) {
            $this->timeslot->add($timeslot);
            $timeslot->setEmployee($this);
        }

        return $this;
    }

    public function removeTimeslot(timeslot $timeslot): static
    {
        if ($this->timeslot->removeElement($timeslot)) {
            // set the owning side to null (unless already changed)
            if ($timeslot->getEmployee() === $this) {
                $timeslot->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTask(): Collection
    {
        return $this->task;
    }

    public function addTask(Task $task): static
    {
        if (!$this->task->contains($task)) {
            $this->task->add($task);
            $task->setEmployee($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->task->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getEmployee() === $this) {
                $task->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * function to get the intials of the employee
     * @return string
     */
    public function getInitials(): string
    {
        return strtoupper($this->firstname[0] . $this->lastname[0]);
    }
}
