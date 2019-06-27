<?php

namespace App\Controller;

use App\Helper\EntidadeFactoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController{
    
    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntidadeFactoryInterface
     */
    protected $factory;

    public function __construct(
        ObjectRepository $repository, 
        EntityManagerInterface $entityManager,
        EntidadeFactoryInterface $factory
    ){
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
    }
    
    public function getAll(): Response
    {
        return new JsonResponse($this->repository->findAll());
    }
    
    public function getOne(int $id): Response
    {
        $item = $this->repository->find($id);
        $cod = is_null($item) ? Response::HTTP_NO_CONTENT : 200;

        return new JsonResponse($item, $cod);
    }

    public function novo(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $entidade = $this->factory->criarEntidade($corpoRequisicao);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        return new JsonResponse($entidade);
    }

    public function edit(int $id, Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $entidade = $this->factory->criarEntidade($corpoRequisicao);

        try {
            $entidadeExistente = $this->atualizaEntidadeExistente($id, $entidade);
            $this->entityManager->flush();

            return new JsonResponse($entidadeExistente);
        } catch (\InvalidArgumentException $ex) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
    }

    public function remove(int $id): Response
    {
        $item = $this->repository->find($id);
        $this->entityManager->remove($item);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    abstract function atualizaEntidadeExistente(int $id, $entidade);
}