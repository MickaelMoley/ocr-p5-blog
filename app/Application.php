<?php
namespace Root;

use Symfony\Component\HttpFoundation\Request;

class Application
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Cette fonction nous permet de charger les différentes configurations de notre application et rediriger la requête vers le bon controleur
     * grâce au router
     */
    public function process(){

        return $this;
    }
    /**
     * Cette fonction nous permet de retourner le traitement du controleur à l'utilisateur
     */
    public function send(){}
}