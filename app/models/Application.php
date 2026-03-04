<?php
class Application {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function create(int $userId, int $jobId, ?string $cvPath = null): int {
        $stmt = $this->db->prepare('INSERT INTO applications (user_id, job_id, cv_path, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$userId, $jobId, $cvPath, 'pending']);
        return (int) $this->db->lastInsertId();
    }

    /** Cek sudah apply atau belum (unique user_id + job_id) */
    public function hasApplied(int $userId, int $jobId): bool {
        $stmt = $this->db->prepare('SELECT 1 FROM applications WHERE user_id = ? AND job_id = ?');
        $stmt->execute([$userId, $jobId]);
        return (bool) $stmt->fetch();
    }

    /** Daftar applicant per job (untuk HR) - termasuk profil lengkap */
    public function getByJobId(int $jobId): array {
        $stmt = $this->db->prepare('
            SELECT a.*, u.name, u.email, u.phone, u.address,
                u.father_name, u.mother_name, u.marital_status,
                u.education_level, u.graduation_year, u.education_major, u.education_university
            FROM applications a
            JOIN users u ON u.id = a.user_id
            WHERE a.job_id = ?
            ORDER BY a.created_at DESC
        ');
        $stmt->execute([$jobId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM applications WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status): bool {
        $allowed = ['pending', 'accepted', 'rejected'];
        if (!in_array($status, $allowed, true)) return false;
        $stmt = $this->db->prepare('UPDATE applications SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    /** Application milik job yang dibuat oleh userId (untuk auth HR) */
    public function getApplicationForHrJob(int $applicationId, int $hrUserId): ?array {
        $stmt = $this->db->prepare('
            SELECT a.* FROM applications a
            JOIN jobs j ON j.id = a.job_id AND j.created_by = ?
            WHERE a.id = ?
        ');
        $stmt->execute([$hrUserId, $applicationId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Daftar apply user (untuk profile candidate) */
    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare('
            SELECT a.*, j.title AS job_title, j.location
            FROM applications a
            JOIN jobs j ON j.id = a.job_id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
