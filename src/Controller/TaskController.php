<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
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
            return $this->redirectToRoute('app_project', ['id' => $task->getProject()->getId()]);
        }
            return $this->render('task/add.html.twig', [
            'form' => $form
        ]);
    }
}
