<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    /**
     * @var UserRepository 
     */
    private $repository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserRepository $repository, UserPasswordEncoderInterface $encoder){
        $this->repository = $repository;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="login")
     */
    public function index(Request $req){
        $json = json_decode($req->getContent()); 
       
        if(is_null($json->usuario) || is_null($json->senha)){
            return new JsonResponse([
                'erro' => 'Favor enviar usuário e senha!'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy([
            'username' => $json->usuario
        ]);
        
        if(!$this->encoder->isPasswordValid($user, $json->senha)){
            return new JsonResponse([
                'erro' => 'Usuário ou senha invalidos!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = JWT::encode(['username' => $user->getUsername()], 'chave');

        return new JsonResponse([
            'access_token' => $token
        ]);
    }
}
