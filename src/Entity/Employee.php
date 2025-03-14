<?php

namespace App\Entity;

use App\Enum\ContractName;
use App\Enum\RoleName;
use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?ContractName $contract = null;

    #[ORM\Column(length: 255)]
    private ?RoleName $role = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column]
    private ?bool $isActif = null;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'employees')]
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
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

}
