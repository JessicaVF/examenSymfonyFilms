<?php

namespace App\Controller;
use App\Entity\Film;
use App\Entity\Impression;
use App\Form\ImpressionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImpressionController extends AbstractController
{
    /**
     * @Route("/impression", name="impression")
     */
    public function index(): Response
    {
        return $this->render('impression/index.html.twig', [
            'controller_name' => 'ImpressionController',
        ]);
    }
    /**
     *
     * @Route("impression/edit/{id}", name="editImpression")
     */
    public function edit(Impression $impression, Request $requete, EntityManagerInterface $manager):Response
    {

       $formImpression = $this->createForm(ImpressionType::class, $impression);
        $formImpression->handleRequest($requete);

        if($formImpression->isSubmitted() && $formImpression->isValid())
        {


            $impression->setDateCreation(new \DateTime());
            $manager->persist($impression);
            $manager->flush();

                return $this->redirectToRoute('showFilm', [
                    'id' => $impression->getFilm()->getId(),
                ]);

//            return $this->redirectToRoute('film');
        }
        return $this->render('impression/edit.html.twig', ['formImpression'=> $formImpression->createView()]);
    }

    /**
     *
     * @Route("impression/delete/{id}", name="deleteImpression")
     */
    public function delete(Impression $impression, EntityManagerInterface $manager): Response
    {
        $manager->remove($impression);
        $manager->flush();
        return $this->redirect('/film');
    }
}
