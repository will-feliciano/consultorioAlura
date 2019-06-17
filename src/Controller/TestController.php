<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class TestController{

    /**
     * @Route ("/tst")
     */

    public function inicialAction(Request $request): Response{
        return new JsonResponse(['mensagem' => 'qualquer coisa']);
    }
}


