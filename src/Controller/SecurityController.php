<?php
//src/Controller/SecurityController.php
namespace App\Controller;


use App\Entity\User;
use App\Form\RegisterType;

use App\Repository\UserRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\DateTime;

class SecurityController extends Controller
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;

    }

    /**
     * @Route("/login", name="snowtrick_security_login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils): Response
    {

        return $this->render('security/login.html.twig', [
            'lastUsername' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError(),
            'controller_name' => 'SecurityController',
        ]);
    }



    /**
     *
     * @Route("/register", name="snowtrick_security_register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {

//       $fullName, $username, $password, $email, $roles

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setRoles(array('ROLE_ADMIN'));
            if (null !== $user){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre compte a bien été créé, vous devez confirmer via le message qui vous a été envoyé par email.');
            return $this->redirectToRoute('snowtrick_security_login');

            }

        }
        return $this->render('security/_register_form.html.twig', ['form' => $form->createView()]);

    }





    /**
     * @Route("/logout", name="snowtrick_security_logout")
     * @throws Exception
     */
    public function logout(): void
    {
        throw new Exception('Cette page est interdite !');
    }
}
