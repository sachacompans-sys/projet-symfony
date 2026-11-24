<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\GameFormType;
use Doctrine\ORM\EntityManagerInterface;

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

    #[Route('/catalogue/nouveau', name: 'app_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $game = new Game();
        $form = $this->createForm(GameFormType::class, $game);
        // 1. Inspecte la requête : si POST, hydrate automatiquement $game
        $form->handleRequest($request);
        // 2. Vérifie l'envoi ET toutes les contraintes #[Assert\...]
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($game);
            $em->flush();
            // 3. Pattern PRG (Post-Redirect-Get, code 303 compatible Turbo) + Flash
            $this->addFlash('success', 'Le jeu a bien été créé.');
            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }
        // Symfony 8 convertit $form en FormView et renvoie 422 automatiquement si invalide
        return $this->render('game/new.html.twig', ['form' => $form]);
    }


    #[Route('/catalogue/{id}/modifier', name: 'app_game_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Game $game, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(GameFormType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('app_game_show', ['id' => $game->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route(
        '/catalogue/{id}/supprimer',
        name: 'app_game_delete',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function delete(
        Game $game,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $token = $request->getPayload()->getString('_token');
        if ($this->isCsrfTokenValid('delete' . $game->getId(), $token)) {
            $em->remove($game);
            $em->flush();
            $this->addFlash('warning', 'Jeu supprimé.');
        }
        return $this->redirectToRoute(
            'app_game_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
