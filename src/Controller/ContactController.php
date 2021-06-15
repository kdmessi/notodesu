<?php
/**
 * Contact controller.
 */

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.locales%>}/contact")
 */
class ContactController extends AbstractController
{
    /**
     * Contact list.
     *
     * @Route("/", name="contact_index", methods={"GET"})
     *
     * @param Request            $request           HTTP request
     * @param ContactRepository  $contactRepository Contact repository
     * @param PaginatorInterface $paginator         Paginator
     *
     * @return Response HTTP response
     */
    public function index(Request $request, ContactRepository $contactRepository, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Contact $contacts */
        $contacts = $contactRepository->findBy(['user' => $user->getId()]);

        $pagination = $paginator->paginate(
            $contacts,
            $request->query->getInt('page', 1),
            ContactRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('contact/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Contact create.
     *
     * @Route("/create", name="contact_create", methods={"GET","POST"})
     *
     * @param Request           $request           HTTP request
     * @param ContactRepository $contactRepository Contact repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(Request $request, ContactRepository $contactRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setUser($user);
            $contactRepository->save($contact);
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
     * @param Request           $request           HTTP request
     * @param Contact           $contact           Category entity
     * @param ContactRepository $contactRepository Category repository
     *
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function edit(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        $this->hasUserAccess($contact);

        $form = $this->createForm(ContactType::class, $contact, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->save($contact);

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
     * @param Request           $request           HTTP request
     * @param Contact           $contact           Category entity
     * @param ContactRepository $contactRepository Category repository
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
    public function delete(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        $this->hasUserAccess($contact);

        $form = $this->createForm(FormType::class, $contact, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->delete($contact);
            $this->addFlash('success', 'global.message.contact_deleted.success');

            return $this->redirectToRoute('contact_index');
        }

        return $this->render(
            'contact/delete.html.twig',
            [
                'form' => $form->createView(),
                'contact' => $contact,
            ]
        );
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
