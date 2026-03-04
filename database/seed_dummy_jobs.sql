-- 10 lowongan dummy (created_by = HR Admin)
USE challora_recruitment;

SET @hr_id = (SELECT id FROM users WHERE role = 'hr' LIMIT 1);

INSERT INTO jobs (title, description, location, salary_range, created_by) VALUES
('Software Engineer', 'Mencari Software Engineer berpengalaman dalam pengembangan web. Kualifikasi: PHP/JavaScript, database, Git. Fresh graduate dipersilakan melamar.', 'Jakarta Selatan', '8-15 juta', @hr_id),
('Frontend Developer', 'Bergabung sebagai Frontend Developer. Skill: HTML/CSS, JavaScript, React/Vue. Remote work tersedia.', 'Remote', '7-12 juta', @hr_id),
('Data Analyst', 'Analisis data bisnis, membuat report dan dashboard. Mahir Excel, SQL, Python/R. Pengalaman 1+ tahun.', 'Jakarta Pusat', '6-10 juta', @hr_id),
('Product Manager', 'Memimpin pengembangan produk dari riset sampai launch. Komunikasi bagus, berpikir analitis.', 'Jakarta Barat', '15-25 juta', @hr_id),
('UI/UX Designer', 'Mendesain interface yang user-friendly. Figma, prototyping, user research. Portofolio wajib dilampirkan.', 'Bandung', '7-12 juta', @hr_id),
('Backend Developer', 'Membangun API dan sistem backend. Node.js/Python/Go, PostgreSQL/MySQL, cloud (AWS/GCP).', 'Surabaya', '10-18 juta', @hr_id),
('Content Writer', 'Menulis konten untuk blog, sosial media, dan marketing. Bahasa Indonesia & Inggris. SEO basic.', 'Remote', '5-8 juta', @hr_id),
('HR Specialist', 'Rekrutmen, onboarding, administrasi HR. Minimal 2 tahun pengalaman di bidang HR.', 'Tangerang', '7-11 juta', @hr_id),
('DevOps Engineer', 'CI/CD, container (Docker/K8s), monitoring, cloud infrastructure. Linux dan scripting wajib.', 'Jakarta', '12-20 juta', @hr_id),
('Marketing Specialist', 'Campaign digital, sosial media, branding. Google Ads, Meta Ads, analytics. Target-driven.', 'Jakarta Timur', '6-10 juta', @hr_id);
