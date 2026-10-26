<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/database.php';

// Hitung total catatan user
$stmt = $pdo->prepare("SELECT COUNT(*) FROM catatan_harian WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalCatatan = $stmt->fetchColumn();

// Hitung catatan hari ini
$stmt = $pdo->prepare("SELECT COUNT(*) FROM catatan_harian WHERE user_id = ? AND tanggal = CURDATE()");
$stmt->execute([$_SESSION['user_id']]);
$catatanHariIni = $stmt->fetchColumn();

// Hitung catatan bulan ini
$stmt = $pdo->prepare("SELECT COUNT(*) FROM catatan_harian WHERE user_id = ? AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
$stmt->execute([$_SESSION['user_id']]);
$catatanBulanIni = $stmt->fetchColumn();

// 5 catatan terbaru
$stmt = $pdo->prepare("SELECT * FROM catatan_harian WHERE user_id = ? ORDER BY tanggal DESC, created_at DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$catatanTerbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Catatan Harian</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">
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
                <h3>Dashboard</h3>
                <p class="text-subtitle text-muted">Selamat datang, <?= htmlspecialchars($_SESSION['user_nama']) ?>!</p>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="row">
                            <div class="col-6 col-lg-4 col-md-6">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="stats-icon purple">
                                                    <i class="iconly-boldDocument"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 class="text-muted font-semibold">Total Catatan</h6>
                                                <h6 class="font-extrabold mb-0"><?= $totalCatatan ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-4 col-md-6">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="stats-icon blue">
                                                    <i class="iconly-boldCalendar"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 class="text-muted font-semibold">Catatan Hari Ini</h6>
                                                <h6 class="font-extrabold mb-0"><?= $catatanHariIni ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-4 col-md-6">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="stats-icon green">
                                                    <i class="iconly-boldPaper"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 class="text-muted font-semibold">Catatan Bulan Ini</h6>
                                                <h6 class="font-extrabold mb-0"><?= $catatanBulanIni ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4><i class="bi bi-clock-history"></i> Catatan Terbaru</h4>
                                        <a href="catatan.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-lg">
                                                <thead>
                                                    <tr>
                                                        <th>Nomor</th>
                                                        <th>Tanggal</th>
                                                        <th>Deskripsi</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (count($catatanTerbaru) > 0): ?>
                                                        <?php foreach ($catatanTerbaru as $row): ?>
                                                            <tr>
                                                                <td class="text-bold-500"><?= htmlspecialchars($row['nomor']) ?></td>
                                                                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                                                <td><?= htmlspecialchars(mb_substr($row['deskripsi'], 0, 80)) ?><?= mb_strlen($row['deskripsi']) > 80 ? '...' : '' ?></td>
                                                                <td><?= htmlspecialchars(mb_substr($row['keterangan'] ?? '-', 0, 50)) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted py-4">
                                                                <i class="bi bi-journal-text" style="font-size: 2rem;"></i>
                                                                <p class="mt-2">Belum ada catatan. <a href="catatan_tambah.php">Tambah catatan pertama!</a></p>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
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

