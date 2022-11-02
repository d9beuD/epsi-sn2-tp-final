<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtistController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Sélection de l'unique artiste
        $artist = $doctrine->getRepository(Artist::class)->find(1);

        return $this->render('artist/index.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/artist/new', name: 'artist_form', methods: ['GET', 'POST'])]
    public function setArtist(Request $request, ArtistRepository $artistRepository, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $artist = $artistRepository->find(1);


        if ($request->getMethod() === 'POST') {
            // Si l'artiste n'existe pas encore, on le crée. Sinon, on le met à jour
            if ($artist === null) {
                $artist = new Artist();
            }

            $artist->setName($request->request->get('name'))
                ->setIllustrationURL($request->request->get('illustrationURL'))
                ->setDescription($request->request->get('description'));

            $entityManager->persist($artist); // Ne fonctionne que si l'artiste n'existe pas déjà
            $entityManager->flush();

            // On redirige sur la page d'accueil
            return $this->redirectToRoute('home');
        }

        // Affichage du formulaire
        return $this->render('artist/new.html.twig', [
            'artist' => $artist,
        ]);
    }
}
