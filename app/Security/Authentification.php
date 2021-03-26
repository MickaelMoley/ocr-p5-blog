<?php


namespace Root\Security;


use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Authentification
{

    public static function login($email, $password, $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(array('email' => $email, 'password' => $password));

        if($user)
        {
            $_SESSION['user'] = $user;
            return true;
        }
        else {
            return false;
        }
    }

    public static function checkAccess($router)
    {

        if(!$_SESSION['user'])
        {
            return new RedirectResponse($router->generate('login_page'));
        }


        if ($_SESSION['user'] && !in_array('ROLE_COLLABORATOR', $_SESSION['user']->getRoles()))
        {
            return new Response("Vous n'êtes pas autorisé à accéder à cette page.");
        }
    }

}