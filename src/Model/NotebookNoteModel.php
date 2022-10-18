<?php

namespace App\Model;

/**
 * NotebookNoteModel
 *
 */
class NotebookNoteModel
{
    private string $id;
    private string $title;
    private string $dateAdd;

    /**
     * @param string $id
     * @param string $title
     * @param \DateTime $dateAdd
     */
    public function __construct(string $id, string $title, \DateTime $dateAdd)
    {
        $this->id = $id;
        $this->title = $title;
        $this->dateAdd = $dateAdd->getTimestamp();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

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
    public function getDateAdd(): string
    {
        return $this->dateAdd;
    }

    /**
     * @param \DateTime $dateAdd
     */
    public function setDateAdd(\DateTime $dateAdd): void
    {
        $this->dateAdd = $dateAdd->getTimestamp();
    }
}