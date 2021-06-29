<?php
/**
 * Contact Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class ContactControllerTest.
 */
class ContactControllerTest extends WebTestCase
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
     * Test create contact for user.
     */
    public function testCreate(): void
    {
        // given
        $expectedStatusCode = 200;
        $this->logIn();

        // when
        $crawler = $this->httpClient->request('GET', '/pl/contact/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $form = $crawler->selectButton('Utwórz')->form();
        $form['contact[firstName]']->setValue('Jan');
        $form['contact[lastName]']->setValue('Kowalski');
        $form['contact[phone]']->setValue('111-222-333');
        $form['contact[address]']->setValue('Test address');
        $this->httpClient->submit($form);
        $this->httpClient->followRedirect();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
        $this->assertStringContainsString('Pomyślnie dodano nowy kontakt.', $this->httpClient->getResponse()->getContent());
    }

    /**
     * Test show contact.
     */
    public function testShow(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser('testcontactshow@test.pl');
        $this->logIn($user);

        $expectedContact = $this->createContact($user);

        // when
        $this->httpClient->request('GET', '/pl/contact/'.$expectedContact->getId());
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals($expectedStatusCode, $result->getStatusCode());
    }

    /**
     * Test edit category.
     */
    public function testEdit(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser('testcontactshow@test.pl');
        $this->logIn($user);

        $expectedContact = $this->createContact($user);

        // when
        $this->httpClient->request('GET', '/pl/contact/'.$expectedContact->getId().'/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }


    /**
     * Test edit category for non authorized user.
     */
    public function testEditNonAuthorizedUser(): void
    {
        // given
        $expectedStatusCode = 302;
        $user = $this->createUser('testcontactshow@test.pl');
        $expectedContact = $this->createContact($user);

        // when
        $this->httpClient->request('GET', '/pl/contact/'.$expectedContact->getId().'/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route.
     */
    public function testIndex(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser('testcontactshow@test.pl');
        $this->logIn($user);

        // when
        $this->httpClient->request('GET', '/pl/contact/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test delete category for user.
     */
    public function testDelete(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = $this->createUser('testcontactshow@test.pl');
        $this->logIn($user);
        $expectedContact = $this->createContact($user);

        // when
        $crawler = $this->httpClient->request('GET', '/pl/contact/'.$expectedContact->getId().'/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $form = $crawler->selectButton('Usuń')->form();
        $this->httpClient->submit($form);
        $this->httpClient->followRedirect();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
        $this->assertStringContainsString('Pomyślnie usunięto kontakt', $this->httpClient->getResponse()->getContent());
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
     * Create contact.
     *
     * @param User $user User entity
     *
     * @return Contact
     */
    private function createContact(User $user): Contact
    {
        $expectedContact = new Contact();
        $expectedContact->setFirstName('Jan');
        $expectedContact->setLastName('Kowalski');
        $expectedContact->setAddress('testowy adres');
        $expectedContact->setPhone('111-222-333');
        $expectedContact->setUser($user);
        $contactRepository = self::$container->get(ContactRepository::class);
        $contactRepository->save($expectedContact);

        return $expectedContact;
    }
}
