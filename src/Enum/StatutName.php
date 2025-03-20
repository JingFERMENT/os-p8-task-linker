<?php

namespace App\Enum;

enum StatutName: string
{
    case ToDo = 'To Do';
    case Doing = 'Doing';
    case Done = 'Done';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::ToDo => 'To Do',
            self::Doing => 'Doing',
            self::Done => 'Done',
        };
    }
}