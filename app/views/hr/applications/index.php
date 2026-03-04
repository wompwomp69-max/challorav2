<?php $maritalLabels = ['single' => 'Belum menikah', 'married' => 'Menikah', 'divorced' => 'Cerai', 'widowed' => 'Duda/Janda']; ?>
<div class="card mb-3">
    <div class="card-body">
        <h1 class="card-title h4">Pelamar: <?= e($job['title']) ?></h1>
        <a href="<?= BASE_URL ?>/hr/jobs" class="btn btn-outline-secondary btn-sm">← Kembali ke daftar lowongan</a>
    </div>
</div>
<?php if (empty($applicants)): ?>
    <div class="card">
        <div class="card-body">Belum ada pelamar.</div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr><th>Nama</th><th>Email</th><th>No. HP</th><th>CV</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applicants as $a): ?>
                            <tr>
                                <td><?= e($a['name']) ?></td>
                                <td><?= e($a['email']) ?></td>
                                <td><?= e($a['phone'] ?? '-') ?></td>
                                <td>
                                    <?php if (!empty($a['cv_path'])): ?>
                                        <a href="<?= BASE_URL ?>/index.php?url=download/cv&id=<?= (int)$a['id'] ?>" class="btn btn-sm btn-outline-primary">Unduh</a>
                                    <?php else: ?>—<?php endif; ?>
                                </td>
                                <td>
                                    <?php $badgeClass = $a['status'] === 'pending' ? 'bg-primary' : ($a['status'] === 'accepted' ? 'bg-success' : ($a['status'] === 'rejected' ? 'bg-danger' : 'bg-secondary')); ?>
                                    <span class="badge <?= $badgeClass ?>"><?= e($a['status']) ?></span>
                                </td>
                                <td>
                                    <form method="post" action="<?= BASE_URL ?>/index.php?url=hr/applications/update-status" class="d-inline">
                                        <input type="hidden" name="application_id" value="<?= (int)$a['id'] ?>">
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                            <option value="pending" <?= $a['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="accepted" <?= $a['status'] === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                            <option value="rejected" <?= $a['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            <tr class="bg-light">
                                <td colspan="6" class="p-3">
                                    <a class="btn btn-sm btn-outline-secondary mb-2" data-bs-toggle="collapse" href="#detail-<?= (int)$a['id'] ?>" role="button">▼ Lihat detail profil pelamar</a>
                                    <div class="collapse" id="detail-<?= (int)$a['id'] ?>">
                                        <div class="row small">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Alamat:</strong> <?= e($a['address'] ?? '-') ?></p>
                                                <p class="mb-1"><strong>Nama Ayah:</strong> <?= e($a['father_name'] ?? '-') ?></p>
                                                <p class="mb-1"><strong>Nama Ibu:</strong> <?= e($a['mother_name'] ?? '-') ?></p>
                                                <p class="mb-1"><strong>Status Pernikahan:</strong> <?= e(!empty($a['marital_status']) ? ($maritalLabels[$a['marital_status']] ?? $a['marital_status']) : '-') ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Pendidikan:</strong> <?= e($a['education_level'] ?? '-') ?> - <?= e($a['education_major'] ?? '') ?> (<?= e($a['graduation_year'] ?? '') ?>)</p>
                                                <p class="mb-1"><strong>Universitas:</strong> <?= e($a['education_university'] ?? '-') ?></p>
                                            </div>
                                            <?php
                                            $weList = $workExpByUser[(int)$a['user_id']] ?? [];
                                            if (!empty($weList)):
                                            ?>
                                            <div class="col-12 mt-2">
                                                <strong>Pengalaman Kerja:</strong>
                                                <ul class="mb-0">
                                                    <?php foreach ($weList as $we): ?>
                                                    <li><?= e($we['title']) ?><?= !empty($we['company_name']) ? ' di ' . e($we['company_name']) : '' ?> (<?= e($we['year_start']) ?> - <?= e($we['year_end']) ?>)<br><span class="text-muted"><?= e($we['description']) ?></span></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
