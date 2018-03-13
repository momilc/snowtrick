<?php
//src/Controller/Admin/BlogController.php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Style;
use App\Entity\User;
use App\Events;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Form\PostType;
use App\Form\RegisterType;
use App\Repository\FigureRepository;
use App\Repository\StyleRepository;
use App\Repository\UserRepository;
use App\Utils\Slugger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/admin/figure")
 * @Security("has_role('ROLE_ADMIN')")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/", name="snowtrick_admin_index")
     * @Route("/", name="snowtrick_admin_figure_index")
     * @Method("GET")
     * @param FigureRepository $figures
     * @return Response
     */
    public function index(FigureRepository $figures): Response
    {
        $allFigures = $figures->findAll();

        return $this->render('admin/blog/index.html.twig', ['figures' => $allFigures]);
    }

//    /**
//     * @Route("/admin")
//     * @param AuthenticationUtils $utils
//     * @param UserRepository $repository
//     * @return Response
//     */
//    public function admin(AuthenticationUtils $utils, UserRepository $repository): Response {
//
//        if ($this->getUser()->getProfile() !== null){
//            $user = $repository->find($this->getUser()->getId());
//            echo '<img src="', $user->getProfile(),'"alt="',$user->getFullName(),''.$user->getFullName().'\'s Profile Image">';
//
////            return $this->render('security/profile.html.twig', [
////                'lastUsername' => $utils->getLastUsername(),
////                'error' => $utils->getLastAuthenticationError(),
////            ]);
//        }
//
//        return $this->render('security/admin.html.twig', [
//            'user' => $this->getUser(),
//            'lastUsername' => $utils->getLastUsername(),
//            'error' => $utils->getLastAuthenticationError(),
//
//        ]);
//
//    }

//    /**
//     * @Route("/admin", name="snowtrick_admin")
//     * @param AuthenticationUtils $utils
//     * @param TokenInterface $token
//     * @param SessionBagInterface $sessionBag
//     * @return Response
//     */
//    public function admin(AuthenticationUtils $utils, TokenInterface $token, SessionBagInterface $sessionBag): Response {
//        $user = $token->getUser();
//
//        $userLogged = $this->isCsrfTokenValid($user->getId(), $user);
//
//        if ($userLogged && $user->getProfile() !== null){
//
//            echo '<img src="',$user->getProfile,'"alt="',$user->getFullName(),'x\'s Profile Image">';
//
////            return $this->render('security/profile.html.twig', [
////                'lastUsername' => $utils->getLastUsername(),
////                'error' => $utils->getLastAuthenticationError(),
////            ]);
//        }
//
//        return $this->render('security/admin.html.twig', [
//            'lastUsername' => $utils->getLastUsername(),
//            'error' => $utils->getLastAuthenticationError(),
//
//        ]);
//
//    }

    /**
     * @Route("/{id}", requirements={"id":"\d+"}, name="snowtrick_admin_figure_show")
     * @param Figure $figure
     * @return Response
     */
    public function show(Figure $figure): Response
    {
        return $this->render('admin/blog/show.html.twig', ['figure' => $figure]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id": "\d+"} ,name="snowtrick_admin_figure_edit")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param Figure $figure
     * @return Response
     */
    public function edit(Request $request, Figure $figure): Response
    {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        // Valider l'envoi du formulaire et son contenu
        if ($form->isSubmitted() && $form->isValid()){

            // je renseigne le slug
            $figure->setSlug(Slugger::slugify($figure->getTitle()));
            // On enregistre tout cela en base avec un flush
            $this->getDoctrine()->getManager()->flush();
            // On affiche un message Flash
            $this->addFlash('success', 'figure.updated_successfully');
            // Redirection à la fin du process en précisant l'ID de la figure postée.
            return $this->redirectToRoute('snowtrick_admin_figure_edit', ['id' => $figure->getId()]);
        }

        return $this->render('admin/blog/edit.html.twig', [
            'form' => $form->createView(),
            'figure' => $figure
        ]);
    }

    /**
     * @Route("/new", name="snowtrick_admin_figure_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function new(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
        $figure = new Figure();
        $figure->setAuthor($this->getUser());

        // On prépare le formulaire
        $form = $this
            ->createForm(FigureType::class, $figure)
            // On sauvegarde la nouvelle figure
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On crée le slug avec le titre de la nouvelle figure
            $figure->setSlug(Slugger::slugify($figure->getTitle()));
            // On sauvegarde en base en persistant car c'est un nouvel ajout
            $em = $this->getDoctrine()->getManager();
            $em->persist($figure);
            $em->flush();

            //Envoyer un email !

            $event = new GenericEvent($figure);

            // When an event is dispatched, Symfony notifies it to all the listeners
            // and subscribers registered to it. Listeners can modify the information
            // passed in the event and they can even modify the execution flow, so
            // there's no guarantee that the rest of this controller will be executed.
            // See https://symfony.com/doc/current/components/event_dispatcher.html


            $eventDispatcher->dispatch(Events::COMMENT_CREATED, $event);


            // On affiche le message
            $this->addFlash('success', 'figure.created_successfully');
            // On vérifie le tout avant de faire une redirection
            if ($form->get('saveAndCreateNew')->isClicked()){

                return $this->redirectToRoute('snowtrick_admin_figure_new');
            }

            return $this->redirectToRoute('snowtrick_admin_figure_index');
        }

        return $this->render('admin/blog/new.html.twig', [
            'form' => $form->createView(),
            'figure' => $figure
        ]);

    }

    public function registerForm(User $user){
        $form = $this->createForm(RegisterType::class);

        return $this->render('blog/_register_form.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/{id}/delete", name="snowtrick_admin_figure_delete")
     * @Method({"POST"})
     * @Security("is_granted('delete', figure)")
     * @param Request $request
     * @param Figure $figure
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Figure $figure): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))){
            return $this->redirectToRoute('snowtrick_figure_index');
        }
        //Suppression des Tags
        $figure->getTags()->clear();
        //Suppression en BDD
        $em = $this->getDoctrine()->getManager();
        $em->remove($figure);
        $em->flush();

        // Message Flash
        $this->addFlash('success', 'figure.deleted_successfully');
        // Redirection vers la liste des figures
        return $this->redirectToRoute('snowtrick_admin_figure_index');
    }

}