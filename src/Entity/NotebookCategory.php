<?php

namespace App\Entity;

use App\Repository\NotebookCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NotebookCategoryRepository::class)]
class NotebookCategory
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: User::class,inversedBy: 'notebookCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: NotebookNote::class, cascade: ['persist', 'remove'])]
    private Collection $notebookNotes;

    public function __construct(string $name,User $user)
    {
        $this->name = $name;
        $this->user = $user;
        $this->notebookNotes = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, NotebookNote>
     */
    public function getNotebookNotes(): Collection
    {
        return $this->notebookNotes;
    }

    public function addNotebookNote(NotebookNote $notebookNote): self
    {
        if (!$this->notebookNotes->contains($notebookNote)) {
            $this->notebookNotes->add($notebookNote);
            $notebookNote->setCategory($this);
        }

        return $this;
    }

    public function removeNotebookNote(NotebookNote $notebookNote): self
    {
        if ($this->notebookNotes->removeElement($notebookNote)) {
            // set the owning side to null (unless already changed)
            if ($notebookNote->getCategory() === $this) {
                $notebookNote->setCategory(null);
            }
        }

        return $this;
    }
}
