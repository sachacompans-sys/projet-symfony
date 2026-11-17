<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class GameController extends AbstractController
{
    #[Route('/catalogue', name: 'app_game_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('game/index.html.twig', [
            'games' => $this->getGames(),
        ]);
    }

    private function getGames(): array
    {
        return [
            ['id' => 1, 'name' => 'GTAVI', 'annee' => '2026', 'genre' => 'OpenWorld', 'rating' => 9.0],
            ['id' => 2, 'name' => 'Warzone', 'annee' => '2018', 'genre' => 'FPS', 'rating' => 7.5],
            ['id' => 3, 'name' => 'The Witcher 3', 'annee' => '2015', 'genre' => 'RPG', 'rating' => 9.7],
            ['id' => 4, 'name' => 'Elden Ring', 'annee' => '2022', 'genre' => 'RPG', 'rating' => 9.5],
            ['id' => 5, 'name' => 'Counter-Strike 2', 'annee' => '2023', 'genre' => 'FPS', 'rating' => 8.6],
            ['id' => 6, 'name' => 'Red Dead Redemption 2', 'annee' => '2018', 'genre' => 'OpenWorld', 'rating' => 9.4],
            ['id' => 7, 'name' => 'Minecraft', 'annee' => '2011', 'genre' => 'Bac à sable', 'rating' => 9.0]
        ];
    }

    #[Route('/catalogue/{id}', name: 'app_game_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): Response
    {
        foreach ($this->getGames() as $game) {
            if ($game['id'] === $id) {
                return $this->render('game/show.html.twig', ['game' => $game]);
            }
        }

        throw $this->createNotFoundException('Jeu introuvable.');
    }

    #[Route('/api/catalogue', name: 'api_game_list', methods: ['GET'])]
    public function ping(): JsonResponse
    {
        return $this->json([
            'genre' => 'fps',
            'année' => '2026'
        ]);
    }
}
