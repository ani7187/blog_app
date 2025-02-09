<?php

namespace helpers\seeder;

use PDO;
use src\Models\Post;

class PostSeeder implements SeederInterface
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->seedPosts();
    }

    /**
     * @return void
     */
    public function seedPosts(): void
    {
        // Sample post data
        $posts = [
            ['title' => 'First Post', 'content' => 'This is the content of the first post.', 'user_id' => 1],
            ['title' => 'Second Post', 'content' => 'This is the content of the second post.', 'user_id' => 2],
        ];

        foreach ($posts as $post) {
            (new Post())
                ->setTitle($post['title'])
                ->setContent($post['content'])
                ->setUserID($post['user_id'])
                ->create();
        }

        echo "Posts seeded successfully.\n";
    }
}