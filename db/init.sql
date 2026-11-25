CREATE TABLE IF NOT EXISTS guru (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(30) NOT NULL UNIQUE,
    nama_guru VARCHAR(100) NOT NULL,
    mata_pelajaran VARCHAR(100) NOT NULL,
    jenjang_ajar ENUM('SD', 'SMP', 'SMA', 'Semua Jenjang') NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO guru (nip, nama_guru, mata_pelajaran, jenjang_ajar, no_hp) VALUES
('198504122010011001', 'Budi Santoso, S.Pd.', 'Matematika', 'SMA', '081234567890'),
('199008172015022003', 'Siti Aminah, M.Pd.', 'Bahasa Indonesia', 'SMP', '081298765432'),
('199211052018032005', 'Rina Kartika, S.Pd.SD', 'Guru Kelas 4', 'SD', '081345678901');

CREATE TABLE IF NOT EXISTS siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nisn VARCHAR(20) NOT NULL UNIQUE,
    nama_siswa VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    jenjang ENUM('SD', 'SMP', 'SMA') NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    alamat TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO siswa (nisn, nama_siswa, jenis_kelamin, jenjang, kelas, alamat) VALUES
('0081234567', 'Ahmad Fauzi', 'L', 'SMA', 'XI IPA 1', 'Jl. Merdeka No. 12'),
('0109876543', 'Nadia Putri', 'P', 'SMP', 'VIII B', 'Jl. Sudirman No. 45'),
('0145678901', 'Rizky Pratama', 'L', 'SD', '5A', 'Jl. Diponegoro No. 8');