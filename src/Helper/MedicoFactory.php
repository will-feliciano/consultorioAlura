<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory{

    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository){
        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function criarMedico(string $json){

        $doctorRec = json_decode($json);
        $especId = $doctorRec->especialidadeId;
        $especialidade = $this->especialidadeRepository->find($especId);
            
        $novoMedico = new Medico();
        $novoMedico->setCrm($doctorRec->crm)->setNome($doctorRec->nome)->setEspecialidade($especialidade);

        return $novoMedico;
    }
}