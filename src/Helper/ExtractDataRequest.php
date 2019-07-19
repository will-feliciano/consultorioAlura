<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class ExtractDataRequest{

    private function getData(Request $request){
        
        $allString = $request->query->all();

        $infOrder = array_key_exists('sort', $allString) ? $allString['sort'] : null;
        unset($allString['sort']);

        $infPage = array_key_exists('page', $allString) ? $allString['page'] : 1;
        unset($allString['page']);

        $infQtdePage = array_key_exists('qtde', $allString) ? $allString['qtde'] : 2;
        unset($allString['qtde']);

        return [$allString, $infOrder, $infPage, $infQtdePage];
    }
    
    public function getDataOrder(Request $req){
        [ ,$dataOrder] = $this->getData($req);
        return $dataOrder;
    }

    public function getDataFilter(Request $req){
        [$dataFilter, ] = $this->getData($req);
        return $dataFilter;
    }

    public function getQtdePages(Request $req){
        [ , , $dataPage, $dataQtde] = $this->getData($req);
        return [$dataPage, $dataQtde];
    }
}
