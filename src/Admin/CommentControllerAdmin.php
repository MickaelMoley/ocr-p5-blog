<?php


namespace App\Admin;


use App\Entity\Comment;
use App\Entity\Post;
use Root\Core\Controller;
use Symfony\Component\HttpFoundation\Response;

class CommentControllerAdmin extends Controller
{

    private $manager;


    /**
     * Fonction permettant de lister les commentaires
     * @param null $params
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list($id = null)
    {
        if($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COLLABORATOR')) {
            $entityManager = $this->get('entityManager');
            $comments = $entityManager->getRepository(Comment::class)->findAll();


            return $this->render('dashboard/comment/list.html.twig', [
                'comments' => $comments,
            ]);
        }
        return new Response('Accès non autorisé', 500);

    }

    /*
     * Fonction permettant de créer un commentaire
     */
    public function create($id = null)
    {
        if($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COLLABORATOR')) {
            $entityManager = $this->get('entityManager');
            $comment = new Comment();


            $user = $this->getUser();
            $post = $entityManager->getRepository(Post::class)->find($id);
            $comment->setPost($post);
            $comment->setAuthor($user);


            $form = $this->createForm(Comment::class, $comment);

            $form->handleRequest($this->request);

            if($form->isSubmitted())
            {
                $data = $form->getData();
                $requestData = $form->getRequestData();
                $data->setCreatedAt(new \DateTime('now'));

                $entityManager->persist($data);
                $entityManager->flush();

                return $this->redirectToRoute('admin_comment_edit',  ['id' => $data->getId()]);
            }
            return $this->render('dashboard/comment/create.html.twig', [
                'form' => $form->createView(),
                'route' => $this->get('router')->match(),
            ]);
        }
        else {
            return $this->redirectToRoute('login_page');
        }
    }

    /*
     * Function permettant de modifier un commentaire.
     */
    public function edit($id = null)
    {
        if($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COLLABORATOR')) {
            $entityManager = $this->get('entityManager');
            $comment = $entityManager->getRepository(Comment::class)->find($id);

            $user = $this->getUser();

            $form = $this->createForm(Comment::class, $comment);

            $form->handleRequest($this->request);

            if($form->isSubmitted())
            {
                $data = $form->getData();
                $requestData = $form->getRequestData();

                $entityManager->persist($data);
                $entityManager->flush();

                return $this->redirectToRoute('admin_comment_edit',  ['id' => $data->getId()]);
            }
            return $this->render('dashboard/comment/edit.html.twig', [
                'form' => $form->createView(),
                'route' => $this->get('router')->match(),
            ]);
        }
        else {
            return $this->redirectToRoute('login_page');
        }
    }

    /*
     * Function permettant de supprimer un commentaire
     *
     */
    public function delete($id = null)
    {

        if(isset($id))
        {
            $entityManager = $this->get('entityManager');

            $comment = $entityManager->getRepository(Comment::class)->find($id);

            if($comment)
            {
                $entityManager->remove($comment);
                $entityManager->flush();
                return $this->redirectToRoute('admin_comment_list');
            }
            else {
                if($this->getEnvironment() === 'dev')
                {
                    return new Response("Aucun commentaire n'a été trouvée avec le paramètre : $id",500);
                }
                else {
                    return new Response(null, 500);
                }
            }
        }

        if($this->getEnvironment() === 'dev')
        {
            return new Response("Aucun paramètre n'a été défini pour la suppresion d'un commentaire.",500);
        }
        else {
            return new Response(null, 500);
        }
    }
}