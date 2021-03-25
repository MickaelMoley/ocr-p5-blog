<?php
namespace Root\Core;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Controller
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;
    protected $dependencies;

    public function __construct($dependencies, Request $request, Response $response)
    {
        $this->dependencies = $dependencies;
        $this->request = $request;
        $this->response = $response;
    }

    public function getEnvironment()
    {
        return $this->dependencies['env'];
    }

    /**
     * Fonction permettant injecter les données au template afin de créer un vue
     * Retourne $content SI existe et VALIDE
     * Retourne des messages d'erreur SI erreur
     * @param $template
     * @param null $parameter
     */
    public function render($template, $parameter = null): Response
    {
        try {
            $content = $this->get('twig')->render($template, $parameter);
        }
        catch (LoaderError | RuntimeError | SyntaxError $exception)
        {
            if($this->getEnvironment() === 'dev')
            {
                return new Response(\sprintf('[%s] : %s',$exception->getCode(),$exception->getMessage()), 500);
            }
            else {
                return new Response(null, 500);
            }
        }
        catch (Exception $exception)
        {
            return new Response($exception->getMessage(), 500);
        }
        return $content;
    }

    /*
     * Fonction qui permet de récupérer une librarie
     */
    public function get($name)
    {
        if(array_key_exists($name, $this->dependencies))
        {
            return $this->dependencies[$name];
        }
        else {
            if($this->getEnvironment() === 'dev')
            {
                throw new Exception("Cette dépendence '$name' n'existe pas dans la liste. Voir le fichier 'Controller' ");
            }
            //Je retourne une Exception si la librairie n'existe pas.
            throw new Exception(null);
        }
    }
}