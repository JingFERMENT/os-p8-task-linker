<?php

namespace App\Controller;

use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EmployeeController extends AbstractController
{
    #[Route('/employees', name: 'app_employees')]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        $employees = $employeeRepository->findAll();

        return $this->render('employee/list.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/employee/{id}/edit', name: 'app_employee_edit', requirements: ['id' => '\d+'])]
    public function employeeEdit(
        Request $request,
        EmployeeRepository $employeeRepository,
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $employee = $employeeRepository->find($id);

        // if no employee correspond, redirect to the employees list page
        if (!$employee) {
            return $this->redirectToRoute('app_employees');
        }

        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_employees');
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/employee/{id}/delete', name: 'app_employee_delete', requirements: ['id' => '\d+'])]
    public function employeeDelete(int $id, EmployeeRepository $employeeRepository, EntityManagerInterface $entityManager)
    {

        $employee = $employeeRepository->find($id);

        if (!$employee) {
            return $this->redirectToRoute('app_employees');
        }

        $entityManager->remove($employee);
        $entityManager->flush();

        return $this->redirectToRoute('app_employees');
    }
}
