<?php

namespace App\Model;

/**
 * NotebookNoteAddSuccessModel
 */
class NotebookNoteAddSuccessModel implements ModelInterface
{
    private string $id;
    private string $title;
    private string $text;
    private string $dateAdd;
    private ?string $dateEdit = null;
    private string $categoryId;

    /**
     * @param string $id
     * @param string $title
     * @param string $text
     * @param \DateTime $dateAdd
     * @param string $categoryId
     */
    public function __construct(string $id, string $title, string $text, \DateTime $dateAdd, string $categoryId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
        $this->dateAdd = $dateAdd->getTimestamp();
        $this->categoryId = $categoryId;
    }

    /**
     * @return string
     */
    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * @param string $categoryId
     */
    public function setCategoryId(string $categoryId): void
    {
        $this->categoryId = $categoryId;
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
     * @return string
     */
    public function getDateEdit(): string
    {
        return $this->dateEdit;
    }

    /**
     * @param \DateTime $dateEdit
     */
    public function setDateEdit(\DateTime $dateEdit): void
    {
        $this->dateEdit = $dateEdit->getTimestamp();
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