<?php
/**
 * EventService tests.
 */

namespace App\Tests\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Service\EventService;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class EventServiceTest.
 */
class EventServiceTest extends KernelTestCase
{
    /**
     * Event service.
     *
     * @var EventService|object|null
     */
    private $eventService;

    /**
     * Event repository.
     *
     * @var EventRepository|object|null
     */
    private $eventRepository;

    /**
     * Set up test.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::$container;

        $this->eventRepository = $container->get(EventRepository::class);
        $this->eventService = $container->get(EventService::class);
    }

    /**
     * Test pagination empty list.
     */
    public function testCreatePaginatedListEmptyList(): void
    {
        // given
        $page = 1;
        $expectedResultSize = 0;

        $user = $this->simpleUser();

        // when
        $result = $this->eventService->createPaginatedList($page, $user);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Test pagination list.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testCreatePaginatedList(): void
    {
        // given
        $page = 1;
        $user = $this->simpleUser();
        $dataSetSize = 3;
        $expectedResultSize = 0;

        $counter = 0;
        while ($counter < $dataSetSize) {
            $this->simpleEvent($user);

            ++$counter;
        }

        // when
        $result = $this->eventService->createPaginatedList($page, $user);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Test save.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSave(): void
    {
        // given
        $event = $this->simpleEvent();

        // when
        $resultEvent = $this->eventRepository->findOneById(
            $event->getId()
        );

        // then
        $this->assertEquals($event, $resultEvent);
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testDelete(): void
    {
        // given
        $event = $this->simpleEvent();
        $expectedId = $event->getId();

        // when
        $this->eventService->delete($event);
        $result = $this->eventService->findOneById($expectedId);

        // then
        $this->assertNull($result);
    }

    /**
     * Test find by id.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testFindOneById(): void
    {
        // given
        $event = $this->simpleEvent();
        $expectedId = $event->getId();

        // when
        $result = $this->eventService->findOneById($expectedId);

        // then
        $this->assertEquals($expectedId, $result->getId());
    }

    /**
     * Simple user method.
     *
     * @return User
     */
    private function simpleUser(): User
    {
        $passwordEncoder = self::$container->get('security.password_encoder');
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles([User::ROLE_USER]);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                'p@55w0rd'
            )
        );

        $userRepository = self::$container->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }

    /**
     * Simple event method.
     *
     * @param User|null $user User entity
     *
     * @return Event
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function simpleEvent(User $user = null): Event
    {
        $event = new Event();
        $event->setTitle('Test event');
        $event->setLocation('Warszawa');
        $date = DateTime::createFromFormat('U', time());
        $event->setUpdatedAt($date);
        $event->setCreatedAt($date);
        $event->setDate($date);

        if (false === $user instanceof User) {
            $event->setAuthor($this->simpleUser());
        } else {
            $event->setAuthor($user);
        }


        $this->eventRepository->save($event);

        return $event;
    }
}
