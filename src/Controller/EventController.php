<?php
/**
 * Event controller.
 */

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\EventService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.locales%>}/event")
 */
class EventController extends AbstractController
{
    /**
     * @var EventService Event service
     */
    private EventService $eventService;

    /**
     * TaskService constructor.
     *
     * @param EventService $eventService Event service
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Event list.
     *
     * @Route("/", name="event_index", methods={"GET"})
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    public function index(Request $request): Response
    {
        $filters = [];
        $filters['category_id'] = $request->query->getInt('filters_category_id');

        $pagination = $this->eventService->createPaginatedList(
            $request->query->getInt('page', 1),
            $this->getUser(),
            $filters
        );

        return $this->render('event/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Event create.
     *
     * @Route("/create", name="event_create", methods={"GET","POST"})
     *
     * @param Request         $request         HTTP request
     * @param EventRepository $eventRepository Event repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(Request $request, EventRepository $eventRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setAuthor($user);
            $eventRepository->save($event);
            $this->addFlash('success', 'global.message.event_created.success');

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display one event.
     *
     * @Route("/{id}", name="event_show", methods={"GET"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Event $event Event entity
     *
     * @return Response
     */
    public function show(Event $event): Response
    {
        $this->hasUserAccess($event);

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * Event edit.
     *
     * @Route("/{id}/edit", name="event_edit", methods={"GET","PUT"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Request         $request         HTTP request
     * @param Event           $event           Event entity
     * @param EventRepository $eventRepository Event repository
     *
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $this->hasUserAccess($event);
        $form = $this->createForm(EventType::class, $event, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event);
            $this->addFlash('success', 'global.message.event_updated.success');

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete event action.
     *
     * @Route("/{id}/delete", name="event_delete", methods={"GET", "DELETE"}, requirements={"id": "[1-9]\d*"})
     *
     * @param Request         $request         HTTP request
     * @param Event           $event           Event entity
     * @param EventRepository $eventRepository Event repository
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $this->hasUserAccess($event);

        $this->hasUserAccess($event);
        $form = $this->createForm(EventType::class, $event, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->delete($event);
            $this->addFlash('success', 'global.message.event_deleted.success');

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Has user access to this action.
     *
     * @param Event $event Event object
     */
    private function hasUserAccess(Event $event): void
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->getId() !== $event->getAuthor()->getId()) {
            throw $this->createAccessDeniedException('Access Denied.');
        }
    }
}
