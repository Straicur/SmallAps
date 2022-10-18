<?php

namespace App\Query;

use App\Controller\OfferController;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Offer TenzieBestQuery Query
 *
 */
class TenzieBestQuery
{
    protected array $levels = [];

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('levels', new Assert\Collection([
        'level' => new Assert\Required([
            new Assert\NotBlank(message: "Level is empty"),
            new Assert\All(constraints: [
                new Assert\NotBlank(),
                new Assert\Type('integer')
            ])
        ]),
        ],));
    }
    #[OA\Property(property: "levels", properties: [
        new OA\Property(property: "level", type: "array", nullable: true, attachables: [
            new OA\Items(type: "integer", example: 1)
        ]),
    ], type: "object")]
    public function setLevels(array $levels): void
    {
        $this->levels = $levels;
    }

    /**
     * @return string[]
     */
    public function getLevels(): array
    {
        return $this->levels;
    }
}