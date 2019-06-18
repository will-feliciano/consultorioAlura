<?php

namespace App\Helper;

use App\Entity\Medico;

class MedicoFactory{

    public function criarMedico(string $json){

        $doctorRec = json_decode($json);
            
        $novoMedico = new Medico();
        $novoMedico->crm = $doctorRec->crm;
        $novoMedico->name = $doctorRec->nome;

        return $novoMedico;
    }
}