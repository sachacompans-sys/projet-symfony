<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LegacyBoxOfficeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegacyReportController extends AbstractController
{
    #[Route('/legacy/rapport', name: 'app_game_report', methods: ['GET'])]
    public function report(LegacyBoxOfficeService $service): Response
    {
        $games = [
            ['tag' => 'RPG', 'title' => 'The Witcher 3: Wild Hunt', 'ratings' => [5, 5, 4.5, 5]],
            ['tag' => 'INDÉ', 'title' => 'Hollow Knight', 'ratings' => [5, 4.5, 5, null]],
            ['tag' => 'ACTION', 'title' => 'Cyberpunk 2077', 'ratings' => [4, 4.5, 3.5, 4]],
            ['tag' => 'SOULS', 'title' => 'Elden Ring', 'ratings' => [5, 5, 5, 4.5]],
        ];

        $report = [];
        foreach ($games as $game) {
            $report[] = [
                'label' => $service->formatBadge($game),
                'average' => $service->calculateAverageRating($game['ratings']),
            ];
        }

        $basePrice = 60.0;
        $prices = [
            'Édition Standard (Plein tarif)' => $service->calculateTicketPrice($basePrice, 28, false),
            'Tarif Jeune (-18 ans)' => $service->calculateTicketPrice($basePrice, 16, false),
            'Abonné Game Pass / VIP' => $service->calculateTicketPrice($basePrice, 28, true),
            'Jeune + Abonné VIP' => $service->calculateTicketPrice($basePrice, 16, true),
        ];

        return $this->render('legacy_report/index.html.twig', [
            'report' => $report,
            'prices' => $prices,
        ]);
    }
}
