<?php require APP_PATH . '/views/layouts/header.php'; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>"><?= e($siteName ?? 'Challora Recruitment Platform') ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (isLoggedIn() && currentRole() === 'hr'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/hr/jobs">Dashboard HR</a></li>
                <?php elseif (isLoggedIn() && currentRole() === 'user'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/jobs">Lowongan</a></li>
                    <li class="nav-item" ><a class="nav-link" href="<?= BASE_URL ?>/applications">Yang sudah dilamar</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <?php if (currentRole() === 'user'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= e($_SESSION['user_name'] ?? 'User') ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/settings">Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/auth/logout">Log out</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item"><a class="nav-link disabled"><?= e($_SESSION['user_name'] ?? 'User') ?></a></li>
                    <div class="dropdown-divider"></div>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/logout">Logout</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/login">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/register">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-success"><?= e($_SESSION['flash']) ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= e($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (isLoggedIn() && currentRole() === 'user' && !($hideProfileBar ?? false) && !isProfileComplete()): ?>
        <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <span>Datamu belum dilengkapi, lengkapi agar HR semakin yakin untuk menerima mu.</span>
            <a href="<?= BASE_URL ?>/user/settings/edit" class="btn btn-warning btn-sm">Lengkapi Data</a>
        </div>
    <?php endif; ?>
    <?= $content ?? '' ?>
</main>
<?php require APP_PATH . '/views/layouts/footer.php'; ?>
