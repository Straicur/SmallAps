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
    #[Assert\Email(message: "It's not an email")]
    private string $registerCode;

    #[Assert\NotNull(message: "UserId is null")]
    #[Assert\NotBlank(message: "UserId is empty")]
    #[Assert\Uuid(message: "It's not an Uuid")]
    private string $userId;

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

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

}