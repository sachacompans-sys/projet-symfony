<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        #[Autowire('%env(ADMIN_PASSWORD)%')]
        private string $adminPassword,
        #[Autowire('%env(USER_PASSWORD)%')]
        private string $userPassword,
    ) {}


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugger = new AsciiSlugger();

        // 1. Création de l'administrateur
        $admin = new User();
        $admin->setEmail('admin@cinetech.fr')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, $this->adminPassword));
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@cinetech.fr')
            ->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, $this->userPassword));
        $manager->persist($user);

        // 2. Création des catégories
        $categories = [];
        foreach (['Open World', 'RPG', 'Plateforme', 'FPS', 'Stratégie'] as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        // 3. Création des jeux
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
                ->setRating($faker->randomFloat(1, 0, 5))
                ->setSlug($slugger->slug($titles[$i])->lower()->toString())
                ->setCategory($faker->randomElement($categories));

            $manager->persist($game);
        }

        $manager->flush();
    }
}
