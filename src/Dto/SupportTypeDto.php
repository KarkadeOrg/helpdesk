<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class SupportTypeDto {
    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public string $topic;

    #[Assert\NotBlank]
    public string $message;
}
