<?php

namespace App\Security;

use Firebase\JWT\JWT;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class jwtAutenticador extends AbstractGuardAuthenticator{

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function supports(Request $request){
        return $request->pathInfo() !== '/login';
    }

    public function getCredencials(Request $request){
        $token = str_replace('Bearer', '', $request->headers->get('Authorization'));
        
        try{
            return JWT::decode($token, 'chave', ['HS256']);
        }catch(Exception $e){
            return false;
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider){
        if(!is_object($credentials) || !property_exists($credentials, 'username')){
            return null;
        }
        $username = $credentials->username;
        return $this->repository->findOneBy(['username' => $username]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return is_object($credentials) && property_exists($credentials, 'username');
    }

    public function onAuthenticationSuccess(Request $request){
        return null;
    }

    public function onAuthenticationFailure(Request $request){
        return new JsonResponse([
            'erro' => 'Falha na autenticação'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(){
        return false;
    }
}