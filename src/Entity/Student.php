<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("student")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("student")
     */
    public $name;

    /**
     * @ORM\ManyToOne(targetEntity=School::class, inversedBy="students")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("student")
     */
    public $school;

    /**
     * @ORM\OneToMany(targetEntity=CommentNote::class, mappedBy="student")
     */
    private $commentNotes;

    public function __construct()
    {
        $this->commentNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): self
    {
        $this->school = $school;

        return $this;
    }

    /**
     * @return Collection<int, CommentNote>
     */
    public function getCommentNotes(): Collection
    {
        return $this->commentNotes;
    }

    public function addCommentNote(CommentNote $commentNote): self
    {
        if (!$this->commentNotes->contains($commentNote)) {
            $this->commentNotes[] = $commentNote;
            $commentNote->setStudent($this);
        }

        return $this;
    }

    public function removeCommentNote(CommentNote $commentNote): self
    {
        if ($this->commentNotes->removeElement($commentNote)) {
            // set the owning side to null (unless already changed)
            if ($commentNote->getStudent() === $this) {
                $commentNote->setStudent(null);
            }
        }

        return $this;
    }
}
