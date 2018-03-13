<?php
//src/Controller/BlogController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;

use App\Entity\Image;
use App\Entity\Photo;
use App\Entity\User;
use App\Events;
use App\Form\CommentType;
use App\Form\ImageType;
use App\Form\RegisterType;
use App\Repository\FigureRepository;
use App\Repository\UserRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/", defaults={"snowtrick_homepage"})
 * Class BlogController
 * @package App\Controller
 */
class BlogController extends AbstractController {
    /**
     * @Route("/", defaults={"_format"="html"}, name="snowtrick_blog_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function index(string $_format): Response
    {

        return $this->render('blog/index.'.$_format.'.twig');

    }

    /**
     * @Route("/figures", defaults={"page": "1", "_format"="html"}, name="snowtrick_blog_figures")
     * @Route("/page/{page}", defaults={"_format"="html"}, requirements={"page": "[1-9]\d*"}, name="snowtrick_blog_index_paginated")
     * @Method("GET")
     * @param int $page
     * @param string $_format
     * @param FigureRepository $figures
     * @return Response
     */
    public function figureAll(int $page, string $_format, FigureRepository $figures):Response {
        $latestFigures = $figures->findLatest($page);
        return $this->render('/blog/figures.'.$_format.'.twig', ['figures' => $latestFigures]);
    }

    /**
     * @Route("/figures/{slug}", name="snowtrick_blog_figure")
     * @Method("GET")
     * @param Figure $figure
     * @return Response
     */
    public function figureShow(Figure $figure) : Response
    {

        return $this->render('blog/figure_show.html.twig', ['figure' => $figure]);
    }

    /**
     * @Route("/comment/{figureSlug}/new", name="snowtrick_comment_new")
     * @Method({"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @ParamConverter("figure", options={"mapping": {"figureSlug":"slug"}})
     * @param Request $request
     * @param Figure $figure
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function commentNew(Request $request, Figure $figure, EventDispatcherInterface $eventDispatcher): Response
    {
        $comment = new Comment();
        $user = $this->getUser();
        $comment->setAuthor($user);
//        $comment->setPhoto($user->getPhoto());
        $figure->addComment($comment);

        // je crée ensuite le formulaire et j'y insert les commentaires
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            // When triggering an event, you can optionally pass some information.
            // For simple applications, use the GenericEvent object provided by Symfony
            // to pass some PHP variables. For more complex applications, define your
            // own event object classes.
            // See https://symfony.com/doc/current/components/event_dispatcher/generic_event.html

            $event = new GenericEvent($comment);

            // When an event is dispatched, Symfony notifies it to all the listeners
            // and subscribers registered to it. Listeners can modify the information
            // passed in the event and they can even modify the execution flow, so
            // there's no guarantee that the rest of this controller will be executed.
            // See https://symfony.com/doc/current/components/event_dispatcher.html


            $eventDispatcher->dispatch(Events::COMMENT_CREATED, $event);
            $this->addFlash('success','Votre commentaire à été posté avec succès, a doit être modéré.');
            return $this->redirectToRoute('snowtrick_blog_figure', ['slug' => $figure->getSlug()]);
        }

       return $this->render('blog/comment_form_error.html.twig', [
           'figure' => $figure,
           'form' => $form->createView(),
       ]);

    }

    public function commentForm(Figure $figure){
        $form = $this->createForm(CommentType::class);

        return $this->render('blog/_comment_form.html.twig', [
            'figure' => $figure,
            'form' => $form->createView()
        ]);
    }



}