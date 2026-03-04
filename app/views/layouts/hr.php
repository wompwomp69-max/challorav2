<?php require APP_PATH . '/views/layouts/header.php'; ?>
<div class="d-flex" style="height: 100vh; overflow: hidden;">
    <nav class="navbar navbar-dark bg-dark flex-column align-items-stretch p-3" style="width: 220px; min-height: 100vh; flex-shrink: 0;">
        <a class="navbar-brand mb-4" href="<?= BASE_URL ?>/hr/jobs">HR Recruitment</a>
        <ul class="nav nav-pills flex-column">
            <li class="nav-item"><a class="nav-link text-white" href="<?= BASE_URL ?>/hr/jobs">Dashboard & Lowongan</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= BASE_URL ?>/hr/applications/accepted">Pelamar Diterima</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= BASE_URL ?>/hr/jobs/create">Buat Lowongan</a></li>
            <li class="nav-item mt-4"><a class="btn btn-primary fw-bold " href="<?= BASE_URL ?>/jobs">User Account Mode</a></li>
            <li class="nav-item mt-4"><a class="btn btn-outline-danger fw-bold" href="<?= BASE_URL ?>/auth/logout">Logout</a></li>
        </ul>
    </nav>
    <main class="flex-grow-1 p-4 d-flex flex-column overflow-auto" style="min-height: 0;">
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-success"><?= e($_SESSION['flash']) ?></div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?= e($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        <?= $content ?? '' ?>
    </main>
</div>
<?php require APP_PATH . '/views/layouts/footer.php'; ?>
