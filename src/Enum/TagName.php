<?php

namespace App\Enum;

enum TagName: string
{
    case Frontend = 'Frontend';
    case Backend = 'Backend';
    case Design = 'Design';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::Frontend => 'Frontend',
            self::Backend => 'Backend',
            self::Design => 'Design',
        };
    }
}