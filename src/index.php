<?php
require_once 'config/database.php';
$stmt = $pdo->query("SELECT * FROM pengumuman ORDER BY tanggal DESC LIMIT 6");
$pengumuman = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Resmi Sekolah Terpadu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Sekolah Terpadu Nusantara</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="index.php">Beranda</a>
            <a class="nav-link" href="ppdb.php">Daftar PPDB</a>
            <a class="nav-link btn btn-light text-primary btn-sm ms-2 px-3" href="admin/login.php">Login Admin</a>
        </div>
    </div>
</nav>

<header class="bg-white py-5 border-bottom">
    <div class="container text-center">
        <h1 class="display-5 fw-bold">Membangun Generasi Unggul & Berkarakter</h1>
        <p class="lead text-muted">Menyelenggarakan pendidikan berkualitas untuk jenjang SD, SMP, dan SMA.</p>
        <a href="ppdb.php" class="btn btn-primary btn-lg mt-2">Pendaftaran Siswa Baru (PPDB)</a>
    </div>
</header>

<main class="container my-5">
    <h3 class="mb-4 fw-bold">Berita & Pengumuman Terbaru</h3>
    <div class="row g-4">
        <?php foreach ($pengumuman as $item): ?>
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <span class="badge bg-info text-dark mb-2"><?= htmlspecialchars($item['kategori']) ?></span>
                    <h5 class="card-title"><?= htmlspecialchars($item['judul']) ?></h5>
                    <p class="card-text text-muted"><?= nl2br(htmlspecialchars($item['isi'])) ?></p>
                </div>
                <div class="card-footer bg-transparent text-muted small">
                    Dipublikasikan: <?= date('d M Y', strtotime($item['tanggal'])) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<footer class="bg-dark text-white text-center py-3 mt-5">
    <small>&copy; <?= date('Y') ?> Sekolah Terpadu Nusantara (SD - SMP - SMA)</small>
</footer>

</body>
</html>