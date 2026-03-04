<div class="card">
    <div class="card-body">
        <h1 class="card-title h4 mb-4">Pengaturan Profil</h1>
        <form method="post" action="<?= BASE_URL ?>/index.php?url=user/settings/edit">
            <h5 class="mb-3">Data Pribadi</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" required value="<?= e($user['name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" class="form-control" id="email" value="<?= e($user['email']) ?>" readonly disabled>
                    <small class="text-muted">Email tidak dapat diubah</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="phone">Nomor Telepon</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= e($user['phone'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label" for="address">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?= e($user['address'] ?? '') ?></textarea>
                </div>
            </div>

            <h5 class="mb-3 mt-4">Data Keluarga</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="father_name">Nama Ayah</label>
                    <input type="text" class="form-control" id="father_name" name="father_name" value="<?= e($user['father_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="mother_name">Nama Ibu</label>
                    <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?= e($user['mother_name'] ?? '') ?>">
                </div>
                <div class="col-md-6 mt-2">
                    <label class="form-label" for="marital_status">Status Pernikahan</label>
                    <select class="form-select" id="marital_status" name="marital_status">
                        <option value="">— Pilih —</option>
                        <option value="single" <?= ($user['marital_status'] ?? '') === 'single' ? 'selected' : '' ?>>Belum menikah</option>
                        <option value="married" <?= ($user['marital_status'] ?? '') === 'married' ? 'selected' : '' ?>>Menikah</option>
                        <option value="divorced" <?= ($user['marital_status'] ?? '') === 'divorced' ? 'selected' : '' ?>>Cerai</option>
                        <option value="widowed" <?= ($user['marital_status'] ?? '') === 'widowed' ? 'selected' : '' ?>>Duda/Janda</option>
                    </select>
                </div>
            </div>

            <h5 class="mb-3 mt-4">Data Pendidikan (Pendidikan Terakhir)</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="education_level">Pendidikan Terakhir</label>
                    <input type="text" class="form-control" id="education_level" name="education_level" placeholder="Contoh: S1, D3, SMA" value="<?= e($user['education_level'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="graduation_year">Tahun Lulus</label>
                    <input type="text" class="form-control" id="graduation_year" name="graduation_year" placeholder="Contoh: 2020" value="<?= e($user['graduation_year'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="education_major">Jurusan</label>
                    <input type="text" class="form-control" id="education_major" name="education_major" value="<?= e($user['education_major'] ?? '') ?>">
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label" for="education_university">Nama Universitas/Instansi</label>
                    <input type="text" class="form-control" id="education_university" name="education_university" value="<?= e($user['education_university'] ?? '') ?>">
                </div>
            </div>

            <h5 class="mb-3 mt-4">Pengalaman Kerja</h5>
            <div id="work-experiences">
                <?php
                $exps = $workExperiences ?? [];
                if (empty($exps)) $exps = [['title' => '', 'company_name' => '', 'year_start' => '', 'year_end' => '', 'description' => '']];
                foreach ($exps as $idx => $exp):
                ?>
                <div class="work-exp-row card mb-3 p-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Judul / Jabatan</label>
                            <input type="text" class="form-control" name="work_title[]" value="<?= e($exp['title'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Nama Instansi Pekerjaan</label>
                            <input type="text" class="form-control" name="work_company[]" placeholder="Nama perusahaan / instansi" value="<?= e($exp['company_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Tahun Mulai</label>
                            <input type="text" class="form-control" name="work_year_start[]" placeholder="2020" value="<?= e($exp['year_start'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Tahun Selesai</label>
                            <input type="text" class="form-control" name="work_year_end[]" placeholder="2023" value="<?= e($exp['year_end'] ?? '') ?>">
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="work_description[]" rows="2"><?= e($exp['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="add-work-exp">+ Tambah Pengalaman Kerja</button>

            <?php if (!empty($error)): ?><p class="text-danger"><?= e($error) ?></p><?php endif; ?>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= BASE_URL ?>/user/settings" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('add-work-exp').addEventListener('click', function() {
    const tpl = document.querySelector('.work-exp-row').cloneNode(true);
    tpl.querySelectorAll('input, textarea').forEach(el => el.value = '');
    document.getElementById('work-experiences').appendChild(tpl);
});
</script>
