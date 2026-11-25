<?php
require_once 'config/database.php';
$sukses = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO ppdb (nama_siswa, jenjang, asal_sekolah, no_hp_ortu) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nama_siswa'],
        $_POST['jenjang'],
        $_POST['asal_sekolah'],
        $_POST['no_hp_ortu']
    ]);
    $sukses = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PPDB Online - Sekolah Terpadu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 600px;">
    <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3">&larr; Kembali ke Beranda</a>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">Formulir Pendaftaran Siswa Baru (PPDB)</div>
        <div class="card-body">
            <?php if ($sukses): ?>
                <div class="alert alert-success">Pendaftaran berhasil dikirim! Panitia akan segera menghubungi nomor orang tua/wali.</div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap Calon Siswa</label>
                    <input type="text" name="nama_siswa" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenjang Pendidikan yang Dituju</label>
                    <select name="jenjang" class="form-select" required>
                        <option value="SD">Sekolah Dasar (SD)</option>
                        <option value="SMP">Sekolah Menengah Pertama (SMP)</option>
                        <option value="SMA">Sekolah Menengah Atas (SMA)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Asal Sekolah (TK / SD / SMP)</label>
                    <input type="text" name="asal_sekolah" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp Orang Tua / Wali</label>
                    <input type="text" name="no_hp_ortu" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Kirim Pendaftaran</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>