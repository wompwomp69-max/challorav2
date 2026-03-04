<div class="d-flex flex-column flex-grow-1 overflow-hidden" style="min-height: 0;">
<h1 class="mb-3 flex-shrink-0">Dashboard HR — Lowongan Saya</h1>

<?php
$stats = $stats ?? ['total' => 0, 'accepted' => 0, 'rejected' => 0, 'pending' => 0];
$totalJobs = $totalJobs ?? 0;
$filter = $filter ?? 'all';
?>
<div class="row mb-3 flex-shrink-0">
    <div class="col-md">
        <div class="card bg-primary text-white h-100">
            <div class="card-body d-flex flex-column justify-content-center align-items-center" style="min-height: 120px;">
                <h5 class="card-title">Total Lowongan</h5>
                <p class="card-text display-6 mb-0"><?= (int) $totalJobs ?></p>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card bg-secondary text-white h-100">
            <div class="card-body d-flex flex-column justify-content-center align-items-center" style="min-height: 120px;">
                <h5 class="card-title">Total Pelamar</h5>
                <p class="card-text display-6 mb-0"><?= (int) $stats['total'] ?></p>
                <small class="opacity-75">
                    Diterima: <?= (int) $stats['accepted'] ?> |
                    Ditolak: <?= (int) $stats['rejected'] ?> |
                    Pending: <?= (int) $stats['pending'] ?>
                </small>
            </div>
        </div>
    </div>
</div>

<p class="flex-shrink-0 mb-3">
    <a href="<?= BASE_URL ?>/hr/jobs/create" class="btn btn-primary">+ Buat Lowongan</a>
    <a href="<?= BASE_URL ?>/hr/applications/accepted" class="btn btn-outline-success">Lihat Pelamar Diterima</a>
</p>

<?php if (($totalJobs ?? 0) === 0 && $filter === 'all'): ?>
    <div class="card flex-shrink-0">
        <div class="card-body">Belum ada lowongan. <a href="<?= BASE_URL ?>/hr/jobs/create">Buat pertama kali</a></div>
    </div>
<?php else: ?>
    <div class="card flex-grow-1 d-flex flex-column overflow-hidden" style="min-height: 0;">
        <div class="card-body d-flex flex-column overflow-hidden" style="min-height: 0;">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-shrink-0 flex-wrap gap-2">
                <form method="get" action="<?= BASE_URL ?>/hr/jobs" class="d-flex align-items-center gap-5 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Tampilkan per halaman:</label>
                        <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            <option value="10" <?= ($perPage ?? 20) == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= ($perPage ?? 20) == 20 ? 'selected' : '' ?>>20</option>
                            <option value="50" <?= ($perPage ?? 20) == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= ($perPage ?? 20) == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="form-label mb-0">Filter:</span>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Filter lowongan">
                            <input type="radio" class="btn-check" name="filter" id="filter-all" value="all" <?= $filter === 'all' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="filter-all">Semua</label>

                            <input type="radio" class="btn-check" name="filter" id="filter-no-apply" value="no_apply" <?= $filter === 'no_apply' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="filter-no-apply">Belum ada apply</label>

                            <input type="radio" class="btn-check" name="filter" id="filter-has-apply" value="has_apply" <?= $filter === 'has_apply' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="filter-has-apply">Sudah ada apply</label>

                            <input type="radio" class="btn-check" name="filter" id="filter-has-accepted" value="has_accepted" <?= $filter === 'has_accepted' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="btn btn-outline-primary" for="filter-has-accepted">Sudah ada yang acc</label>
                        </div>
                    </div>
                    <input type="hidden" name="page" value="1">
                </form>
            </div>
            <div class="table-responsive overflow-auto flex-grow-1" style="min-height: 0;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Lokasi</th>
                            <th>Dibuat</th>
                            <th>Pelamar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jobs)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada lowongan untuk filter ini.</td>
                            </tr>
                        <?php else: ?>
                        <?php foreach ($jobs as $j): ?>
                            <tr>
                                <td><?= e($j['title']) ?></td>
                                <td><?= e($j['location'] ?? '-') ?></td>
                                <td><?= !empty($j['created_at']) ? date('d/m/Y H:i', strtotime($j['created_at'])) : '-' ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/hr/jobs/applicants?id=<?= (int) $j['id'] ?>">
                                        <?= (int) ($j['applicant_count'] ?? 0) ?> pelamar
                                        <?php if (($j['applicant_accepted'] ?? 0) > 0 || ($j['applicant_rejected'] ?? 0) > 0): ?>
                                            <small class="text-muted">(<?= (int) ($j['applicant_accepted'] ?? 0) ?> diterima, <?= (int) ($j['applicant_rejected'] ?? 0) ?> ditolak)</small>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td class="d-flex gap-1">
                                    <a href="<?= BASE_URL ?>/hr/jobs/edit?id=<?= (int) $j['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="post" action="<?= BASE_URL ?>/index.php?url=hr/jobs/delete" class="d-inline" onsubmit="return confirm('Hapus lowongan ini?');">
                                        <input type="hidden" name="id" value="<?= (int) $j['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (($totalPages ?? 1) > 1): ?>
                <nav class="mt-2 flex-shrink-0">
                    <ul class="pagination pagination-sm mb-0">
                        <?php
                        $currentPage = (int) ($page ?? 1);
                        $baseUrl = BASE_URL . '/hr/jobs';
                        $currentFilter = $filter ?? 'all';
                        ?>
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>?page=<?= $currentPage - 1 ?>&per_page=<?= (int) ($perPage ?? 20) ?>&filter=<?= urlencode($currentFilter) ?>">«</a>
                        </li>
                        <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>?page=<?= $i ?>&per_page=<?= (int) ($perPage ?? 20) ?>&filter=<?= urlencode($currentFilter) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $currentPage >= ($totalPages ?? 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>?page=<?= min($currentPage + 1, $totalPages ?? 1) ?>&per_page=<?= (int) ($perPage ?? 20) ?>&filter=<?= urlencode($currentFilter) ?>">»</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
</div>

