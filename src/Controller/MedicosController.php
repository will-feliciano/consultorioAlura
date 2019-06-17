<?php

namespace App\Controller;

use App\Entity\Medico;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractController{

    /**
     * @var EntityManagerInterface
     */

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
       $this->entityManager = $entityManager;
    }

    /**
     * @Route("/medicos", methods={"POST"})
     */

    public function novo(Request $req): Response{

        $dadosRec = $req->getContent();
        $dadosRec = json_decode($dadosRec);       
        
        $doctor = new Medico();
        $doctor->crm = $dadosRec->crm;
        $doctor->name = $dadosRec->nome;

        $this->entityManager->persist($doctor);
        $this->entityManager->flush();

        return new JsonResponse($doctor);
    }

    /**
     * @Route("/medicos", methods={"GET"})
     */
    public function allDoctors(): Response{
        $repo = $this
            ->getDoctrine()
            ->getRepository(Medico::class);
        $listDoctors = $repo->findAll();

        return new JsonResponse($listDoctors);
    }

    /**
     * @Route("/medicos/{id}", methods={"GET"})
     */
    public function doctor(Request $req): Response{
        $id = $req->get('id');
        $repo = $this
            ->getDoctrine()
            ->getRepository(Medico::class);
        $doctor = $repo->find($id);

        return new JsonResponse($doctor);
    }
}