<?php

namespace App\Query;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * RegisterConfirmQuery
 */
class RegisterConfirmQuery
{
    #[Assert\NotNull(message: "RegisterCode is null")]
    #[Assert\NotBlank(message: "RegisterCode is empty")]
    #[Assert\Type(type: "string")]
    private string $registerCode;

    /**
     * @return string
     */
    public function getRegisterCode(): string
    {
        return $this->registerCode;
    }

    /**
     * @param string $registerCode
     */
    public function setRegisterCode(string $registerCode): void
    {
        $this->registerCode = $registerCode;
    }
}