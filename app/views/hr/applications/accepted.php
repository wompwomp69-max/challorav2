<div class="card mb-3">
    <div class="card-body">
        <h1 class="card-title h4">Pelamar Diterima (<?= (int) $totalAccepted ?> total)</h1>
        <a href="<?= BASE_URL ?>/hr/jobs" class="btn btn-outline-secondary btn-sm">← Kembali ke daftar lowongan</a>
    </div>
</div>

<?php if (empty($applicants)): ?>
    <div class="card">
        <div class="card-body">Belum ada pelamar yang diterima.</div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <form method="get" action="<?= BASE_URL ?>/hr/applications/accepted" class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0">Tampilkan per halaman:</label>
                    <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="10" <?= ($perPage ?? 20) == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= ($perPage ?? 20) == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= ($perPage ?? 20) == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= ($perPage ?? 20) == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Lowongan</th>
                            <th>Lokasi</th>
                            <th>CV</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applicants as $a): ?>
                            <tr>
                                <td><?= e($a['name']) ?></td>
                                <td><?= e($a['email']) ?></td>
                                <td><?= e($a['phone'] ?? '-') ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/hr/jobs/applicants?id=<?= (int) $a['job_id'] ?>"><?= e($a['job_title'] ?? '-') ?></a>
                                </td>
                                <td><?= e($a['job_location'] ?? '-') ?></td>
                                <td>
                                    <?php if (!empty($a['cv_path'])): ?>
                                        <a href="<?= BASE_URL ?>/index.php?url=download/cv&id=<?= (int) $a['id'] ?>" class="btn btn-sm btn-outline-primary">Unduh</a>
                                    <?php else: ?>—<?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (($totalPages ?? 1) > 1): ?>
                <nav class="mt-3">
                    <ul class="pagination pagination-sm mb-0">
                        <?php $currentPage = (int) ($page ?? 1); ?>
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/hr/applications/accepted?page=<?= $currentPage - 1 ?>&per_page=<?= (int) ($perPage ?? 20) ?>">«</a>
                        </li>
                        <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>/hr/applications/accepted?page=<?= $i ?>&per_page=<?= (int) ($perPage ?? 20) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $currentPage >= ($totalPages ?? 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= BASE_URL ?>/hr/applications/accepted?page=<?= min($currentPage + 1, $totalPages ?? 1) ?>&per_page=<?= (int) ($perPage ?? 20) ?>">»</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
