<?php

namespace App\Entity;

use App\Repository\CommentNoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentNoteRepository::class)
 */
class CommentNote
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("commentByStudent")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups("commentByStudent")
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("commentByStudent")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=Student::class, inversedBy="commentNotes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentNotes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("commentByStudent")
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
