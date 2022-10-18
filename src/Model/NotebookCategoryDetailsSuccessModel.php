<?php

namespace App\Model;

/**
 * NotebookCategoryDetailsSuccessModel
 */
class NotebookCategoryDetailsSuccessModel implements ModelInterface
{
    /**
     * @var NotebookNoteModel[]
     */
    private array $notebookNoteModels = [];

    /**
     * @return array
     */
    public function getNotebookNoteModels(): array
    {
        return $this->notebookNoteModels;
    }

    public function addNotebookNoteModel(NotebookNoteModel $notebookNoteModel)
    {
        $this->notebookNoteModels[] = $notebookNoteModel;
    }

    /**
     * @param array $notebookNoteModels
     */
    public function setNotebookNoteModels(array $notebookNoteModels): void
    {
        $this->notebookNoteModels = $notebookNoteModels;
    }
}