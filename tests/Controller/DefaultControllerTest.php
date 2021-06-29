<?php
/**
 * Default Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class DefaultControllerTest.
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * Client.
     *
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser|null
     */
    private $client = null;

    /**
     *
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test / route index method.
     *
     */
    public function testIndexNoLocale(): void
    {
        // given
        $expectedStatusCode = 302;

        // when
        $this->client->request('GET', '/');
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->client->followRedirect();

        // then
        $this->assertEquals($expectedStatusCode, $statusCode);
        $this->assertResponseRedirects('/pl/login');
    }

    /**
     * Test rendering index method.
     *
     * @param string $lang  language of page
     * @param string $title title page
     *
     * @dataProvider dataProviderForTestIndexHtml
     */
    public function testIndex(string $lang, string $title): void
    {
        // given
        $this->logIn();

        // when
        $this->client->request('GET', '/'.$lang);

        // then
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', $title);
    }

    /**
     * Data provider for testIndexHtml() method.
     *
     * @return \Generator Test case
     */
    public function dataProviderForTestIndexHtml(): Generator
    {
        yield 'pl' => [
            'lang' => 'pl',
            'title' => 'Strona główna',
        ];

        yield 'en' => [
            'lang' => 'en',
            'title' => 'Homepage',
        ];
    }

    /**
     * LogIn method.
     *
     */
    private function logIn(): void
    {
        $session = self::$container->get('session');

        // somehow fetch the user (e.g. using the user repository)
        $user = $this->createUser('test@test.pl');

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
        $this->client->getCookieJar()->set($cookie);
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
}
