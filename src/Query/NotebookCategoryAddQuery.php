<?php

namespace App\Query;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * NotebookCategoryAddQuery
 */
class NotebookCategoryAddQuery
{
    #[Assert\NotNull(message: "Name is null")]
    #[Assert\NotBlank(message: "Name is empty")]
    #[Assert\Type(type: "string")]
    private string $name;

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