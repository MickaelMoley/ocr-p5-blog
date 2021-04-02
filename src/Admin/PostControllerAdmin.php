<?php
namespace App\Admin;

use App\Entity\Post;
use App\Entity\User;
use Root\Core\Controller;
use Symfony\Component\HttpFoundation\Response;

class PostControllerAdmin extends Controller
{
    /*
     * Fonction qui permet de récupérer la liste des articles
     * et afficher la vue
     */
    public function list($id = null)
    {
        if($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COLLABORATOR')) {
            $em = $this->get('entityManager');
            $posts = $em->getRepository(Post::class)->findAll();

            return $this->render('dashboard/post/list.html.twig', [
                'posts' => $posts,
            ]);
        }
        return new Response('Accès non autorisé', 500);
    }

    /*
     * Permet de créer un article
     * et le sauvegarder
     */
    public function create($id = null)
    {

        if($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COLLABORATOR'))
        {
            $em = $this->get('entityManager');
            $post = new Post();
            $users = $em->getRepository(User::class)->findAll();


            $form = $this->createForm(Post::class, $post);

            $form->handleRequest($this->request);

            $user = $this->getUser();

            if($form->isSubmitted())
            {
                $data = $form->getData();
                $requestData = $form->getRequestData();

                $data->setCreatedAt(new \DateTime('now'));
                $data->setUpdatedAt(new \DateTime('now'));

                //On défini l'utilisateur courant ou l'utilisateur attribué
                if($requestData['author'] === $user->getId())
                {
                    $data->setAuthor($user);
                }
                else {
                    $user = $em->getRepository(User::class)->find($requestData['author']);
                    $data->setAuthor($user);
                }


                $em->persist($data);
                $em->flush();
                return $this->redirectToRoute('admin_post_edit',  ['id' => $data->getId()]);
            }

            return $this->render('dashboard/post/create.html.twig', [
                'users' => $users
            ]);
        }
        else {
            return $this->redirectToRoute('login_page');
        }
    }

    /*
     * Permet de récupérer et modifier un article
     * Et le sauvegarder s'il y a des changements
     */
    public function edit($id = null)
    {

        if($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COLLABORATOR'))
        {
            $em = $this->get('entityManager');
            $post = $em->getRepository(Post::class)->find($id);

            $user = $this->getUser();
            $users = $em->getRepository(User::class)->findAll();

            $form = $this->createForm(Post::class, $post);

            $form->handleRequest($this->request);

            if($form->isSubmitted())
            {
                $data = $form->getData();
                $requestData = $form->getRequestData();

                $data->setUpdatedAt(new \DateTime('now'));

                //On défini l'utilisateur courant ou l'utilisateur attribué*
                if(isset($requestData['author']) && !is_null($requestData['author']))
                {
                    if($requestData['author'] === $user->getId())
                    {
                        $data->setAuthor($user);
                    }
                    else {
                        $user = $em->getRepository(User::class)->find($requestData['author']);
                        $data->setAuthor($user);
                    }
                }


                $em->persist($data);
                $em->flush();

                return $this->redirectToRoute('admin_post_edit',  ['id' => $data->getId()]);
            }
            return $this->render('dashboard/post/edit.html.twig', [
                'form' => $form->createView(),
                'route' => $this->get('router')->match(),
                'users' => $users
            ]);
        }
        else {
            return $this->redirectToRoute('login_page');
        }
    }

    /*
     * Permet de supprimer un article
     */
    public function delete($id = null)
    {
        if(isset($id))
        {
            $em = $this->get('entityManager');

            $post = $em->getRepository(Post::class)->find($id);

            if($post)
            {
                $em->remove($post);
                $em->flush();
            }
            else {
                if($this->getEnvironment() === 'dev')
                {
                    return new Response("Aucune article n'a été trouvée avec le paramètre : $id",500);
                }
                else {
                    return new Response(null, 500);
                }
            }

            return $this->redirectToRoute('admin_post_list');
        }

        if($this->getEnvironment() === 'dev')
        {
            return new Response("Aucun paramètre n'a été défini pour la suppresion de l'article.",500);
        }
        else {
            return new Response(null, 500);
        }
    }
}