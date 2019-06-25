<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController{

    public function __construct(ObjectRepository $repository){
        $this->repository = $repository;
    }
    
    public function getAll(): Response
    {
        $entityList = $this->repository->findAll();

        return new JsonResponse($entityList);
    }
}