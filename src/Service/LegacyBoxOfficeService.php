<?php

declare(strict_types=1);

namespace App\Service;

final class LegacyBoxOfficeService
{
    public function formatBadge(array $entry): string
    {
        $tag = $entry['tag'] ?? 'N/A';
        $title = $entry['title'] ?? 'Titre inconnu';

        return '[' . strtoupper((string) $tag) . '] ' . $title;
    }

    /**
     * @param array<int, int|float|null> $ratings
     */
    public function calculateAverageRating(array $ratings): float
    {
        $validRatings = array_filter(
            $ratings,
            static fn(int|float|null $rating): bool => null !== $rating
        );

        if ([] === $validRatings) {
            return 0.0;
        }

        $average = array_sum($validRatings) / count($validRatings);

        return round($average, 1);
    }

    public function calculateTicketPrice(float $basePrice, int $age, bool $isMember): float
    {
        $price = $basePrice;

        if ($age < 18 || $age > 65) {
            $price *= 0.75;
        } elseif ($isMember) {
            $price *= 0.80;
        }

        return round($price, 2);
    }
}
