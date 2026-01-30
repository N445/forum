<?php

namespace App\DataFixtures;

use App\Entity\Post\Comment;
use App\Entity\Post\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->loadUsers($manager);
        $posts = $this->loadPosts($manager, $users);
        $this->loadComments($manager, $users, $posts);

        $manager->flush();
    }

    /**
     * @return User[]
     */
    private function loadUsers(ObjectManager $manager): array
    {
        $users = [];

        // Admin user
        $admin = new User();
        $admin->setEmail('admin@forum.local');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $manager->persist($admin);
        $users[] = $admin;

        // Regular users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($this->faker->unique()->safeEmail());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setRoles([]);
            $user->setIsVerified($this->faker->boolean(80));
            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * @param User[] $users
     * @return Post[]
     */
    private function loadPosts(ObjectManager $manager, array $users): array
    {
        $posts = [];

        for ($i = 0; $i < 30; $i++) {
            $post = new Post();
            $post->setTitle($this->faker->sentence(mt_rand(4, 8)));
            $post->setContent($this->faker->paragraphs(mt_rand(3, 6), true));
            $post->setAuthor($this->faker->randomElement($users));
            $manager->persist($post);
            $posts[] = $post;
        }

        return $posts;
    }

    /**
     * @param User[] $users
     * @param Post[] $posts
     */
    private function loadComments(ObjectManager $manager, array $users, array $posts): void
    {
        foreach ($posts as $post) {
            $commentCount = mt_rand(0, 8);

            for ($i = 0; $i < $commentCount; $i++) {
                $comment = new Comment();
                $comment->setContent($this->faker->paragraph(mt_rand(1, 3)));
                $comment->setAuthor($this->faker->randomElement($users));
                $comment->setPost($post);
                $manager->persist($comment);
            }
        }
    }
}
