<?php

namespace App\Controller;

use App\Enum\StatutName;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjectRepository;
use App\Repository\StatutRepository;
use App\Repository\TaskRepository;

final class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('project/list.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/{id}/', name: 'app_project', requirements: ['id' => '\d+'])]
    public function show(
        ProjectRepository $projectRepository,
        EmployeeRepository $employeeRepository,
        TaskRepository $taskRepository,
        StatutRepository $statutRepository,
        int $id,
    ): Response {

        // fetch the project 
        $project = $projectRepository->find($id);

        // if no project correspond, redirect to the home page
        if (!$project) {
            return $this->redirectToRoute('app_homepage');
        }

        // fetch the employees 
        $employees = $employeeRepository->findEmployeesByProject($id);

        // fetch the task statuts 
        $statutToDo = $statutRepository->findOneBy(['statutName' => StatutName::ToDo->value]);
        $statutDoing = $statutRepository->findOneBy(['statutName' => StatutName::Doing->value]);
        $statutDone = $statutRepository->findOneBy(['statutName' => StatutName::Done->value]);

        // Fetching Tasks by Status
        $toDoTasks = $taskRepository->findByProjectAndStatut($project, $statutToDo);
        $doingTasks = $taskRepository->findByProjectAndStatut($project, $statutDoing);
        $doneTasks = $taskRepository->findByProjectAndStatut($project, $statutDone);

        // Grouping tasks by status
        $tasksByStatus = [
            'To do' => $toDoTasks,
            'Doing' => $doingTasks,
            'Done' => $doneTasks
        ];

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'tasksToDo' => $tasksByStatus['To do'],
            'tasksDoing' => $tasksByStatus['Doing'],
            'tasksDone' => $tasksByStatus['Done'],
            'employees' => $employees,
        ]);
    }
}
