<div class="row justify-content-center" style="margin-top: 100px;">
    <div class="col-md-4">
        <?php if (array_key_exists('login_error', \helpers\ErrorFlow::getErrors())): ?>
            <div class="alert alert-warning" role="alert">
                <?= \helpers\ErrorFlow::fetch("login_error") ?>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <form action="/login" method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>