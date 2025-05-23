<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Enum\ContractName;
use App\Enum\RoleName;
use App\Form\EmployeeType;
use App\Form\RegistrationFormType;
use App\Repository\EmployeeRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\CssSelector\XPath\TranslatorInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;

final class EmployeeController extends AbstractController
{

    #[Route('/welcome', name: 'app_welcome')]
    public function welcome(): Response
    {
        return $this->render('auth/welcome.html.twig');
    }

    #[Route('/registration', name: 'app_registration')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        VerifyEmailHelperInterface $verifyEmailHelper,
        TransportInterface $mailer
    ): Response {
        $employee = new Employee();
        // to several mandatory fields, we set default values
        $employee->setStartDate(new \DateTime('today'));
        $employee->setContract(ContractName::PermanentContract);

        $employee->setRoles(['ROLE_USER']);
        $employee->setRole(RoleName::Collaborator);
        $employee->setIsActif(true);

        $form = $this->createForm(RegistrationFormType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $employee->setPassword($userPasswordHasher->hashPassword($employee, $plainPassword));

            $entityManager->persist($employee);
            $entityManager->flush();

            // Generate a signed email verification link 
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $employee->getId(),
                $employee->getEmail(),
                ['id' => $employee->getId()]
            );

            // Create the email with the signed URL
            $email = (new Email())
                ->from(new Address('TEST@TEST.com', 'Test'))
                ->to($employee->getEmail())
                ->subject('Merci de confirmer votre email')
                ->html('<p>Merci de confirmer votre email en cliquant <a href="' . $signatureComponents->getSignedUrl() . '">ici</a>.</p>');

            // Send the verification email
            $mailer->send($email);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/employees', name: 'app_employees')]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        $employees = $employeeRepository->findAll();

        return $this->render('employee/list.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/employees/{id}/edit', name: 'app_employees_edit', requirements: ['id' => '\d+'])]
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
            $businessRole = $employee->getRole();

            if ($businessRole === RoleName::ProjectManager) {
                $employee->setRoles(['ROLE_ADMIN']);
            } else {
                $employee->setRoles(['ROLE_USER']);
            }

            $entityManager->persist($employee);
            $entityManager->flush();
            return $this->redirectToRoute('app_employees');
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/employees/{id}/delete', name: 'app_employees_delete', requirements: ['id' => '\d+'])]
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
