<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Enum\StatutName;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    // #[Route('/task', name: 'app_project_tasks')]
    // public function tasksByProjectAndStatut(
    //     int $id,
    //     Statut $statutName,
    //     TaskRepository $taskRepository,
    //     EntityManagerInterface $entityManager
    // ): Response {
    //     $project = $entityManager->find('App\Entity\Project', $id);
    //     $statut = $entityManager->getRepository('App\Entity\Statut')->findOneBy(['statutName' => StatutName::ToDo]);

        

    //     $tasks = $taskRepository->findByProjectAndStatus($project, $statut);

  

    //     return $this->render('task/list.html.twig', [
    //         'project' => $project,
    //         'statut' => $statut,
    //         'tasks' => $tasks,
    //     ]);
    // }
}
