-- Extended user profile: keluarga, pendidikan, pengalaman kerja
-- Run after schema.sql

USE challora_recruitment;

-- Data keluarga & pendidikan (pendidikan terakhir)
ALTER TABLE users 
  ADD COLUMN father_name VARCHAR(100) DEFAULT NULL,
  ADD COLUMN mother_name VARCHAR(100) DEFAULT NULL,
  ADD COLUMN marital_status VARCHAR(50) DEFAULT NULL COMMENT 'single, married, etc',
  ADD COLUMN education_level VARCHAR(100) DEFAULT NULL COMMENT 'pendidikan terakhir',
  ADD COLUMN graduation_year VARCHAR(10) DEFAULT NULL,
  ADD COLUMN education_major VARCHAR(150) DEFAULT NULL COMMENT 'jurusan',
  ADD COLUMN education_university VARCHAR(200) DEFAULT NULL;

-- Pengalaman kerja (bisa banyak)
CREATE TABLE IF NOT EXISTS user_work_experiences (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL COMMENT 'judul/jabatan',
  year_start VARCHAR(10) NOT NULL,
  year_end VARCHAR(10) NOT NULL,
  description TEXT DEFAULT NULL,
  sort_order INT DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
