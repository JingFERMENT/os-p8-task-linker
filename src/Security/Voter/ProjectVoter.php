<?php

namespace App\Security\Voter;

use App\Entity\Employee;
use App\Entity\Project;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProjectVoter extends Voter
{
    public const VIEW = 'PROJECT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        $result = ($attribute === self::VIEW
            && ($subject instanceof Project));

        return $result;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $employee = $token->getUser();
       
        // if the user is anonymous, do not grant access
        if (!$employee instanceof Employee) {
            return false;
        }

       /** @var Project $project */
        $project = $subject;

        // Admins can always view
        if (in_array('ROLE_ADMIN', $employee->getRoles())) {
            return true;
        }

        // If the project is null, this is the list of all projects
        // so the user can view it
        if (null === $project) {
            return true;
        }

        foreach ($project->getEmployees() as $assignedEmployee) {
          
            if( $assignedEmployee->getId()=== $employee->getId()) {
                return true;
            } 
        }

       return false;
        

        
    }
}
