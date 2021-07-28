<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\FilmType;
use App\Form\ImpressionType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    /**
     * @Route("/film", name="film")
     */
    public function index(FilmRepository $repository): Response
    {
        $films = $repository->findAll();

        return $this->render('film/index.html.twig', [
            'films' => $films,
        ]);
    }
    /**
     * @Route("film/show/{id}", name="showFilm", requirements={"id"="\d+"})
     */
    public function show(Film $film = null, Request $requete, EntityManagerInterface $manager): Response{

        $impressions = $film->getImpressions();

        //Impressions add
        $impression = new Impression;
        $formImpression = $this->createForm(ImpressionType::class, $impression);
        $formImpression->handleRequest($requete);

        if($formImpression->isSubmitted() && $formImpression->isValid())
        {
            $impression = $formImpression->getData();
            $impression->setDateCreation(new \DateTime());
            $impression->setFilm($film);
            $manager->persist($impression);
            $manager->flush();
        }

        return $this->render('film/show.html.twig', [
            'film' => $film, 'impressions'=>$impressions, 'formImpression'=> $formImpression->createView()
        ]);

    }
    /**
     * @Route("film/add", name="addFilm")
     * @Route("film/edit/{id}", name="editFilm")
     */
    public function add(Film $film = null, Request $requete, EntityManagerInterface $manager):Response
    {

        if(!$film){
            $film = new Film;
            $modeEdition =false;
        }
        else{
            $modeEdition= true;
        }

        $formFilm = $this->createForm(FilmType::class, $film);
        $formFilm->handleRequest($requete);

        if($formFilm->isSubmitted() && $formFilm->isValid())
        {
            if(!$modeEdition){

                $film->setAnneeSortie(new \DateTime());

            }
            $manager->persist($film);
            $manager->flush();
            if($modeEdition){
                return $this->redirectToRoute('showFilm', [
                    'id' => $film->getId(),
                ]);
            }
            return $this->redirectToRoute('film');
        }
        return $this->render('film/edit.html.twig', ['formFilm'=> $formFilm->createView(), 'film'=> $film, 'modeEdition'=> $modeEdition]);
    }
    /**
     *
     * @Route("film/delete/{id}", name="deleteFilm")
     */
    public function delete(Film $film, EntityManagerInterface $manager): Response
    {
        $manager->remove($film);
        $manager->flush();
        return $this->redirect('/film');
    }
}
