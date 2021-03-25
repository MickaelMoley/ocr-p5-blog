<?php
namespace Root;

use Symfony\Component\HttpFoundation\Request;
use AltoRouter;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Application
{
    private $request;
    private $response;

    private $entityManager;

    private $router;
    private $config;

    private $dependencies = [];

    public function __construct(Request $request = null)
    {
        // S'il y a une requête alors on le traite
        if($request)
        {
            $this->request = $request;
            $this->response = new Response();
        }

        $this->config = yaml_parse_file($this->getRootDir().'/env.yaml')['config'];
    }

    /**
     * Cette fonction nous permet de charger les différentes configurations de notre application et rediriger la requête vers le bon controleur
     * grâce au router
     */
    public function process()
    {
        $this->router = new AltoRouter();
        $this->loadRoutesConfiguration($this->getRootDir().'/routes.yaml');
        $this->loadEntityManagerConfiguration();

        $match = $this->router->match();

        if($match === false)
        {
            // S'il n'y a pas de match alors on affichera une erreur 404
            $this->response->setContent("Page Not Found");
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
            $this->response->headers->set("Content-Type", "text/html");
        }
        else {
            // S'il y a un match alors on appellera la configuration de la route
            list($controller, $action) = explode('#', $match['target']);

            $controller = new $controller($this->dependencies);

            if(is_callable(array($controller, $action)))
            {
                $content = call_user_func_array(array($controller, $action), $match['params']);

                /**
                 * Si le retour de la fonction est de type "Response" alors on le défini directement à la variable 'self::reponse'
                 * Cas d'utilisation :
                 * - Requête AJAX
                 * - Réponse directe
                 */
                if(gettype($content) === 'object')
                {
                    $this->response = $content;
                }
                /**
                 * Si le retour de la fonction est de type "string", c'est une page
                 * on le défini dans le contenu dans la reponse déjà instanciée
                 * Cas d'utilisation :
                 * - Une page contenant de HTML (twig)
                 */
                else if(gettype($content) === 'string')
                {
                    $this->response->setContent($content);
                }
            }
            /**
             * Dans le cas où les arguments ne peuvent pas être appelés comme une fonction
             * alors on affiche une erreur selon l'environnement 'dev' ou 'prod'
             */
            else {
                // On affiche une page d'erreur avec la description de l'erreur si on est en dev
                if($this->config['app']['env'] === 'dev')
                {
                    $this->response->setContent('Error: cannot call ' . get_class($controller) . '#' . $action);
                    $this->response->setStatusCode(Response::HTTP_NOT_FOUND);

                    $this->response->headers->set('Content-Type', 'text/html');
                }
                else {
                    // On affiche une page d'erreur 500 si on est en prod
                    $this->response->setContent('Erreur 500.');
                    $this->response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                    $this->response->headers->set('Content-Type', 'text/html');
                }
            }
        }


        return $this;
    }
    /**
     * Cette fonction nous permet de retourner le traitement du controleur à l'utilisateur
     */
    public function send(){

        return $this->response->send();
    }

    /**
     * Fonction permettant de charger les routes depuis un fichier de configuration
     * @param $path
     */
    private function loadRoutesConfiguration(string $path)
    {
        $routes = yaml_parse_file($path)['routes'];

        if($routes)
        {
            /**
             * Chaque route dans le fichier de configuration ci-dessus sera ajouté dans notre routeur
             * afin que chaque route puisse effectuer une action lorsqu'un URL sera matché
             */
            foreach ($routes as $route)
            {
                /*
                 * 'method' contient la/les méthode(s) de requête
                 * 'path'   contient l'URL qui devra être matché
                 * 'class'  contient la classe qui devra être appelé si une URL est matché
                 * 'action' contient la fonction qui sera appelé dans la classe si une URL est matché
                 * 'name'   contient le nom de la route
                 */

                $this->router->map(
                    $route['method'],
                    $route['path'],
                    sprintf('%s#%s', $route['class'], $route['action']),
                    $route['name']
                );
            }
        }
    }

    public function loadEntityManagerConfiguration()
    {
        if($this->config['database'] && $this->config['orm'])
        {
            $debug = $this->config['env'] === 'dev'; // Si l'environnement 'dev' est actif, alors on active le mode débuggage
            $entityPath = [];

            //On redéfini le chemin pour les entités
            foreach ($this->config['orm']['entity_path'] as $key => $entity_path)
            {
                $entityPath[$key] =  $this->getRootDir().$entity_path;
            }


            $config = Setup::createAnnotationMetadataConfiguration($entityPath,$debug,null, null, false);
            try {
                return $this->entityManager = EntityManager::create($this->config['database'], $config);
            } catch (\Doctrine\ORM\ORMException $e) {
                echo $e->getMessage();
            }
        }


    }

    /**
     * Fonction qui nous retourne le chemin du dossier courant
     * @return string
     */
    public function getRootDir()
    {
        return __DIR__;
    }

}