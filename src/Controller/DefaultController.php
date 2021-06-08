<?php
/**
 * DefaultController
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.locales%>}", name="homepage")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/", name="default")
     *
     * @return Response
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('homepage', ['_locale' => 'pl']);
    }
}
