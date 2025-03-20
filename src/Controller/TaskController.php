<?php

namespace App\Controller;

<<<<<<< HEAD
use App\Entity\Project;
use App\Entity\Task;
use App\Enum\StatutName;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\StatutRepository;
use App\Repository\TaskRepository;
=======
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
>>>>>>> 2d46599397054f3da280fe39ea3a8d7fa9da37d5
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

<<<<<<< HEAD
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
=======
        $task = new Task(); // // Create a new instance of the Task entity
        $task->setProject($project); // Link it to the project
        // Create an instance of our form filled with the data passed as the second parameter.
        $form = $this->createForm(TaskType::class, $task);
        
        // Handle the form data in the request
        $form->handleRequest($request);

        //Check if the form has been submitted and is validated
        if($form->isSubmitted() && $form->isValid()){ // use the constraints defined in the Task entity
            $entityManager->persist($task); // add the vehicle
            $entityManager->flush(); // save the vehicle in the database
            // $this->addFlash('success', 'La nouvelle tâche a bien été ajoutée !');
>>>>>>> 2d46599397054f3da280fe39ea3a8d7fa9da37d5
            return $this->redirectToRoute('app_project', ['id' => $task->getProject()->getId()]);
        }
            return $this->render('task/add.html.twig', [
            'form' => $form
        ]);
    }
<<<<<<< HEAD

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


=======
>>>>>>> 2d46599397054f3da280fe39ea3a8d7fa9da37d5
}
