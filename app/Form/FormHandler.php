<?php


namespace Root\Form;


class FormHandler
{
    private $class;
    private $data;
    private $isSubmitted = false;
    private $newData;

    public function __construct($class, $data)
    {
        $this->class = $class;
        $this->data = $data;
        $this->newData = null;
        return $this;
    }

    /**
     * Fonction permettant de retourner l'entité actuel
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Fonction permettant de retourner les nouvelles données
     * @return null
     */
    public function getRequestData()
    {
        return $this->newData;
    }

    /**
     * Fonction permettant de gérer les données d'une entité en les récupérant
     * depuis la requête et retournant une entité avec les nouvelles données.
     * @param $request
     */
    public function handleRequest($request)
    {
        $data = $request->request->get('form');
        /**
         * On récupère les propriétés de la classe
         */
        $objectProperties = (new \ReflectionObject(new $this->class))->getProperties();

        foreach ($objectProperties as $key => $property) {
            // On défini la propriété en public pour pouvoir y accéder
            $property->setAccessible(true);
            if(isset($data[$property->getName()]) AND $data[$property->getName()] !== null)
            {
                //On appelle la fonction de la classe et on défini la nouvelle valeur
                $this->data->{sprintf('set%s', ucfirst($property->getName()))}($data[$property->getName()]);
            }

        }
        $this->newData = $data;

        /**
         * S'il détecte que le bouton attribut HTML [name='btn_submit']
         * alors il y a eu une soumission des données, on passe la variable à TRUE
         */
        if($request->request->get('btn_submit') !== null)
        {
            $this->isSubmitted = true;
        }

    }

    /**
     * Fonction permettant de retourner l'entité actuel
     * pour les afficher dans la vue
     * @return mixed
     */
    public function createView()
    {
        return $this->data;
    }

    /*
     * Fonction qui permet de vérifier qu'un formulaire a été soumis
     */
    public function isSubmitted(): bool
    {
        return $this->isSubmitted;
    }
}