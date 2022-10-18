<?php

namespace App\Query;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NotebookNoteDetailsQuery
 */
class NotebookNoteDetailsQuery
{
    #[Assert\NotNull(message: "Note id is null")]
    #[Assert\NotBlank(message: "Note id is empty")]
    #[Assert\Uuid]
    private Uuid $noteId;
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