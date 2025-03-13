<?php

namespace App\Enum;

enum ContractName: string
{
    case PermanentContractP = 'CDI';
    case FixedTermContract = 'CDD';
    case Freelancer = 'Freelance';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::PermanentContractP => 'CDI',
            self::FixedTermContract => 'CDD',
            self::Freelancer => 'Freelance',
        };
    }
}