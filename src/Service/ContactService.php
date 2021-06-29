<?php
/**
 * Contact service.
 */

namespace App\Service;

use App\Repository\ContactRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use App\Entity\Contact;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ContactService.
 */
class ContactService
{
    /**
     * Contact repository.
     *
     * @var ContactRepository
     */
    private ContactRepository $contactRepository;

    /**
     * Paginator.
     *
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * ContactService constructor.
     *
     * @param ContactRepository  $contactRepository Contact repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(ContactRepository $contactRepository, PaginatorInterface $paginator)
    {
        $this->contactRepository = $contactRepository;
        $this->paginator = $paginator;
    }

    /**
     * Find category by Id.
     *
     * @param int $id Contact Id
     *
     * @return Contact|null Contact entity
     */
    public function findOneById(int $id): ?Contact
    {
        return $this->contactRepository->findOneById($id);
    }

    /**
     * Create paginated list.
     *
     * @param int           $page Page number
     * @param UserInterface $user User entity
     *
     * @return PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page, UserInterface $user): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->contactRepository->findByUser($user),
            $page,
            ContactRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save category.
     *
     * @param Contact $contact Contact entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Contact $contact): void
    {
        $this->contactRepository->save($contact);
    }

    /**
     * Delete category.
     *
     * @param Contact $contact Contact entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Contact $contact): void
    {
        $this->contactRepository->delete($contact);
    }
}
