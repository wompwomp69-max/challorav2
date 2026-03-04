-- Add company name to work experience
USE challora_recruitment;

ALTER TABLE user_work_experiences
  ADD COLUMN company_name VARCHAR(200) DEFAULT NULL COMMENT 'nama instansi pekerjaan';
