<div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card my-auto" style="max-width: 1200px; min-width: 60%;">
        <div class="card-body">
            <h1 class="card-title h4 mb-4 text-center">Buat Lowongan</h1>
            <form method="post" action="<?= BASE_URL ?>/index.php?url=hr/jobs/create">
                <div class="mb-3">
                    <label class="form-label" for="title">Judul</label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?= e($old['title']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="description">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required><?= e($old['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="location">Lokasi</label>
                    <input type="text" class="form-control" id="location" name="location" value="<?= e($old['location']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="salary_range">Kisaran Gaji</label>
                    <input type="text" class="form-control" id="salary_range" name="salary_range" value="<?= e($old['salary_range']) ?>" placeholder="Contoh: 5-8 jt">
                </div>
                <?php if (!empty($error)): ?><p class="text-danger"><?= e($error) ?></p><?php endif; ?>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= BASE_URL ?>/hr/jobs" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
