<?php $maritalLabels = ['single' => 'Belum menikah', 'married' => 'Menikah', 'divorced' => 'Cerai', 'widowed' => 'Duda/Janda']; ?>
<div class="card mb-4">
    <div class="card-body">
        <h1 class="card-title h4 mb-4">Profil Saya</h1>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nama:</strong> <?= e($user['name']) ?></p>
                <p><strong>Email:</strong> <?= e($user['email']) ?></p>
                <p><strong>No. HP:</strong> <?= e($user['phone'] ?? '-') ?></p>
                <p><strong>Alamat:</strong> <?= e($user['address'] ?? '-') ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Nama Ayah:</strong> <?= e($user['father_name'] ?? '-') ?></p>
                <p><strong>Nama Ibu:</strong> <?= e($user['mother_name'] ?? '-') ?></p>
                <p><strong>Status Pernikahan:</strong> <?= e(!empty($user['marital_status']) ? ($maritalLabels[$user['marital_status']] ?? $user['marital_status']) : '-') ?></p>
            </div>
        </div>
        <?php if (!empty($user['education_level']) || !empty($user['education_university'])): ?>
        <h6 class="mt-3">Pendidikan Terakhir</h6>
        <p><?= e($user['education_level'] ?? '') ?> - <?= e($user['education_major'] ?? '') ?> (<?= e($user['graduation_year'] ?? '') ?>)<br><?= e($user['education_university'] ?? '') ?></p>
        <?php endif; ?>
        <?php if (!empty($workExperiences)): ?>
        <h6 class="mt-3">Pengalaman Kerja</h6>
        <ul class="list-unstyled">
            <?php foreach ($workExperiences as $we): ?>
            <li class="mb-2"><?= e($we['title']) ?><?= !empty($we['company_name']) ? ' di ' . e($we['company_name']) : '' ?> (<?= e($we['year_start']) ?> - <?= e($we['year_end']) ?>)<br><small class="text-muted"><?= e($we['description']) ?></small></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/user/settings/edit" class="btn btn-primary">Edit Profil</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h2 class="card-title h5 mb-4">Lamaran Saya</h2>
        <?php if (empty($applications)): ?>
            <p class="text-muted">Belum ada lamaran.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr><th>Lowongan</th><th>Lokasi</th><th>Status</th><th>Tanggal</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $a): ?>
                            <tr>
                                <td><?= e($a['job_title']) ?></td>
                                <td><?= e($a['location'] ?? '-') ?></td>
                                <td>
                                    <?php $badgeClass = $a['status'] === 'pending' ? 'bg-primary' : ($a['status'] === 'accepted' ? 'bg-success' : ($a['status'] === 'rejected' ? 'bg-danger' : 'bg-secondary')); ?>
                                    <span class="badge <?= $badgeClass ?>"><?= e($a['status']) ?></span>
                                </td>
                                <td><?= e($a['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
