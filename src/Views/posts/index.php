<h2>Posts</h2>

<!-- Search Form -->
<form method="GET" action="/posts" class="mb-4">
    <div class="input-group">
        <input type="text" name="query" class="form-control" placeholder="Search posts by title or content" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>

<!-- Posts List -->
<div class="list-group">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="list-group-item">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>

                <!-- Truncated Content -->
                <p id="content-<?php echo $post['id']; ?>" class="content-truncated"><?php echo htmlspecialchars(substr($post['content'], 0, 150)); ?>...</p>

                <a href="/posts/<?php echo $post['id']; ?>" class="btn btn-primary">Read More</a>

                <small class="p-3">By <?php echo htmlspecialchars($post['username']); ?> | <?php echo $post['created_at']; ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts found.</p>
    <?php endif; ?>
</div>

<!-- Pagination -->
<div class="pagination justify-content-center mt-5">
    <?php if (isset($paginationLinks)): ?>
        <div class="pagination-links">
            <?php echo $paginationLinks; ?>
        </div>
    <?php endif; ?>
</div>