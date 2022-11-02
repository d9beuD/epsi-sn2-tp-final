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
        // Récupération de tous les albums
        $albums = $albumRepository->findAll();

        // Affichage de la page avec les albums
        return $this->render('album/index.html.twig', [
            'albums' => $albums,
        ]);
    }

    #[Route('/albums/new', name: 'new_album', methods: ['GET', 'POST'])]
    public function add(Request $request, ManagerRegistry $doctrine, ArtistRepository $artistRepository): Response
    {
        // Récupération du gestionnaire d'entités
        $entityManager = $doctrine->getManager();

        // Récupération de l'unique artiste
        $artist = $artistRepository->find(1);

        if ($request->getMethod() === 'POST') {
            // Si l'artiste n'existe pas encore, on ne peut pas ajouter d'album
            if ($artist === null) {
                throw $this->createAccessDeniedException('Vous devez d\'abord ajouter un artiste');
            }

            //Création du nouvel album
            $album = new Album();

            $album->setTitle($request->request->get('title'))
                ->setIllustrationURL($request->request->get('illustrationURL'))
                ->setReleaseDate(new DateTime($request->request->get('releaseDate')))
                ->setArtist($artist);

            // On précise à Doctrine qu'il va falloir l'enregistrer
            $entityManager->persist($album);

            // Exécute toutes les requêtes SQL en attente
            $entityManager->flush();

            // On redirige vers la liste des albums
            return $this->redirectToRoute('show_albums');
        }

        // Si méthode GET, on affiche le formulaire d'ajout de l'album
        return $this->render('album/new.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/albums/{id}', name: 'show_album')]
    public function show(Album $album): Response
    {
        // L'album est auto-injecté dans $album parce qu'il y a l'ID dans la route
        return $this->render('album/show.html.twig', [
            'album' => $album,
        ]);
    }

    #[Route('/albums/{id}/edit', methods: ['GET', 'POST'], name: 'edit_album')]
    public function edit(Request $request, Album $album, ManagerRegistry $doctrine): Response
    {
        // Si on poste depuis le formulaire
        if ($request->getMethod() === 'POST') {
            // Récupération du gestionnaire d'entités
            $entityManager = $doctrine->getManager();

            $album
                ->setTitle($request->request->get('title'))
                ->setIllustrationURL($request->request->get('illustrationURL'))
                ->setReleaseDate(new DateTime($request->request->get('releaseDate')));

            // Exécute toutes les requêtes SQL en attente
            $entityManager->flush();
            return $this->redirectToRoute('show_album', ['id' => $album->getId()]);
        }

        // Si méthode GET, on affiche le formulaire d'ajout de l'album
        return $this->render('album/edit.html.twig', [
            'album' => $album,
        ]);
    }
}
