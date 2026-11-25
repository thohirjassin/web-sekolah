<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$pesan = '';
$edit_data = null;

// Hapus Data Siswa
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM siswa WHERE id = ?");
    $stmt->execute([$_GET['hapus']]);
    header("Location: siswa.php?status=deleted");
    exit;
}

// Ambil Data untuk Form Edit
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM siswa WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}

// Simpan Data Baru atau Update Data Lama
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = trim($_POST['nisn']);
    $nama = trim($_POST['nama_siswa']);
    $jk = $_POST['jenis_kelamin'];
    $jenjang = $_POST['jenjang'];
    $kelas = trim($_POST['kelas']);
    $alamat = trim($_POST['alamat']);

    try {
        if (!empty($_POST['id'])) {
            $stmt = $pdo->prepare("UPDATE siswa SET nisn=?, nama_siswa=?, jenis_kelamin=?, jenjang=?, kelas=?, alamat=? WHERE id=?");
            $stmt->execute([$nisn, $nama, $jk, $jenjang, $kelas, $alamat, $_POST['id']]);
            header("Location: siswa.php?status=updated");
            exit;
        } else {
            $stmt = $pdo->prepare("INSERT INTO siswa (nisn, nama_siswa, jenis_kelamin, jenjang, kelas, alamat) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nisn, $nama, $jk, $jenjang, $kelas, $alamat]);
            header("Location: siswa.php?status=added");
            exit;
        }
    } catch (PDOException $e) {
        $pesan = "Gagal menyimpan data (pastikan NISN tidak duplikat): " . $e->getMessage();
    }
}

// Filter berdasarkan jenjang
$filter_jenjang = $_GET['filter_jenjang'] ?? '';
if (in_array($filter_jenjang, ['SD', 'SMP', 'SMA'])) {
    $stmt = $pdo->prepare("SELECT * FROM siswa WHERE jenjang = ? ORDER BY kelas ASC, nama_siswa ASC");
    $stmt->execute([$filter_jenjang]);
    $data_siswa = $stmt->fetchAll();
} else {
    $data_siswa = $pdo->query("SELECT * FROM siswa ORDER BY jenjang ASC, kelas ASC, nama_siswa ASC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Data Siswa - Admin Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">Panel Admin Sekolah</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="dashboard.php">PPDB & Pengumuman</a>
            <a class="nav-link" href="guru.php">Data Guru</a>
            <a class="nav-link active" href="siswa.php">Data Siswa</a>
        </div>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container my-4">
    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Operasi data siswa berhasil dilakukan!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($pesan): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Form Tambah / Edit Siswa -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    <?= $edit_data ? 'Edit Data Siswa' : 'Tambah Siswa Baru' ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
                        <div class="mb-2">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control" value="<?= htmlspecialchars($edit_data['nisn'] ?? '') ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nama Lengkap Siswa</label>
                            <input type="text" name="nama_siswa" class="form-control" value="<?= htmlspecialchars($edit_data['nama_siswa'] ?? '') ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" <?= (($edit_data['jenis_kelamin'] ?? '') === 'L') ? 'selected' : '' ?>>Laki-laki (L)</option>
                                <option value="P" <?= (($edit_data['jenis_kelamin'] ?? '') === 'P') ? 'selected' : '' ?>>Perempuan (P)</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label">Jenjang</label>
                                <select name="jenjang" class="form-select" required>
                                    <?php foreach (['SD', 'SMP', 'SMA'] as $j): ?>
                                        <option value="<?= $j ?>" <?= (($edit_data['jenjang'] ?? '') === $j) ? 'selected' : '' ?>><?= $j ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label">Kelas</label>
                                <input type="text" name="kelas" placeholder="cth: 5A / X IPA 1" class="form-control" value="<?= htmlspecialchars($edit_data['kelas'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" rows="2" class="form-control" required><?= htmlspecialchars($edit_data['alamat'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <?= $edit_data ? 'Simpan Perubahan' : 'Tambah Siswa' ?>
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="siswa.php" class="btn btn-secondary w-100 mt-2">Batal Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Siswa -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Data Siswa Aktif (<?= count($data_siswa) ?>)</span>
                    <div class="btn-group btn-group-sm">
                        <a href="siswa.php" class="btn btn-outline-light <?= $filter_jenjang === '' ? 'active' : '' ?>">Semua</a>
                        <a href="?filter_jenjang=SD" class="btn btn-outline-light <?= $filter_jenjang === 'SD' ? 'active' : '' ?>">SD</a>
                        <a href="?filter_jenjang=SMP" class="btn btn-outline-light <?= $filter_jenjang === 'SMP' ? 'active' : '' ?>">SMP</a>
                        <a href="?filter_jenjang=SMA" class="btn btn-outline-light <?= $filter_jenjang === 'SMA' ? 'active' : '' ?>">SMA</a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>L/P</th>
                                <th>Jenjang & Kelas</th>
                                <th>Alamat</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data_siswa)): ?>
                                <tr><td colspan="6" class="text-center text-muted">Belum ada data siswa.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($data_siswa as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['nisn']) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($s['nama_siswa']) ?></td>
                                <td><?= $s['jenis_kelamin'] ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= $s['jenjang'] ?></span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($s['kelas']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($s['alamat']) ?></td>
                                <td>
                                    <a href="?edit=<?= $s['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?hapus=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data siswa ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>