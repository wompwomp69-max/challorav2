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

    /** Count per status untuk satu job */
    public function getCountsByJobId(int $jobId): array {
        $stmt = $this->db->prepare('
            SELECT status, COUNT(*) AS cnt FROM applications WHERE job_id = ? GROUP BY status
        ');
        $stmt->execute([$jobId]);
        $rows = [];
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[$r['status']] = (int) $r['cnt'];
        }
        return [
            'total' => (int) ($rows['pending'] ?? 0) + (int) ($rows['accepted'] ?? 0) + (int) ($rows['rejected'] ?? 0),
            'accepted' => (int) ($rows['accepted'] ?? 0),
            'rejected' => (int) ($rows['rejected'] ?? 0),
            'pending' => (int) ($rows['pending'] ?? 0),
        ];
    }

    /** Statistik applicant untuk semua job milik HR (total, accepted, rejected, pending) */
    public function getCountsByHrJobs(int $hrUserId): array {
        $stmt = $this->db->prepare('
            SELECT status, COUNT(*) AS cnt
            FROM applications a
            JOIN jobs j ON j.id = a.job_id AND j.created_by = ?
            GROUP BY status
        ');
        $stmt->execute([$hrUserId]);
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return [
            'total' => (int) ($rows['pending'] ?? 0) + (int) ($rows['accepted'] ?? 0) + (int) ($rows['rejected'] ?? 0),
            'accepted' => (int) ($rows['accepted'] ?? 0),
            'rejected' => (int) ($rows['rejected'] ?? 0),
            'pending' => (int) ($rows['pending'] ?? 0),
        ];
    }

    /** Daftar applicant yang diterima untuk job milik HR (dengan pagination) */
    public function getAcceptedApplicantsForHr(int $hrUserId, int $page = 1, int $perPage = 20): array {
        $offset = max(0, ($page - 1) * $perPage);
        $perPage = (int) $perPage;
        $offset = (int) $offset;
        $sql = "SELECT a.*, u.name, u.email, u.phone,
                j.title AS job_title, j.location AS job_location
            FROM applications a
            JOIN users u ON u.id = a.user_id
            JOIN jobs j ON j.id = a.job_id AND j.created_by = ?
            WHERE a.status = ?
            ORDER BY a.created_at DESC
            LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hrUserId, 'accepted']);
        return $stmt->fetchAll();
    }

    public function countAcceptedForHr(int $hrUserId): int {
        $stmt = $this->db->prepare('
            SELECT COUNT(*)
            FROM applications a
            JOIN jobs j ON j.id = a.job_id AND j.created_by = ?
            WHERE a.status = ?
        ');
        $stmt->execute([$hrUserId, 'accepted']);
        return (int) $stmt->fetchColumn();
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
