<?php

namespace helpers\seeder;

use PDO;
use src\Models\User;

class UserSeeder implements SeederInterface
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->seedUsers();
    }

    /**
     * @return void
     */
    public function seedUsers(): void
    {
        // Sample user data
        $users = [
            ['username' => 'John', 'password' => 'secret123', 'email' => 'john.doe@example.com'],
            ['username' => 'Ani', 'password' => 'secret123', 'email' => 'azizyana02@gmail.com'],
        ];

        foreach ($users as $user) {
            (new User())
                ->setUsername($user['username'])
                ->setPassword($user['password'])
                ->setEmail($user['email'])
                ->create();
        }

        echo "Users seeded successfully.\n";
    }
}