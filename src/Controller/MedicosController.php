<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Repository\MedicosRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\MedicoFactory;
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

    /**
     * @var MedicoFactory
     */
    private $medicoHelper;

    /**
     * @var MedicosRepository
     */
    private $medicosRepository;

    public function __construct(
        EntityManagerInterface $entityManager, 
        MedicoFactory $medicoHelper,
        MedicosRepository $medicosRepository
    ){
       $this->entityManager = $entityManager;
       $this->medicoHelper = $medicoHelper;
       $this->medicosRepository = $medicosRepository;
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

        $listDoctors = $this->medicosRepository->findAll();
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

        $existente
            ->setCrm($novoMedico->getCrm())
            ->setNome($novoMedico->getNome());
        
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
        
        return $this->medicosRepository->find($id);
    }

    /**
     * @Route("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function getDoctorsForSpecs(int $especialidadeId){
        
        $medicos = $this->medicosRepository->findBy([
            'especialidade' => $especialidadeId
        ]);

        return new JsonResponse($medicos);       
    }
}