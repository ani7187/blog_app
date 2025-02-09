<?php if (empty($post)): ?>
    <div class="alert alert-warning" role="alert">
        Post not found
    </div>
<?php else: ?>
    <div class="card-header">
        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    </div>
    <div class="card">
        <div class="card-body">
            <p><small>By <?php echo htmlspecialchars($post['username']); ?> | <?php echo $post['created_at']; ?></small></p>

            <div class="post-content">
                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            </div>
        </div>
        <!--<div class="card-footer">
            <a href="/posts" class="btn btn-secondary">Back to Posts</a>
        </div>-->
    </div>

<?php if (array_key_exists('comment_error', \helpers\ErrorFlow::getErrors())): ?>
    <div class="alert alert-warning" role="alert">
        <?= \helpers\ErrorFlow::fetch("comment_error") ?>
    </div>
<?php endif; ?>

<div class="comments-section">
    <h3>Comments</h3>

    <form action="/posts/<?= $post['id'] ?>/comments" method="POST">
        <textarea name="content" class="form-control" required></textarea>
        <button type="submit" class="btn btn-primary mt-2">Add Comment</button>
    </form>

    <?php if (!empty($comments)): ?>
        <ul class="list-group mt-3">
            <?php foreach ($comments as $comment): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($comment['content']) ?></strong>
                    <small class="text-muted"> - <?= $comment['created_at'] ?></small>

                    <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                        <form action="/posts/<?= $post['id'] ?>/comments/<?= $comment['id'] ?>/delete" method="POST" class="d-inline">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="/posts/<?= $post['id'] ?>?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php endif; ?>


