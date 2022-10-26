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
        $artist = $doctrine->getRepository(Artist::class)->find(1);

        return $this->render('artist/index.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/artist/new', methods: ['GET', 'POST'])]
    public function setArtist(Request $request, ArtistRepository $artistRepository, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $artist = $artistRepository->find(1);


        if ($request->getMethod() === 'POST') {
            if ($artist === null) {
                $artist = new Artist();
            }

            $artist->setName($request->request->get('name'))
                ->setIllustrationURL($request->request->get('illustrationURL'))
                ->setDescription($request->request->get('description'));

            $entityManager->persist($artist); // Ne fonctionne que si l'artiste n'existe pas déjà
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('artist/new.html.twig', [
            'artist' => $artist,
        ]);
    }
}
