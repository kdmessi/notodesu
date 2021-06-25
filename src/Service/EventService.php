<?php
/**
 * Event service.
 */

namespace App\Service;

use App\Repository\EventRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use App\Entity\Event;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class EventService.
 */
class EventService
{
    /**
     * Event repository.
     *
     * @var EventRepository
     */
    private EventRepository $eventRepository;

    /**
     * Paginator.
     *
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * Category service.
     *
     * @var CategoryService
     */
    private CategoryService $categoryService;

    /**
     * EventService constructor.
     *
     * @param EventRepository    $eventRepository Event repository
     * @param PaginatorInterface $paginator       Paginator
     * @param CategoryService    $categoryService Category service
     */
    public function __construct(EventRepository $eventRepository, PaginatorInterface $paginator, CategoryService $categoryService)
    {
        $this->eventRepository = $eventRepository;
        $this->paginator = $paginator;
        $this->categoryService = $categoryService;
    }

    /**
     * Find category by Id.
     *
     * @param int $id Event Id
     *
     * @return Event|null Event entity
     */
    public function findOneById(int $id): ?Event
    {
        return $this->eventRepository->findOneById($id);
    }

    /**
     * Create paginated list.
     *
     * @param int           $page    Page number
     * @param UserInterface $user    User entity
     * @param array         $filters Filters array
     *
     * @return PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page, UserInterface $user, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->eventRepository->queryByAuthor($user, $filters),
            $page,
            EventRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save category.
     *
     * @param Event $event Event entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Event $event): void
    {
        $this->eventRepository->save($event);
    }

    /**
     * Delete category.
     *
     * @param Event $event Event entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Event $event): void
    {
        $this->eventRepository->delete($event);
    }

    /**
     * Prepare filters for the tasks list.
     *
     * @param array $filters Raw filters from request
     *
     * @return array Result array of filters
     */
    private function prepareFilters(array $filters): array
    {
        $resultFilters = [];
        if (isset($filters['category_id']) && is_numeric($filters['category_id'])) {
            $category = $this->categoryService->findOneById(
                $filters['category_id']
            );
            if (null !== $category) {
                $resultFilters['category'] = $category;
            }
        }

        return $resultFilters;
    }
}
