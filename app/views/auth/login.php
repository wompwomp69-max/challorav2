<div class="card mx-auto my-auto" style="max-width: 400px;">
    <div class="card-body">
        <h1 class="card-title h4 mb-4">Login</h1>
        <form method="post" action="<?= BASE_URL ?>/index.php?url=auth/login">
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
            </div>
            <?php if (!empty($error)): ?><p class="text-danger"><?= e($error) ?></p><?php endif; ?>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" class="btn btn-primary">Login</button>
                <a href="<?= BASE_URL ?>/auth/register">Belum punya akun? Daftar</a>
            </div>
        </form>
    </div>
</div>
