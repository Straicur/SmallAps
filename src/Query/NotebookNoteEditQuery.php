<?php

namespace App\Query;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NotebookNoteEditQuery
 */
class NotebookNoteEditQuery
{
    #[Assert\NotNull(message: "Title is null")]
    #[Assert\NotBlank(message: "Title is empty")]
    #[Assert\Type(type: "string")]
    private string $title;

    #[Assert\NotNull(message: "Text is null")]
    #[Assert\NotBlank(message: "Text is empty")]
    #[Assert\Type(type: "string")]
    private string $text;

    #[Assert\NotNull(message: "Note id is null")]
    #[Assert\NotBlank(message: "Note id is empty")]
    #[Assert\Uuid]
    private Uuid $noteId;

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
    public function getNoteId(): Uuid
    {
        return $this->noteId;
    }

    /**
     * @param string $noteId
     */
    public function setNoteId(string $noteId): void
    {
        $this->noteId = Uuid::fromString($noteId);
    }
}