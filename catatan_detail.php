<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/database.php';

$id = (int) ($_GET['id'] ?? 0);

// Ambil data catatan berdasarkan id dan user_id
$stmt = $pdo->prepare("SELECT * FROM catatan_harian WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$catatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$catatan) {
    header('Location: catatan.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Catatan - Catatan Harian</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <div id="app">
        <?php include 'partials/sidebar.php'; ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Detail Catatan</h3>
                            <p class="text-subtitle text-muted">Informasi lengkap catatan harian</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="catatan.php">Catatan</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <section class="section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Nomor Catatan: <?= htmlspecialchars($catatan['nomor']) ?></h4>
                            <div class="d-flex gap-2">
                                <a href="catatan_print.php?id=<?= $catatan['id'] ?>" target="_blank" class="btn btn-outline-secondary">
                                    <i class="bi bi-printer"></i> Cetak Dokumen Ini
                                </a>
                                <a href="catatan_edit.php?id=<?= $catatan['id'] ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%;">Nomor</th>
                                            <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($catatan['nomor']) ?></span></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td><?= date('d F Y', strtotime($catatan['tanggal'])) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Deskripsi</th>
                                            <td style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($catatan['deskripsi'])) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td style="white-space: pre-wrap;"><?= !empty($catatan['keterangan']) ? nl2br(htmlspecialchars($catatan['keterangan'])) : '<em class="text-muted">- Tidak ada keterangan -</em>' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat Pada</th>
                                            <td><?= date('d/m/Y H:i:s', strtotime($catatan['created_at'])) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Terakhir Diperbarui</th>
                                            <td><?= date('d/m/Y H:i:s', strtotime($catatan['updated_at'])) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <a href="catatan.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php include 'partials/footer.php'; ?>
        </div>
    </div>
    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>

