<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // title
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre de la tâche est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $title = null;

    // description
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    // deadline
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: 'La date ne peut pas être antérieur à aujourd\'hui.'
    )]
    private ?\DateTimeInterface $deadline = null;

    // employee
    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'task')]
    #[ORM\JoinColumn(name: "employee_id", referencedColumnName: "id", onDelete: "SET NULL")] //  // if the employee is delete, the task will be assigned to "null"
    private ?Employee $employee = null;
    
    // project
    #[ORM\ManyToOne(inversedBy: 'task')]
    private ?Project $project = null;

    /**
     * @var Collection<int, Timeslot>
     */
    #[ORM\OneToMany(targetEntity: Timeslot::class, mappedBy: 'task')]
    private Collection $timeslot;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'tasks')]
    private Collection $tag;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[Assert\NotBlank(message: "Merci de selectionner un statut.")]
    private ?Statut $statut = null;

    public function __construct()
    {
        $this->timeslot = new ArrayCollection();
        $this->tag = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection<int, Timeslot>
     */
    public function getTimeslot(): Collection
    {
        return $this->timeslot;
    }

    public function addTimeslot(Timeslot $timeslot): static
    {
        if (!$this->timeslot->contains($timeslot)) {
            $this->timeslot->add($timeslot);
            $timeslot->setTask($this);
        }

        return $this;
    }

    public function removeTimeslot(Timeslot $timeslot): static
    {
        if ($this->timeslot->removeElement($timeslot)) {
            // set the owning side to null (unless already changed)
            if ($timeslot->getTask() === $this) {
                $timeslot->setTask(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tag->contains($tag)) {
            $this->tag->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tag->removeElement($tag);

        return $this;
    }

    public function getStatut(): ?Statut
    {
        return $this->statut;
    }

    public function setStatut(?Statut $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}
