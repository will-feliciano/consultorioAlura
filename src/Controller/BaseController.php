<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends AbstractController{

    /**
     * @var ObjectController
     */
    private $repository;

    public function __construct(ObjectRepository $repository){
        $this->repository = $repository;
    }

    public function buscarTodos(): Response{

        $medicoList = $this->medicosRepository->findAll();

        return new JsonResponse($medicoList);
    }
}

