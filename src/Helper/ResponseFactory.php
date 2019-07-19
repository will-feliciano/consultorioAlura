<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFactory{

    /**
     * @var bool
     */
    private $sucesso;

    /**
     * @var int
     */
    private $paginaAtual;
    
    /**
     * @var int
     */
    private $itensPorPagina;
    
    public function __construct(
        bool $sucesso,
        $conteudoResponse,
        int $statusResponse = Response::HTTP_OK,
        int $paginaAtual = null,
        int $itensPorPagina = null        
    ) {
        $this->sucesso = $sucesso;
        $this->paginaAtual = $paginaAtual;
        $this->statusResponse = $statusResponse;
        $this->itensPorPagina = $itensPorPagina;
        $this->conteudoResponse = $conteudoResponse;
    }

    public function getResponse(): JsonResponse{
        $resposta = [
            'sucesso' => $this->sucesso,
            'pgAtual' => $this->paginaAtual,
            'itensPorPagina' => $this->itensPorPagina,
            'conteudo' => $this->conteudoResponse
        ];

        if(is_null($this->paginaAtual)){
            unset($resposta['pgAtual']);
            unset($resposta['itensPorPagina']);
        }

        return new JsonResponse($resposta, $this->statusResponse);
    }

}