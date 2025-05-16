<?php

namespace App\Controller;

use App\Form\Enable2FAType;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Writer\PngWriter;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface as GoogleAuthenticatorTwoFactorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccountController extends AbstractController
{
    #[Route('/enable-2fa', name: 'enable_2fa')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function enable2fa(
        Request $request,
        GoogleAuthenticatorInterface $googleAuthenticator,
        EntityManagerInterface $entityManager
    ) {
        $employee = $this->getUser();
        $form = $this->createForm(Enable2FAType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Generate and store the secret
            $secret = $googleAuthenticator->generateSecret();
            $employee->setGoogleAuthenticatorSecret($secret);
            
            // $user->setRoles(['ROLE_USER', 'IS_AUTHENTICATED_2FA_IN_PROGRESS']);
            $entityManager->flush();

            return $this->redirectToRoute('app_logout');
        }

        return $this->render('auth/enable_2fa.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/2fa/qrcode', name: '2fa_qr_code')]
    public function showQrCode(GoogleAuthenticatorInterface $googleAuthenticator): Response
    {

        $employee = $this->getUser();
        
         if (!($employee instanceof GoogleAuthenticatorTwoFactorInterface)) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        $qrCode = $this->displayQrCode($googleAuthenticator->getQRContent($employee));

        return $qrCode;
    }


    #[Route('/2fa', name: '2fa_login')]
    public function displayGoogleAuthenticator(EntityManagerInterface $entityManager): Response
    {
        
        $employee = $this->getUser();
            
        if ($employee->getQrCodeShown()) {
        // Don't show the QR code again, just show the code entry page
            return $this->render('auth/2fa_code_entry.html.twig');
        }
        
        // Set the QR code shown flag to true
       $employee->setQrCodeShown(true);
        
        $entityManager->flush();

        return $this->render('auth/2fa.html.twig', [
            'qrCode' => $this->generateUrl('2fa_qr_code'),
        ]);
    }

    private function displayQrCode(string $qrCodeContent): mixed
    {
        $builder = new Builder(
            new PngWriter(),
            [],
            false,
            $qrCodeContent,
            new Encoding('UTF-8'),
            ErrorCorrectionLevel::Low,
            200,
            0,
            RoundBlockSizeMode::Margin
        );

        $result = $builder->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }
}