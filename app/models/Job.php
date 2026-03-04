<?php
class Job {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function all(): array {
        $stmt = $this->db->query('
            SELECT j.*, u.name AS created_by_name
            FROM jobs j
            LEFT JOIN users u ON u.id = j.created_by
            ORDER BY j.created_at DESC
        ');
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('
            SELECT j.*, u.name AS created_by_name
            FROM jobs j
            LEFT JOIN users u ON u.id = j.created_by
            WHERE j.id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByCreator(int $createdBy): array {
        $stmt = $this->db->prepare('SELECT * FROM jobs WHERE created_by = ? ORDER BY created_at DESC');
        $stmt->execute([$createdBy]);
        return $stmt->fetchAll();
    }

    public function countByCreator(int $createdBy): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM jobs WHERE created_by = ?');
        $stmt->execute([$createdBy]);
        return (int) $stmt->fetchColumn();
    }

    /** Filter: all, no_apply, has_apply, has_accepted */
    public function countByCreatorFiltered(int $createdBy, string $filter): int {
        $filter = $this->normalizeFilter($filter);
        $base = 'SELECT COUNT(*) FROM jobs j WHERE j.created_by = ?';
        $where = $this->filterWhereClause($filter);
        $sql = $base . $where;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$createdBy]);
        return (int) $stmt->fetchColumn();
    }

    private function normalizeFilter(string $filter): string {
        $allowed = ['all', 'no_apply', 'has_apply', 'has_accepted'];
        return in_array($filter, $allowed, true) ? $filter : 'all';
    }

    private function filterWhereClause(string $filter): string {
        if ($filter === 'no_apply') {
            return ' AND j.id NOT IN (SELECT job_id FROM applications)';
        }
        if ($filter === 'has_apply') {
            return ' AND j.id IN (SELECT job_id FROM applications)';
        }
        if ($filter === 'has_accepted') {
            return " AND j.id IN (SELECT job_id FROM applications WHERE status = 'accepted')";
        }
        return '';
    }

    public function findByCreatorPaginated(int $createdBy, int $page = 1, int $perPage = 10, string $filter = 'all'): array {
        $filter = $this->normalizeFilter($filter);
        $offset = max(0, ($page - 1) * $perPage);
        $perPage = (int) $perPage;
        $offset = (int) $offset;
        $where = $this->filterWhereClause($filter);
        $sql = "SELECT j.*, u.name AS created_by_name
            FROM jobs j
            LEFT JOIN users u ON u.id = j.created_by
            WHERE j.created_by = ? $where
            ORDER BY j.created_at DESC
            LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$createdBy]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare('
            INSERT INTO jobs (title, description, location, salary_range, created_by)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['location'] ?? null,
            $data['salary_range'] ?? null,
            $data['created_by'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare('
            UPDATE jobs SET title = ?, description = ?, location = ?, salary_range = ?
            WHERE id = ?
        ');
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['location'] ?? null,
            $data['salary_range'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM jobs WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function isCreatedBy(int $jobId, int $userId): bool {
        $stmt = $this->db->prepare('SELECT 1 FROM jobs WHERE id = ? AND created_by = ?');
        $stmt->execute([$jobId, $userId]);
        return (bool) $stmt->fetch();
    }
}
