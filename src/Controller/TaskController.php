<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Enum\StatutName;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\StatutRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// This attribute ensures that the user is authenticated before accessing any route in this controller.
final class TaskController extends AbstractController
{  

    #[Route('projects/{id}/task/add', name: 'app_task_add')]
    public function taskAdd(
        int $id,
        Request $request,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        $project = $projectRepository->find($id);

        if (!$project) {
            return $this->redirectToRoute('app_homepage');
        }

        // Get the currently authenticated user
        $employee = $security->getUser();

        if ($security->isGranted('ROLE_ADMIN')) {
            
        } elseif ($security->isGranted('ROLE_USER')) {

            //if it's a user, check if the employee is associated with the project
            if (!$project->getEmployees()->contains($employee)) {
                return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
            }
        } else {
            // else redirect if employee is not associated with the project
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }

        $task = new Task();
        $task->setProject($project);
        
        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){ 
            $entityManager->persist($task); 
            $entityManager->flush(); 
            return $this->redirectToRoute('app_project', ['id' => $task->getProject()->getId()]);
        }
            return $this->render('task/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('projects/{id}/task/{taskId}/edit', name: 'app_task_edit')]
    public function taskEdit(
        int $id,
        int $taskId,
        Request $request,
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        
       

        $project = $projectRepository->find($id);
        if (!$project) {
            return $this->redirectToRoute('app_homepage');
        }

         // Get the currently authenticated user
        $employee = $security->getUser();

        if ($security->isGranted('ROLE_ADMIN')) {
            
        } elseif ($security->isGranted('ROLE_USER')) {

            //if it's a user, check if the employee is associated with the project
            if (!$project->getEmployees()->contains($employee)) {
                return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
            }
        } else {
            // else redirect if employee is not associated with the project
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }

        $task = $taskRepository->find($taskId);

        if (!$task) {
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }
        
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush(); 
            return $this->redirectToRoute('app_project', ['id' => $task->getProject()->getId()]);
        }
            return $this->render('task/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('projects/{id}/task/{taskId}/delete', name: 'app_task_delete')]
    public function taskDelete(
        int $id,
        int $taskId,
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
            
            $project = $projectRepository->find($id);
            if (!$project) {
                return $this->redirectToRoute('app_homepage');
            }

              // Get the currently authenticated user
        $employee = $security->getUser();

        if ($security->isGranted('ROLE_ADMIN')) {
            
        } elseif ($security->isGranted('ROLE_USER')) {

            //if it's a user, check if the employee is associated with the project
            if (!$project->getEmployees()->contains($employee)) {
                return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
            }
        } else {
            // else redirect if employee is not associated with the project
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }
    
            $task = $taskRepository->find($taskId);
    
            if (!$task) {
                return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
            }
            
            $entityManager->remove($task); 
            $entityManager->flush();
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
    }  

}
