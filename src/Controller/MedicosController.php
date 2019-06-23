<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
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

    public function __construct(EntityManagerInterface $entityManager, MedicoFactory $medicoHelper){
       $this->entityManager = $entityManager;
       $this->medicoHelper = $medicoHelper;
    }

    /**
     * @Route("/medicos", methods={"POST"})
     */
    public function novo(Request $req): Response{

        $dadosRec = $req->getContent();        
        $doctor = $this->medicoHelper->criarMedico($dadosRec);
        
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
    public function doctor(int $id): Response{
                
        $doctor = $this->buscaMedico($id);
        $cod = is_null($doctor)? Response::HTTP_NO_CONTENT : 200;
        return new JsonResponse($doctor, $cod);
    }

    /**
     * @Route("/medicos/{id}", methods={"PUT"})
     */
    public function editDoctor(int $id, Request $req){

        $dadosRecebidos = $req->getContent();
        $novoMedico = $this->medicoHelper->criarMedico($dadosRecebidos);
        
        $existente = $this->buscaMedico($id);        

        if(is_null($existente)){
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $existente->setCrm($novoMedico->getCrm())->setName($novoMedico->getName());
        
        $this->entityManager->flush();

        return new JsonResponse($existente);
    }

    /**
     * @Route("/medicos/{id}", methods={"DELETE"})
     */
    public function removeDoctor(int $id){
        $medico = $this->buscaMedico($id);
        $this->entityManager->remove($medico);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function buscaMedico(int $id){

        $repo = $this
            ->getDoctrine()
            ->getRepository(Medico::class);
        return $repo->find($id);
    }
}