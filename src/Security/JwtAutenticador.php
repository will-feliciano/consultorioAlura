<?php

namespace App\Security;

use Firebase\JWT\JWT;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


class JwtAutenticador extends AbstractGuardAuthenticator{ 

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // TODO: Implement start() method.
    }

    public function supports(Request $request){
        return $request->getPathInfo() !== '/login';
    }

    /**
     * @param Request $request
     *
     * @return mixed Any non-null value
     *
     * @throws \UnexpectedValueException If null is returned
     */    

    public function getCredentials(Request $request){
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));

        try {
            return JWT::decode($token, 'chave', ['HS256']);
        } catch (\Exception $e) {
            return false;
        }
    }

    

    
    

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @throws AuthenticationException
     *
     * @return UserInterface|null
     */    
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!is_object($credentials) || !property_exists($credentials, 'username')) {
            return null;
        }
        $username = $credentials->username;
        return $this->repository->findOneBy(['username' => $username]);
    }

    /**   
     *
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     *
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return is_object($credentials) && property_exists($credentials, 'username');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'erro' => 'Falha na autenticação'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {	
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
