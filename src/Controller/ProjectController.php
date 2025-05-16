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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ProjectController extends AbstractController
{

    #[Route('/', name: 'app_projects')]
    public function index(ProjectRepository $projectRepository, Security $security): Response
    {
        // Get the currently authenticated user
        $employee = $security->getUser();

        if (!$employee->isGoogleAuthenticatorEnabled()) {
            return $this->redirectToRoute('enable_2fa');
        }

        // Check if the user has the ROLE_ADMIN role    
        if ($this->isGranted('ROLE_ADMIN')) {
            // If the user is an admin, fetch all projects
            $projects = $projectRepository->findAll();
        } elseif ($security->isGranted('ROLE_USER')) {

            $projects = $projectRepository->findByEmployee($employee); // Fetch projects associated with the employee
        } else {

            return $this->redirectToRoute('app_projects'); // Redirect if no valid role is found
        }

        return $this->render('project/list.html.twig', [
            'projects' => $projects,
        ]);
    }


    #[Route('/projects/{id}/', name: 'app_project', requirements: ['id' => '\d+'])]
    public function show(
        ProjectRepository $projectRepository,
        EmployeeRepository $employeeRepository,
        TaskRepository $taskRepository,
        StatutRepository $statutRepository,
        int $id
    ): Response {

        $project = $projectRepository->find($id);

        if (!$project) {
            return $this->redirectToRoute('app_projects');
        }

        if (!$this->isGranted('PROJECT_VIEW', $project)) {
            return $this->redirectToRoute('app_projects');
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


    #[Route('/projects/add', name: 'app_project_add')]
    public function projectAdd(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {

        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_projects');
        }

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
            return $this->redirectToRoute('app_projects');
        }

        return $this->render('project/add.html.twig', [
            'form' => $form
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/projects/{id}/edit', name: 'app_project_edit')]

    public function projectEdit(
        int $id,
        Request $request,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager
    ): Response {

        $project = $projectRepository->find($id);
        if (!$project) {
            return $this->redirectToRoute('app_projects');
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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/projects/{id}/archive', name: 'app_project_archive'),]

    public function archiveProject(
        int $id,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager
    ): Response {

        $project = $projectRepository->find($id);

        if (!$project) {
            return $this->redirectToRoute('app_projects');
        };

        if ($project->isArchived()) {
            return $this->redirectToRoute('app_projects');
        }

        // Set isArchived to true (1)
        $project->setIsArchived(true);

        // Persist the change
        $entityManager->flush();

        return $this->redirectToRoute('app_projects');
    }
}
