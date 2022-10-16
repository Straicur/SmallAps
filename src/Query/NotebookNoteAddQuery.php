<?php

namespace App\Query;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NotebookNoteAddQuery
 */
class NotebookNoteAddQuery
{
    #[Assert\NotNull(message: "Title is null")]
    #[Assert\NotBlank(message: "Title is empty")]
    #[Assert\Type(type: "string")]
    private string $title;

    #[Assert\NotNull(message: "Text is null")]
    #[Assert\NotBlank(message: "Text is empty")]
    #[Assert\Type(type: "string")]
    private string $text;

    #[Assert\NotNull(message: "NotebookCategory id is null")]
    #[Assert\NotBlank(message: "NotebookCategory id is empty")]
    #[Assert\Uuid]
    private Uuid $notebookCategoryId;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return Uuid
     */
    #[OA\Property(type: "string", example: "60266c4e-16e6-1ecc-9890-a7e8b0073d3b")]
    public function getNotebookCategoryId(): Uuid
    {
        return $this->notebookCategoryId;
    }

    /**
     * @param string $notebookCategoryId
     */
    public function setNotebookCategoryId(string $notebookCategoryId): void
    {
        $this->notebookCategoryId = Uuid::fromString($notebookCategoryId);
    }
}