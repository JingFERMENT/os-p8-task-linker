<?php

namespace App\Security\Voter;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ProjectVoter extends Voter
{
    public const ACCESS_PROJECT = 'ACCESS_PROJECT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return ($attribute === self::ACCESS_PROJECT) 
            && ($subject === null
                || $subject instanceof Project);
        return $result;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $employee = $token->getUser();
       
        // if the user is anonymous, do not grant access
        if (!$employee instanceof Employee) {
            return false;
        }

        // Admins can always view
        if (in_array('ROLE_ADMIN', $employee->getRoles())) {
            return true;
        }
        
        // if the project is active
        if( $subject instanceof Project) {
            return $this->canAccessProject($attribute, $subject, $employee);
        }

       return false;  
    }

    private function canAccessProject(string $attribute, Project $project, Employee $employee): bool
    {
        $attribute === self::ACCESS_PROJECT;
        // Check if the employee is a member of the project
        if ($project->getEmployees()->contains($employee)) {
            return true;
        }

        return false;
    }
}
