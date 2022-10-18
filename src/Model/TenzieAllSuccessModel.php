<?php

namespace App\Model;

/**
 * TenzieAllSuccessModel
 *
 */
class TenzieAllSuccessModel implements ModelInterface
{
    /**
     * @var TenzieAllModel[]
     */
    private array $tenzieAllModels = [];
    private int $page;
    private int $limit;
    private int $maxPage;

    /**
     * @return array
     */
    public function getTenzieAllModels(): array
    {
        return $this->tenzieAllModels;
    }

    public function addTenzieAllModel(TenzieAllModel $tenzieAllModel)
    {
        $this->tenzieAllModels[] = $tenzieAllModel;
    }

    /**
     * @param array $tenzieAllModels
     */
    public function setTenzieAllModels(array $tenzieAllModels): void
    {
        $this->tenzieAllModels = $tenzieAllModels;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getMaxPage(): int
    {
        return $this->maxPage;
    }

    /**
     * @param int $maxPage
     */
    public function setMaxPage(int $maxPage): void
    {
        $this->maxPage = $maxPage;
    }

}