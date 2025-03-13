<?php

namespace App\Enum;

enum RoleName: string
{
    case Collaborator = 'Collaborateur';
    case ProjectManager = 'Chef de projet';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::Collaborator => 'Collaborateur',
            self::ProjectManager => 'Chef de projet',
        };
    }
}