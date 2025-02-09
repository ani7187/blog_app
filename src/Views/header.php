<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Hello, world!</title>
</head>
<body class="d-flex flex-column min-vh-100"> <!-- Added min-vh-100 to stretch body to full height -->

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Blog</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                    aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Move "Posts" link to the left side -->
            <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="navbar-nav">
                    <a class="nav-link" aria-current="page" href="/posts">Posts</a>
                </div>
                <div class="navbar-nav">
                    <a class="nav-link" aria-current="page" href="/my_posts">My Posts</a>
                </div>
            <?php endif; ?>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <!-- If user is logged in, show Logout, otherwise Login/Register -->
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="/logout">Logout</a>
                    <?php else: ?>
                        <a class="nav-link" href="/login">Login</a>
                        <a class="nav-link" href="/register">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>