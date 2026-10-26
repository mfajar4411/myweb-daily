<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor = trim($_POST['nomor'] ?? '');
    $tanggal = $_POST['tanggal'] ?? '';
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (empty($nomor) || empty($tanggal) || empty($deskripsi)) {
        $error = 'Nomor, tanggal, dan deskripsi harus diisi!';
    } else {
        $stmt = $pdo->prepare("INSERT INTO catatan_harian (nomor, tanggal, deskripsi, keterangan, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nomor, $tanggal, $deskripsi, $keterangan, $_SESSION['user_id']]);
        header('Location: catatan.php?success=' . urlencode('Catatan berhasil ditambahkan!'));
        exit;
    }
}

// Auto generate nomor
$stmt = $pdo->prepare("SELECT COUNT(*) + 1 FROM catatan_harian WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$nextNum = $stmt->fetchColumn();
$autoNomor = 'CH-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Catatan - Catatan Harian</title>
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
                            <h3>Tambah Catatan</h3>
                            <p class="text-subtitle text-muted">Buat catatan harian baru</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="catatan.php">Catatan</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Form Tambah Catatan</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form action="catatan_tambah.php" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nomor" class="form-label">Nomor <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nomor" name="nomor" value="<?= htmlspecialchars($nomor ?? $autoNomor) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal ?? date('Y-m-d')) ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" placeholder="Tuliskan deskripsi catatan harian..." required><?= htmlspecialchars($deskripsi ?? '') ?></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"><?= htmlspecialchars($keterangan ?? '') ?></textarea>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="catatan.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
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

