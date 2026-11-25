<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$pesan = '';
$edit_data = null;

// Hapus Data Guru
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM guru WHERE id = ?");
    $stmt->execute([$_GET['hapus']]);
    header("Location: guru.php?status=deleted");
    exit;
}

// Ambil Data untuk Form Edit
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM guru WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}

// Simpan Data Baru atau Update Data Lama
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip']);
    $nama = trim($_POST['nama_guru']);
    $mapel = trim($_POST['mata_pelajaran']);
    $jenjang = $_POST['jenjang_ajar'];
    $no_hp = trim($_POST['no_hp']);

    try {
        if (!empty($_POST['id'])) {
            $stmt = $pdo->prepare("UPDATE guru SET nip=?, nama_guru=?, mata_pelajaran=?, jenjang_ajar=?, no_hp=? WHERE id=?");
            $stmt->execute([$nip, $nama, $mapel, $jenjang, $no_hp, $_POST['id']]);
            header("Location: guru.php?status=updated");
            exit;
        } else {
            $stmt = $pdo->prepare("INSERT INTO guru (nip, nama_guru, mata_pelajaran, jenjang_ajar, no_hp) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nip, $nama, $mapel, $jenjang, $no_hp]);
            header("Location: guru.php?status=added");
            exit;
        }
    } catch (PDOException $e) {
        $pesan = "Gagal menyimpan data (pastikan NIP tidak duplikat): " . $e->getMessage();
    }
}

$data_guru = $pdo->query("SELECT * FROM guru ORDER BY nama_guru ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Data Guru - Admin Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">Panel Admin Sekolah</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="dashboard.php">PPDB & Pengumuman</a>
            <a class="nav-link active" href="guru.php">Data Guru</a>
            <a class="nav-link" href="siswa.php">Data Siswa</a>
        </div>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container my-4">
    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Operasi data guru berhasil dilakukan!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($pesan): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Form Tambah / Edit Guru -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    <?= $edit_data ? 'Edit Data Guru' : 'Tambah Guru Baru' ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
                        <div class="mb-2">
                            <label class="form-label">NIP / NUPTK</label>
                            <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($edit_data['nip'] ?? '') ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nama Lengkap & Gelar</label>
                            <input type="text" name="nama_guru" class="form-control" value="<?= htmlspecialchars($edit_data['nama_guru'] ?? '') ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Mata Pelajaran / Tugas</label>
                            <input type="text" name="mata_pelajaran" class="form-control" value="<?= htmlspecialchars($edit_data['mata_pelajaran'] ?? '') ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Jenjang Mengajar</label>
                            <select name="jenjang_ajar" class="form-select" required>
                                <?php
                                $jenjang_list = ['SD', 'SMP', 'SMA', 'Semua Jenjang'];
                                foreach ($jenjang_list as $j):
                                    $selected = (($edit_data['jenjang_ajar'] ?? '') === $j) ? 'selected' : '';
                                ?>
                                    <option value="<?= $j ?>" <?= $selected ?>><?= $j ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($edit_data['no_hp'] ?? '') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <?= $edit_data ? 'Simpan Perubahan' : 'Tambah Guru' ?>
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="guru.php" class="btn btn-secondary w-100 mt-2">Batal Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Guru -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">Daftar Tenaga Pendidik (<?= count($data_guru) ?>)</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>NIP</th>
                                <th>Nama Guru</th>
                                <th>Mapel</th>
                                <th>Jenjang</th>
                                <th>No. HP</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data_guru)): ?>
                                <tr><td colspan="6" class="text-center text-muted">Belum ada data guru.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($data_guru as $g): ?>
                            <tr>
                                <td><?= htmlspecialchars($g['nip']) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($g['nama_guru']) ?></td>
                                <td><?= htmlspecialchars($g['mata_pelajaran']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= $g['jenjang_ajar'] ?></span></td>
                                <td><?= htmlspecialchars($g['no_hp']) ?></td>
                                <td>
                                    <a href="?edit=<?= $g['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?hapus=<?= $g['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data guru ini?')">Hapus</a>
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