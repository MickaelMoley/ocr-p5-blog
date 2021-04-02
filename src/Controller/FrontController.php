<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Root\Core\Controller;

class FrontController extends Controller
{


    /*
     * Fonction permettant d'afficher la page d'accueil
     */
    public function index()
    {

        $em = $this->get('entityManager');

        $posts = $em->getRepository(Post::class)->findAll();

        return $this->render('front/index.html.twig');


    }

    /**
     * Fonction permettant d'afficher la liste des posts
     * @param null $params
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPost($params = null)
    {


        $em = $this->get('entityManager');
        $posts = $em->getRepository(Post::class)->findAll();

        return $this->render('front/post/list.html.twig', ['posts' => $posts]);
    }

    /**
     * Fonction permettant d'afficher un article
     * @param null $params
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function showPost($params = null)
    {

        $em = $this->get('entityManager');
        $post = $em->getRepository(Post::class)->find($params);
        $comments = $em->getRepository(Comment::class)->findPublishComments($post);

        return $this->render('front/post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
        ]);
    }

    /**
     * Fonction permettant de créer un commentaire
     * @param null $params
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createComment($params = null)
    {

        $em = $this->get('entityManager');
        $post = $em->getRepository(Post::class)->find($params);
        $user = $this->getUser();


        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setPost($post);
        $comment->setMessage($this->request->request->get('form')['message']);
        $comment->setCreatedAt(new \DateTime('now'));
        $comment->setStatus(0);

        $em->persist($comment, $user);
        $em->flush();


        return $this->redirectToRoute('show_front_post',  ['id' => $post->getId()]);
    }

    /**
     * Fonction permettant d'afficher la page Contact et d'envoyer un mail
     * @param null $params
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contact($params = null)
    {

        $data = $this->request->request->get('form');
        $status = [];

        if(isset($data)){
            $mail = $this->get('mailer');
            $to      = 'mickaaos440@gmail.com';
            $subject = 'Demande de contact';
            $message = $data['message'];
            $replyTo = $data['mail'];


            try {
                //Parametre serveur
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'mickaaos440@gmail.com  ';
                $mail->Password   = 'Hallucinations0617!Gek';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                //Recipients
                $mail->setFrom('contact@blogocr.fr', 'Formulaire de contact BlogOCR');
                $mail->addAddress('mickael.moley@outlook.com');
                $mail->addReplyTo($replyTo, $data['name']); // L'utilisateur qui a soumis le commentaire


                //Content
                $mail->isHTML(true);    //On envoie le mail au format HTML
                $mail->Subject = 'Demande de contact';
                $mail->Body    = $this->render('emails/contact.html.twig', [
                    'data' => $data
                ]);

                $mail->send();
                return $this->render('front/contact.html.twig', ['flash' => ['status' => 'success', 'message' => 'Votre demande de contact a bien été transmis.']]);
            } catch (Exception $e) {
                return $this->render('front/contact.html.twig', ['flash' => ['status' => 'error', 'message' => "Une erreur s'est produite."]]);
            }
        }

        return $this->render('front/contact.html.twig', $status);
    }
}