<?php

namespace helpers\seeder;

use PDO;

class SeederRunner
{
    /** @return void */
    public function run(): void
    {
        // Create and run the UserSeeder
        $userSeeder = new UserSeeder();
        $userSeeder->run();

        // Create and run the PostSeeder
        $postSeeder = new PostSeeder();
        $postSeeder->run();
    }
}