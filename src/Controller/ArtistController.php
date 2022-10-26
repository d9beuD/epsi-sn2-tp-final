<?php

namespace App\Controller;

use App\Entity\Artist;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
