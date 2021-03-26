<?php


namespace App\Admin;

use App\Entity\User;
use Root\Core\Controller;
use Root\Security\Authentification;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /*
     * Fonction permettant de créer un compte
     */
    public function register()
    {
        $em = $this->get('entityManager');
        $user = new User();
        $user->setRoles('ROLE_USER');


        $form = $this->createForm(User::class, $user);

        $form->handleRequest($this->request);

        if($form->isSubmitted())
        {
            $data = $form->getData();

            if(!$this->checkIfExist($data))
            {
                $em->persist($data);
                $em->flush();
                return $this->redirectToRoute('login_page');
            }
            else {
                return $this->render('security/register.html.twig', [
                    'error' => 'Un utilisateur existe déjà.'
                ]);
            }

        }

        return $this->render('security/register.html.twig');
    }

    /**
     * Fonction permettant de s'authentifier avec ses identifiants
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function login()
    {

        if($this->getUser())
        {
            if($this->isGranted('ROLE_USER'))
            {
                //TODO: On redirigera vers la page blog
                return new Response('Page blog ', 500);
            }
            else {
                //TODO: On redirigera vers la page administration
                return new Response('Page administration', 500);
            }
        }
        else {

            $data = $this->request->request->get('form');

            if(isset($data['email']) && $data['password'])
            {
                if(Authentification::login($data['email'], $data['password'], $this->get('entityManager')))
                {

                    return $this->redirectToRoute('login_page');

                }
                else {
                    return $this->render('security/login.html.twig', ['error' => 'Veuillez vérifier vos identifiants.']);
                }
            }
        }

        return $this->render('security/login.html.twig');
    }

    /**
     * Fonction permettant de vérifier si l'utilisateur existe
     * @param $user
     * @return bool
     */
    private function checkIfExist($user)
    {
        $user_row = $this->get('entityManager')->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

        return isset($user_row);
    }

    /**
     * Fonction permettant de se déconnecter sur un compte
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function logout()
    {
        unset($_SESSION['user']);

        return $this->redirectToRoute('login_page');
    }
}