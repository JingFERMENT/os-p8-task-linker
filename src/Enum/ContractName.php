<?php

namespace App\Enum;

enum ContractName: string
{
    case PermanentContract = 'CDI';
    case FixedTermContract = 'CDD';
    case Freelancer = 'Freelance';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::PermanentContract => 'CDI',
            self::FixedTermContract => 'CDD',
            self::Freelancer => 'Freelance',
        };
    }
}