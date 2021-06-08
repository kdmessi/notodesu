<?php
/**
 * Login form authenticator.
 */

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class LoginFormAuthenticator.
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    /**
     * User repository.
     *
     * @var \App\Repository\UserRepository
     */
    private $userRepository;

    /**
     * URL generator.
     *
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * CSRF token manager.
     *
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * Password encoder.
     *
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param \App\Repository\UserRepository $userRepository User repository
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator URL generator
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager CSRF token manager
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder Password encoder
     */
    public function __construct(UserRepository $userRepository, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Does the authenticator support the given Request?
     *
     * If this returns false, the authenticator will be skipped.
     *
     * @param Request $request HTTP request
     *
     * @return bool Result
     */
    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g. an associate array). If you return null, authentication
     * will be skipped.
     *
     * @param Request $request HTTP request
     *
     * @return array Credential array
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * Get user.
     *
     * @param mixed $credentials Credentials
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider User provider
     *
     * @return \App\Entity\User|null Result
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->userRepository->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            throw new UsernameNotFoundException('Email could not be found.');
        }

        return $user;
    }

    /**
     * Checks credentials.
     *
     * @param mixed $credentials Credentials
     * @param \Symfony\Component\Security\Core\User\UserInterface $user User
     *
     * @return bool Result
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param array $credentials User credentials
     *
     * @return string|null Hashed password
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    /**
     * Called when authentication executed and was successful!
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token Authentication token
     * @param string $providerKey The key of the firewall
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse Redirect response
     *
     * @throws \Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
        return new RedirectResponse($this->urlGenerator->generate('default'));
    }

    /**
     * Return the URL to the login page.
     *
     * @return string Login URL
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
