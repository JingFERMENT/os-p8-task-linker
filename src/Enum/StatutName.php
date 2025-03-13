<?php

namespace App\Enum;

enum StatutName: string
{
    case ToDo = 'To do';
    case Doing = 'Doing';
    case Done = 'Done';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::ToDo => 'To do',
            self::Doing => 'Doing',
            self::Done => 'Done',
        };
    }
}