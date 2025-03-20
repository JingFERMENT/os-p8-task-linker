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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('project/{id}/task/add', name: 'app_task_add')]
    public function taskAdd(
        int $id,
        Request $request,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $project = $projectRepository->find($id);

        if (!$project) {
            return $this->redirectToRoute('app_homepage');
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

    #[Route('project/{id}/task/{taskId}/edit', name: 'app_task_edit')]
    public function taskEdit(
        int $id,
        int $taskId,
        Request $request,
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager
    ): Response {
        
        $project = $projectRepository->find($id);
        if (!$project) {
            return $this->redirectToRoute('app_homepage');
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

    #[Route('project/{id}/task/{taskId}/delete', name: 'app_task_delete')]
    public function taskDelete(
        int $id,
        int $taskId,
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager
    ): Response {
            
            $project = $projectRepository->find($id);
            if (!$project) {
                return $this->redirectToRoute('app_homepage');
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
