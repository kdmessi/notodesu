<?php
/**
 * Event Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class EventControllerTest.
 */
class EventControllerTest extends WebTestCase
{
    /**
     * Test client.
     *
     * @var KernelBrowser
     */
    private $httpClient;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test edit event.
     */
    public function testEdit(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser('testevent@test.pl');
        $this->logIn($user);

        $expectedContact = $this->createEvent($user);

        // when
        $this->httpClient->request('GET', '/pl/event/'.$expectedContact->getId().'/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }


    /**
     * Test edit event for non authorized user.
     */
    public function testEditNonAuthorizedUser(): void
    {
        // given
        $expectedStatusCode = 302;
        $user = $this->createUser('testevent@test.pl');
        $expectedContact = $this->createEvent($user);

        // when
        $this->httpClient->request('GET', '/pl/event/'.$expectedContact->getId().'/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test delete event for user.
     */
    public function testDelete(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser('testevent@test.pl');
        $this->logIn($user);

        $expectedEvent = $this->createEvent($user);

        // when
        $crawler = $this->httpClient->request('GET', '/pl/event/'.$expectedEvent->getId().'/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $form = $crawler->selectButton('Usuń')->form();
        $this->httpClient->submit($form);
        $this->httpClient->followRedirect();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
        $this->assertStringContainsString('Pomyślnie usunięto wydarzenie', $this->httpClient->getResponse()->getContent());
    }

    /**
     * Test index route.
     */
    public function testIndex(): void
    {
        // given
        $expectedStatusCode = 200;
        $this->logIn();

        // when
        $this->httpClient->request('GET', '/pl/event/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test show event.
     */
    public function testShow(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser('testevent@test.pl');
        $this->logIn($user);

        $expectedEvent = $this->createEvent($user);

        // when
        $this->httpClient->request('GET', '/pl/event/'.$expectedEvent->getId());
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals($expectedStatusCode, $result->getStatusCode());
    }

    /**
     * Test create event for user.
     */
    public function testCreate(): void
    {
        // given
        $expectedStatusCode = 200;
        $this->logIn();

        // when
        $crawler = $this->httpClient->request('GET', '/pl/event/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $form = $crawler->selectButton('Utwórz')->form();
        $form['event[title]']->setValue('Test title');
        $form['event[location]']->setValue('Test location');
        $form['event[category]']->setValue('');
        $this->httpClient->submit($form);
        $this->httpClient->followRedirect();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
        $this->assertStringContainsString('Pomyślnie dodano nowe wydarzenie.', $this->httpClient->getResponse()->getContent());
    }

    /**
     * LogIn method.
     *
     * @param User|null $user User entity
     */
    private function logIn(User $user = null): void
    {
        $session = self::$container->get('session');

        // somehow fetch the user (e.g. using the user repository)
        if (null === $user) {
            $user = $this->createUser('test@test.pl');
        }

        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'main';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->httpClient->getCookieJar()->set($cookie);
    }

    /**
     * Create user.
     *
     * @param string $email User email
     * @param array  $roles User roles
     *
     * @return User User entity
     */
    private function createUser(string $email, array $roles = [User::ROLE_USER]): User
    {
        $passwordEncoder = self::$container->get('security.password_encoder');
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
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
     * Create event.
     *
     * @param User $user User entity
     *
     * @return Event
     */
    private function createEvent(User $user): Event
    {
        $expectedCategory = new Category();
        $expectedCategory->setTitle('Test Category');
        $categoryRepository = self::$container->get(CategoryRepository::class);
        $categoryRepository->save($expectedCategory);

        $expectedEvent = new Event();
        $expectedEvent->setTitle('Testowy event');
        $expectedEvent->setLocation('Testowa lokacja');
        $expectedEvent->setDate(new DateTime());
        $expectedEvent->setAuthor($user);
        $expectedEvent->setCategory($expectedCategory);
        $contactRepository = self::$container->get(EventRepository::class);
        $contactRepository->save($expectedEvent);

        return $expectedEvent;
    }
}
