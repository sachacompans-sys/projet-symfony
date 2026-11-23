<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugger = new AsciiSlugger();

        $categories = [];
        foreach (['Open World', 'RPG', 'Plateforme', 'FPS', 'Stratégie'] as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        $titles = [
            'The Witcher 3: Wild Hunt',
            'Elden Ring',
            'Cyberpunk 2077',
            'Baldur\'s Gate 3',
            'Red Dead Redemption 2',
            'The Legend of Zelda: Breath of the Wild',
            'Horizon Zero Dawn',
            'Ghost of Tsushima',
            'Grand Theft Auto V',
            'The Elder Scrolls V: Skyrim',
            'Assassin\'s Creed Valhalla',
            'God of War Ragnarök',
            'Doom Eternal',
            'Half-Life 2',
            'Counter-Strike 2',
            'Overwatch 2',
            'BioShock Infinite',
            'Titanfall 2',
            'Civilization VI',
            'Age of Empires IV',
            'StarCraft II',
            'XCOM 2',
            'Cities: Skylines',
            'Anno 1800',
            'Hollow Knight',
            'Celeste',
            'Ori and the Will of the Wisps',
            'Rayman Legends',
            'Super Mario Odyssey',
            'Dead Cells',
        ];

        for ($i = 0; $i < 30; $i++) {
            $game = new Game();
            $game->setName($titles[$i])
                ->setSynopsis($faker->paragraphs(2, true))
                ->setAnnee($faker->numberBetween(2004, 2026))
                ->setRating($faker->randomFloat(1, 6, 10))
                ->setSlug($slugger->slug($titles[$i])->lower()->toString())
                ->setCategory($faker->randomElement($categories));

            $manager->persist($game);
        }

        $manager->flush();
    }
}
