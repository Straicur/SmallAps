<?php

namespace App\Entity;

use App\Repository\RegisterCodeRepository;
use App\ValueGenerator\ValueGeneratorInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RegisterCodeRepository::class)]
class RegisterCode
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 9)]
    private string $code;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $dateAdd;

    #[ORM\Column(type: 'datetime',nullable: true)]
    private ?\DateTime $dateAccept;

    #[ORM\Column(type: 'boolean')]
    private bool $used;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /**
     * @param User $user
     * @param ValueGeneratorInterface $code
     */
    public function __construct(User $user, ValueGeneratorInterface $code)
    {
        $this->user = $user;
        $this->code = $code->generate();
        $this->dateAdd = new \DateTime("Now");
        $this->used = false;
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(ValueGeneratorInterface $code): self
    {
        $this->code = $code->generate();

        return $this;
    }

    public function getDateAdd(): \DateTime
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTime $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getDateAccept(): ?\DateTime
    {
        return $this->dateAccept;
    }

    public function setDateAccept(\DateTime $dateAccept): self
    {
        $this->dateAccept = $dateAccept;

        return $this;
    }

    public function getUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): self
    {
        $this->used = $used;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
