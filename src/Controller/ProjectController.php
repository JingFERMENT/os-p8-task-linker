<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjectRepository;

final class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(ProjectRepository $projectRepository ): Response
    {
        $projects = $projectRepository->findAll();
        
        return $this->render('project/list.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/{id}', name: 'app_project')]
    public function show(ProjectRepository $projectRepository, int $id): Response
    {
        $project = $projectRepository->find($id);
        
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }
   
}
