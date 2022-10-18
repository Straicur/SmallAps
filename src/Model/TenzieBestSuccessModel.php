<?php

namespace App\Model;

/**
 * TenzieAllSuccessModel
 *
 */
class TenzieBestSuccessModel implements ModelInterface
{
    /**
     * @var TenzieBestModel[]
     */
    private array $tenzieBestModels = [];

    /**
     * @return array
     */
    public function getTenzieBestModels(): array
    {
        return $this->tenzieBestModels;
    }

    public function addTenzieBestModel(TenzieBestModel $tenzieBestModel)
    {
        $this->tenzieBestModels[] = $tenzieBestModel;
    }

    /**
     * @param array $tenzieBestModels
     */
    public function setTenzieBestModels(array $tenzieBestModels): void
    {
        $this->tenzieBestModels = $tenzieBestModels;
    }

}