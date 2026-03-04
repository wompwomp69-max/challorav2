<h1 class="mb-4">Daftar Lowongan</h1>
<?php if (empty($jobs)): ?>
    <div class="card">
        <div class="card-body">Belum ada lowongan.</div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($jobs as $j): ?>
            <?php $applied = in_array((int)$j['id'], $appliedJobIds ?? [], true); ?>
            <div class="col-12">
                <div class="card <?= $applied ? 'border-primary border-2' : '' ?>">
                    <div class="card-body">
                        <?php if ($applied): ?><span class="badge bg-primary mb-2">Sudah dilamar</span><?php endif; ?>
                        <h5 class="card-title"><a href="<?= BASE_URL ?>/jobs/show?id=<?= (int)$j['id'] ?>" class="text-decoration-none"><?= e($j['title']) ?></a></h5>
                        <p class="card-text text-muted"><?= e(mb_substr($j['description'], 0, 200)) ?><?= mb_strlen($j['description']) > 200 ? '…' : '' ?></p>
                        <p class="card-text small text-muted">Lokasi: <?= e($j['location'] ?? '-') ?> | Gaji: <?= e($j['salary_range'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
