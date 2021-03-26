<?php
namespace Root\Core;

use Exception;
use Root\Form\FormHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    public function __construct($dependencies, Request $request)
    {
        $this->dependencies = $dependencies;
        $this->request = $request;
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
    public function render($template, $parameter = [])
    {
        try {$this->get('twig')->addGlobal('user', $this->getUser());

            $content = $this->get('twig')->render($template, $parameter);
            $response = new Response();
            $response->setContent($content);
            $response->setStatusCode(200);
            return $response;
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

    /**
     * Fonction permettant de gérer les données du formulaire
     * @param $class
     * @param $data
     * @return FormHandler
     */
    public function createForm($class, $data)
    {
        return new FormHandler($class, $data);
    }

    /**
     * Fonction permettant de rediriger l'utilisateur vers une nouvelle page
     * @param $name
     * @param array $params
     * @return Response
     */
    public function redirectToRoute($name, $params = [])
    {
        try {
            return new RedirectResponse($this->get('router')->generate($name, $params));
        } catch (\Exception $e) {
            if($this->getEnvironment() === 'dev'){
                return new Response($e->getMessage(), 500);
            }
            else {
                return new Response(null, 500);
            }
        }
    }

    /**
     * Fonction permettant de récupérer l'utilisateur courant depuis la session
     * @return mixed|null
     */
    public function getUser()
    {
        if(isset($_SESSION['user']))
        {
            $this->get('twig')->addGlobal('user', $_SESSION['user']);
            return $_SESSION['user'];
        }
        return null;
    }

    /**
     * Fonction permettant de vérifier
     * que l'utilisateur ait les droits nécessaire afin d'accéder à une page
     * @param string $role
     * @return bool|null
     */
    public function isGranted(string $role)
    {
        if($this->getUser())
        {
            return in_array($role, $this->getUser()->getRoles());
        }
        else {
            return null;
        }
    }


}