<?php
/**
 * Security controller.
 */

namespace App\Controller;

use App\Form\LogInType;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController.
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/{_locale<%app.locales%>}/login", name="app_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LogInType::class);
        $form->get('email')->setData($lastUsername);

        if ($error) {
            $form->addError(new FormError($error->getMessage()));
        }

        return $this->render('security/login.html.twig', ['error' => $error, 'form' => $form->createView()]);
    }
}
