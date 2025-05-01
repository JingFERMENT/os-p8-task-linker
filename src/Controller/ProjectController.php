<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enum\StatutName;
use App\Form\ProjectType;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjectRepository;
use App\Repository\StatutRepository;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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

        $project = $projectRepository->find($id);

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

    #[Route('/project/add', name: 'app_project_add')]
    public function projectAdd(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project = $form->getData(); //Retrieves Project entity from form.

            foreach (
                $project->getEmployees() // Loops through selected employees.
                as $employee
            ) {
                $employee->addProject($project);
            } //Updates the Employee entity to ensure bidirectional ManyToMany sync.

            $entityManager->persist($project);
            $entityManager->flush();
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('project/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/project/{id}/edit', name: 'app_project_edit')]

    public function projectEdit(
        int $id,
        Request $request,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager
    ): Response {

        $project = $projectRepository->find($id);
        if (!$project) {
            return $this->redirectToRoute('app_homepage');
        }

        // Take a snapshot of employees BEFORE handling the form submission
        $existingEmployees = new ArrayCollection($project->getEmployees()->toArray());

        // Create and handle the form
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project = $form->getData(); //Retrieves Project entity from form.

            foreach (
                $project->getEmployees() // newly updated list of employees after the form submission.
                as $employee
            ) {
                if (!$existingEmployees->contains($employee)) {
                    $employee->addProject($project);
                }
            };

            // Remove employees that are no longer selected
            foreach (
                $existingEmployees
                as $oldEmployee
            ) {
                if (!$project->getEmployees()->contains($oldEmployee)) {
                    $oldEmployee->removeProject($project);
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_project', ['id' => $id]);
        }
        return $this->render('project/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/project/{id}/archive', name: 'app_project_archive'), ]

    public function archiveProject(
        int $id,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager
    ): Response {

        $project = $projectRepository->find($id);

        if (!$project) {
            return $this->redirectToRoute('app_homepage');
        };

        if($project->isArchived()) {
            return $this->redirectToRoute('app_homepage');
        }

        // Set isArchived to true (1)
        $project->setIsArchived(true);
       
        // Persist the change
        $entityManager->flush();

        return $this->redirectToRoute('app_homepage');
    }
}
