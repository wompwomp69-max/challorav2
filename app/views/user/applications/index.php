<style>.card-hover { cursor: pointer; } .card-hover:hover { background-color: rgba(13, 110, 253, 0.05); }</style>
<h1 class="mb-4">Telah dilamar</h1>
<p><a href="<?= BASE_URL ?>/jobs" class="btn btn-outline-secondary btn-sm mb-3">← Kembali ke daftar lowongan</a></p>
<?php if (empty($applications)): ?>
    <div class="card">
        <div class="card-body">Belum ada lamaran. <a href="<?= BASE_URL ?>/jobs">Cari lowongan</a></div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($applications as $a): ?>
        <?php $badgeClass = $a['status'] === 'pending' ? 'bg-primary' : ($a['status'] === 'accepted' ? 'bg-success' : ($a['status'] === 'rejected' ? 'bg-danger' : 'bg-secondary')); ?>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="<?= BASE_URL ?>/jobs/show?id=<?= (int)($a['job_id'] ?? 0) ?>" class="text-decoration-none text-dark">
            <div class="card h-100 card-hover">
                <div class="card-body">
                    <h5 class="card-title"><?= e($a['job_title']) ?></h5>
                    <span class="badge <?= $badgeClass ?>"><?= e($a['status']) ?></span>
                    <p class="card-text small mt-2"><?= e($a['created_at']) ?></p>
                    <p class="card-text small text-muted mb-0">Klik untuk lihat detail →</p>
                </div>
            </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
