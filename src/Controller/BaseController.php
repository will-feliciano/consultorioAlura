<?php

namespace App\Controller;

use App\Helper\ResponseFactory;
use App\Helper\ExtractDataRequest;
use App\Helper\EntidadeFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController{   

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var EntidadeFactoryInterface
     */
    protected $factory;

    /**
     * @var ExtractDataRequest
     */
    private $extractData;

    public function __construct(        
        EntityManagerInterface $entityManager,
        ObjectRepository $repository, 
        EntidadeFactoryInterface $factory,
        ExtractDataRequest $extractData
    ){
        $this->entityManager = $entityManager;
        $this->repository = $repository;        
        $this->factory = $factory;
        $this->extractData = $extractData;
    }
    
    public function getAll(Request $req): Response
    {
        $order = $this->extractData->getDataOrder($req);
        $filter = $this->extractData->getDataFilter($req); 
        [$paginaAtual, $itensPorPagina] = $this->extractData->getQtdePages($req);              
        
        $lista = $this->repository->findBy(
            $filter, 
            $order,
            $itensPorPagina,
            ($paginaAtual - 1) * $itensPorPagina
        );

        $resposta = new ResponseFactory(
            true,
            $lista,
            Response::HTTP_OK,
            $paginaAtual,
            $itensPorPagina            
        );
        
        return $resposta->getResponse();

    }
    
    public function getOne(int $id): Response
    {
        $item = $this->repository->find($id);
        $cod = is_null($item) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        
        $resposta = new ResponseFactory(
            true,            
            $item,
            $cod
        );

        return $resposta->getResponse();       
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

            $resposta = new ResponseFactory(
                true,
                $entidadeExistente,
                Response::HTTP_OK
            );

            return $resposta->getResponse();

        } catch (\InvalidArgumentException $ex) {

            $resposta = new ResponseFactory(
                false,
                "Recurso não encontrado",
                Response::HTTP_NOT_FOUND
            );

            return $resposta->getResponse();
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