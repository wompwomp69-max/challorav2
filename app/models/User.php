<?php
class User {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function create(string $name, string $email, string $password, string $role = 'user', ?string $phone = null, ?string $address = null): int {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, $hash, $role, $phone, $address]);
        return (int) $this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }

    public function update(int $id, array $data): bool {
        $allowed = ['name', 'phone', 'address', 'father_name', 'mother_name', 'marital_status',
            'education_level', 'graduation_year', 'education_major', 'education_university'];
        $set = [];
        $params = [];
        foreach ($allowed as $k) {
            if (array_key_exists($k, $data)) {
                $set[] = "`$k` = ?";
                $params[] = $data[$k];
            }
        }
        if (empty($set)) return false;
        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $set) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /** Work experiences */
    public function getWorkExperiences(int $userId): array {
        $stmt = $this->db->prepare('SELECT * FROM user_work_experiences WHERE user_id = ? ORDER BY sort_order, year_start DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function setWorkExperiences(int $userId, array $items): void {
        $this->db->prepare('DELETE FROM user_work_experiences WHERE user_id = ?')->execute([$userId]);
        $stmt = $this->db->prepare('INSERT INTO user_work_experiences (user_id, title, company_name, year_start, year_end, description, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)');
        foreach ($items as $i => $row) {
            if (!empty(trim($row['title'] ?? ''))) {
                $stmt->execute([
                    $userId,
                    trim($row['title']),
                    trim($row['company_name'] ?? ''),
                    trim($row['year_start'] ?? ''),
                    trim($row['year_end'] ?? ''),
                    trim($row['description'] ?? ''),
                    $i
                ]);
            }
        }
    }

    public function updatePassword(int $id, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        return $stmt->execute([$hash, $id]);
    }
}
