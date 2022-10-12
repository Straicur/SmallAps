<?php

namespace App\Model;

/**
 * TenzieAllSuccessModel
 *
 */
class TenzieBestModel
{
    /**
     * @var TenzieAllModel[]
     */
    private array $tenzieAllModels = [];

    private int $level;

    /**
     * @param int $level
     */
    public function __construct(int $level)
    {
        $this->level = $level;
    }

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
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }
}