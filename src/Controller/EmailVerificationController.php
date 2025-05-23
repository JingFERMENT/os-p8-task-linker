<?php

namespace App\Controller;

use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

final class EmailVerificationController extends AbstractController
{
    // This route will be called when the user clicks the verification link from the email
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, EntityManagerInterface $entityManager): RedirectResponse
    {
        $userId = $request->get('id');

        if (!$userId) {
            throw $this->createNotFoundException();
        }

        $employee = $entityManager->getRepository(Employee::class)->find($userId);

        if (!$employee) {
            throw $this->createNotFoundException();
        }

        try {
            $verifyEmailHelper->validateEmailConfirmationFromRequest($request, $employee->getId(), $employee->getEmail());
            $employee->setIsVerified(true);
            $entityManager->flush();

            // Optionally flash a success message
        } catch (\Exception $e) {
            // Handle verification failure
        }

        return $this->redirectToRoute('app_login'); // Or wherever
    }
}
