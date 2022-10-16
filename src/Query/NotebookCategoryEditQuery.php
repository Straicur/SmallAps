<?php

namespace App\Query;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NotebookCategoryEditQuery
 */
class NotebookCategoryEditQuery
{
    #[Assert\NotNull(message: "NotebookCategory id is null")]
    #[Assert\NotBlank(message: "NotebookCategory id is empty")]
    #[Assert\Uuid]
    private Uuid $notebookCategoryId;

    #[Assert\NotNull(message: "Name is null")]
    #[Assert\NotBlank(message: "Name is empty")]
    #[Assert\Type(type: "string")]
    private string $name;

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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

}