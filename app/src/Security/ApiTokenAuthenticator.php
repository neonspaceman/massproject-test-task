<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    protected const HEADER_AUTH_USER = 'X-AUTH-USER';
    protected const HEADER_AUTH_PASSWORD = 'X-AUTH-PASSWORD';

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::HEADER_AUTH_USER)
            && $request->headers->has(self::HEADER_AUTH_PASSWORD);
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->headers->get(self::HEADER_AUTH_USER);
        $password = $request->headers->get(self::HEADER_AUTH_PASSWORD);

        if ($email === null || $password === null) {
            throw new AuthenticationException();
        }

        return new Passport(
            new UserBadge($email),
            new CustomCredentials(function (string $credentials, User $user): bool {
                return $this->passwordHasher->isPasswordValid($user, $credentials);
            }, $password));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception): ?Response
    {
        throw new AuthenticationException();
    }
}
