<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/user/register", name="register")
     */
    public function register(Request $requete, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user= new User();
        $formRegister = $this->createForm(RegisterType::class, $user);
        $formRegister->handleRequest($requete);
        if($formRegister->isSubmitted() && $formRegister->isValid()){

            $hashedPassword = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('login');
        }
        return $this->render('user/register.html.twig', ['formRegister'=> $formRegister->createView()]);

    }
    /**
     * @Route("user/login", name="login")
     */
    public function login(): Response{

        return $this->render('user/login.html.twig');
    }
    /**
     * @Route("user/logout", name="logout")
     */
    public function logout(): Response{
        return $this->redirectToRoute('login');
    }
}
