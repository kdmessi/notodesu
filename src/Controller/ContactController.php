<?php
/**
 * Contact controller.
 */

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\ContactType;
use App\Service\ContactService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContactController.
 *
 * @Route("/{_locale<%app.locales%>}/contact")
 *
 * @IsGranted("ROLE_USER")
 */
class ContactController extends AbstractController
{
    /**
     * Contact service.
     *
     * @var ContactService
     */
    private ContactService $contactService;

    /**
     * ContactController constructor.
     *
     * @param ContactService $contactService Contact service
     */
    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Contact list.
     *
     * @Route("/", name="contact_index", methods={"GET"})
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $page = $request->query->getInt('page', 1);
        $pagination = $this->contactService->createPaginatedList($page, $user);

        return $this->render('contact/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Contact create.
     *
     * @Route("/create", name="contact_create", methods={"GET","POST"})
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setUser($user);
            $this->contactService->save($contact);
            $this->addFlash('success', 'global.message.contact_created.success');

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display one contact.
     *
     * @Route("/{id}", name="contact_show", methods={"GET"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Contact $contact Contact entity
     *
     * @return Response
     */
    public function show(Contact $contact): Response
    {
        $this->hasUserAccess($contact);

        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * Contact edit.
     *
     * @Route("/{id}/edit", name="contact_edit", methods={"GET","PUT"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Request $request HTTP request
     * @param Contact $contact Contact entity
     *
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function edit(Request $request, Contact $contact): Response
    {
        $this->hasUserAccess($contact);

        $form = $this->createForm(ContactType::class, $contact, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactService->save($contact);

            $this->addFlash('success', 'global.message.contact_updated.success');

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Contact $contact Contact entity
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="contact_delete",
     * )
     */
    public function delete(Request $request, Contact $contact): Response
    {
        $this->hasUserAccess($contact);

        $form = $this->createForm(ContactType::class, $contact, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactService->delete($contact);
            $this->addFlash('success', 'global.message.contact_deleted.success');

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/delete.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact,
        ]);
    }

    /**
     * Has user access to this action.
     *
     * @param Contact $contact
     */
    private function hasUserAccess(Contact $contact): void
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->getId() !== $contact->getUser()->getId()) {
            throw $this->createAccessDeniedException('Access Denied.');
        }
    }
}
