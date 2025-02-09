<div class="container">
    <h2>My Posts</h2>

    <a href="/posts/create" class="btn btn-success mb-3">Create New Post</a>

    <?php if (array_key_exists('post_error', \helpers\ErrorFlow::getErrors())): ?>
    <div class="alert alert-warning" role="alert">
        <?= \helpers\ErrorFlow::fetch("post_error") ?>
    </div>
    <?php endif; ?>

    <!-- Display Posts -->
    <?php if (!empty($posts)): ?>
        <div class="list-group">
            <?php foreach ($posts as $post): ?>
                <div class="list-group-item">
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($post['content'], 0, 150)); ?>...</p>

                    <a href="/posts/<?php echo $post['id']; ?>" class="btn btn-primary">Read More</a>
                    <a href="/posts/edit/<?php echo $post['id']; ?>" class="btn btn-warning">Edit</a>
                    <a href="/posts/delete/<?php echo $post['id']; ?>" class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>

                    <small class="p-3">By <?php echo htmlspecialchars($post['username']); ?>
                        | <?php echo $post['created_at']; ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination Links -->
        <div class="pagination mt-2">
            <?php if ($pagesCnt > 1): ?>
                <?php for ($i = 1; $i <= $pagesCnt; $i++): ?>
                    <a href="/my_posts?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>No posts found.</p>
    <?php endif; ?>
</div>
