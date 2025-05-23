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

        // if the user is not an admin, check if they are a member of the project

        // if the project is active
        if ($subject instanceof Project && $attribute === self::ACCESS_PROJECT) {
            return $this->canAccessProject($attribute, $subject, $employee);
        }

        return false;
    }

    private function canAccessProject(string $attribute, Project $project, Employee $employee): bool
    {
        
        $tasks = $project->getTasks();

        foreach ($tasks as $task) {
            $assignedEmployees = $task->getEmployee();

            if ($assignedEmployees !== null && ($assignedEmployees->getId() === $employee->getId())) {
                return true;
            }
        }


        // Check if the employee is assigned to project
        if ($project->getEmployees()->contains($employee)) {
            return true;
        }

        return false;
    }
}
