<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EspecialidadeRepository
     */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/especialidades", methods={"POST"})
     */
    public function nova(Request $req): Response
    {
        $dadosRec = json_decode($req->getContent());
        
        $especialidade = new Especialidade();
        $especialidade->setDescricao($dadosRec->descricao);

        $this->entityManager->persist($especialidade);
        $this->entityManager->flush();

        return new JsonResponse($especialidade);
    }

    /**
     * @Route("/especialidades", methods={"GET"})
     */
    public function allEspecs(): Response{
        
        $listEspecs = $this->repository->findAll();

        return new JsonResponse($listEspecs);
    }

    /**
     * @Route("/especialidades/{id}", methods={"GET"})
     */
    public function especialidade(int $id): Response{

        return new JsonResponse($this->repository->find($id));
    }

    /**
     * @Route("/especialidades/{id}", methods={"PUT"})
     */
    public function editEspec(int $id, Request $req): Response{

        $dadosRec = json_decode($req->getContent());
        
        $especialidade = $this->repository->find($id);
        $especialidade->setDescricao($dadosRec->descricao);

        $this->entityManager->flush();

        return new JsonResponse($especialidade);
    }

    /**
     * @Route("/especialidades/{id}", methods={"DELETE"})
     */
    public function removeEspec(int $id): Response{

        $espec = $this->repository->find($id);
        $this->entityManager->remove($espec);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

}
