<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GameController extends AbstractController
{
    #[Route('/catalogue', name: 'app_game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {
        return $this->render('game/index.html.twig', [
            'games' => $gameRepository->findAllWithCategory(),
        ]);
    }

    #[Route('/catalogue/{id}', name: 'app_game_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Game $game): Response
    {
        return $this->render('game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/api/catalogue', name: 'api_game_list', methods: ['GET'])]
    public function ping(): JsonResponse
    {
        return $this->json([
            'genre' => 'fps',
            'année' => '2026',
        ]);
    }
}
