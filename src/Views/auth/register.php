<div class="row justify-content-center" style="margin-top: 100px;">
    <div class="col-md-6">
        <?php if (array_key_exists('register_error', \helpers\ErrorFlow::getErrors())): ?>
            <div class="alert alert-warning" role="alert">
                <?= \helpers\ErrorFlow::fetch("register_error") ?>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <form action="/register" method="POST">
                    <div class="form-group mb-3">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Choose a password">
                    </div>

                    <div class="form-group mb-3">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm your password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>