<?php

namespace App\Model;

/**
 * NotebookCategoriesSuccessModel
 *
 */
class NotebookCategoriesSuccessModel implements ModelInterface
{
    /**
     * @var NotebookCategoryModel[]
     */
    private array $notebookCategoryModels = [];

    /**
     * @return array
     */
    public function getNotebookCategoryModels(): array
    {
        return $this->notebookCategoryModels;
    }

    public function addNotebookCategoryModel(NotebookCategoryModel $notebookCategoryModel)
    {
        $this->notebookCategoryModels[] = $notebookCategoryModel;
    }

    /**
     * @param array $notebookCategoryModels
     */
    public function setNotebookCategoryModels(array $notebookCategoryModels): void
    {
        $this->notebookCategoryModels = $notebookCategoryModels;
    }
}