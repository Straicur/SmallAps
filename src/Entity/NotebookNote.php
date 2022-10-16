<?php

namespace App\Entity;

use App\Repository\NotebookNoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NotebookNoteRepository::class)]
class NotebookNote
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: NotebookCategory::class, inversedBy: 'notebookNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private NotebookCategory $category;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $dateAdd;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $dateEdit = null;

    /**
     * @param NotebookCategory $category
     * @param string $title
     * @param string $text
     */
    public function __construct(NotebookCategory $category, string $title, string $text)
    {
        $this->category = $category;
        $this->title = $title;
        $this->text = $text;
        $this->dateAdd = new \DateTime('Now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCategory(): NotebookCategory
    {
        return $this->category;
    }

    public function setCategory(NotebookCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDateAdd(): \DateTime
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTime $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getDateEdit(): \DateTime
    {
        return $this->dateEdit;
    }

    public function setDateEdit(?\DateTime $dateEdit): self
    {
        $this->dateEdit = $dateEdit;

        return $this;
    }
}
