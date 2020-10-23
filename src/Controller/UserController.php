<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    /**
     * Formulaire créant un user
     * @Route("/membre/inscription",name="user_create",methods={"GET|POST"})
     * @param Request $request
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $encoder)
    {
        #1.creation d'un objet User
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        #2.Création du formulaire
        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            #3.TODO:Encodage du mdp
            $user->setPassword(
                $encoder->encodePassword($user, $user->getPassword())
            );
            #4.TODO:Sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            #5.TODO:Notification flash
            $this->addFlash('notice', 'Felicitation pourvotre inscription!');

            #6.TODO:Redirection FIXME (modifier l'Url vers une page de connexion)
            return $this->redirectToRoute('index');
        }
        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}