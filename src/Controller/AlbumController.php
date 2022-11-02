<?php

namespace App\Controller;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    #[Route('/albums', name: 'show_albums')]
    public function index(AlbumRepository $albumRepository): Response
    {
        $albums = $albumRepository->findAll();

        return $this->render('album/index.html.twig', [
            'albums' => $albums,
        ]);
    }

    #[Route('/albums/new', name: 'new_album', methods: ['GET', 'POST'])]
    public function add(Request $request, ManagerRegistry $doctrine, ArtistRepository $artistRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $artist = $artistRepository->find(1);

        if ($request->getMethod() === 'POST') {
            // Si l'artiste n'existe pas encore, il faut l'ajout d'un album
            if ($artist === null) {
                return $this->createAccessDeniedException('Vous devez d\'abord ajouter un artiste');
            }

            $album = new Album();

            $album->setTitle($request->request->get('title'))
                ->setIllustrationURL($request->request->get('illustrationURL'))
                ->setReleaseDate(new DateTime($request->request->get('releaseDate')))
                ->setArtist($artist);

            $entityManager->persist($album);
            $entityManager->flush();

            return $this->redirectToRoute('show_albums');
        }

        return $this->render('album/new.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/albums/{id}', name: 'show_album')]
    public function show(Album $album): Response
    {
        return $this->render('album/show.html.twig', [
            'album' => $album,
        ]);
    }

    #[Route('/albums/{id}/edit', methods: ['GET', 'POST'], name: 'edit_album')]
    public function edit(Request $request, Album $album, ManagerRegistry $doctrine): Response
    {

        if ($request->getMethod() === 'POST') {
            $entityManager = $doctrine->getManager();

            $album
                ->setTitle($request->request->get('title'))
                ->setIllustrationURL($request->request->get('illustrationURL'))
                ->setReleaseDate(new DateTime($request->request->get('releaseDate')));

            $entityManager->flush();
            return $this->redirectToRoute('show_album', ['id' => $album->getId()]);
        }

        return $this->render('album/edit.html.twig', [
            'album' => $album,
        ]);
    }
}
