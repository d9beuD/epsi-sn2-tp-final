<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Music;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MusicController extends AbstractController
{
    #[Route('/music', name: 'app_music')]
    public function index(): Response
    {
        return $this->render('music/index.html.twig', [
            'controller_name' => 'MusicController',
        ]);
    }

    #[Route('/album/{id}/musics/new', methods: ['GET', 'POST'], name: 'add_music')]
    public function add(Request $request, ManagerRegistry $doctrine, ?Album $album = null): Response
    {
        // Si l'ID de l'album est inconnu (qu'il ne correspond à aucun album dans la base de données)
        if ($album === null) {
            throw $this->createNotFoundException('L\'album n\'existe pas.');
        }

        if ($request->isMethod('POST')) {
            $entityManager = $doctrine->getManager();

            $music = new Music();
            $music->setTitle($request->request->get('title'))
                ->setDuration($request->request->get('duration'))
                ->setAlbum($album);

            $entityManager->persist($music);
            $entityManager->flush();

            // On redirige vers l'album
            return $this->redirectToRoute('show_album', ['id' => $album->getId()]);
        }

        // On affiche le formulaire d'ajout d'une musique
        return $this->render('music/new.html.twig', [
            'album' => $album,
        ]);
    }

    #[Route('/music/{id}', methods: ['GET', 'POST'], name: 'edit_music')]
    public function edit(Request $request, ManagerRegistry $doctrine, ?Music $music = null): Response
    {
        // Si l'ID de la musique est inconnu
        if ($music === null) {
            throw $this->createNotFoundException('La musique n\'existe pas.');
        }

        if ($request->isMethod('POST')) {
            $entityManager = $doctrine->getManager();

            $music->setTitle($request->request->get('title'))
                ->setDuration($request->request->get('duration'));

            $entityManager->flush();

            // On redirige vers l'album
            return $this->redirectToRoute('show_album', [
                'id' => $music->getAlbum()->getId()
            ]);
        }

        // Affichage du formulaire d'édition d'une musique
        return $this->render('music/edit.html.twig', [
            'music' => $music,
        ]);
    }
}
