<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/database.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM catatan_harian WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    header('Location: catatan.php?deleted=1');
    exit;
}

// Pencarian
$search = trim($_GET['search'] ?? '');
$where = "WHERE user_id = ?";
$params = [$_SESSION['user_id']];

if (!empty($search)) {
    $where .= " AND (nomor LIKE ? OR deskripsi LIKE ? OR keterangan LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

// Pagination
$perPage = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM catatan_harian $where");
$countStmt->execute($params);
$totalRows = $countStmt->fetchColumn();
$totalPages = ceil($totalRows / $perPage);

$stmt = $pdo->prepare("SELECT * FROM catatan_harian $where ORDER BY tanggal DESC, created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$catatan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Harian - CRUD</title>
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
                            <h3>Catatan Harian</h3>
                            <p class="text-subtitle text-muted">Kelola catatan harian Anda</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Catatan Harian</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> Catatan berhasil dihapus!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <section class="section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Daftar Catatan</h4>
                            <a href="catatan_tambah.php" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Tambah Catatan
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- Search -->
                            <form method="GET" class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Cari nomor, deskripsi, atau keterangan..." value="<?= htmlspecialchars($search) ?>">
                                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                                    <?php if (!empty($search)): ?>
                                        <a href="catatan.php" class="btn btn-secondary"><i class="bi bi-x-lg"></i> Reset</a>
                                    <?php endif; ?>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nomor</th>
                                            <th>Tanggal</th>
                                            <th>Deskripsi</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($catatan) > 0): ?>
                                            <?php foreach ($catatan as $i => $row): ?>
                                                <tr>
                                                    <td><?= $offset + $i + 1 ?></td>
                                                    <td class="text-bold-500"><?= htmlspecialchars($row['nomor']) ?></td>
                                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                                    <td><?= htmlspecialchars(mb_substr($row['deskripsi'], 0, 60)) ?><?= mb_strlen($row['deskripsi']) > 60 ? '...' : '' ?></td>
                                                    <td><?= htmlspecialchars(mb_substr($row['keterangan'] ?? '-', 0, 40)) ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="catatan_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info" title="Detail">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <a href="catatan_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <a href="catatan.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus catatan ini?')">
                                                                <i class="bi bi-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <?php if (!empty($search)): ?>
                                                        <i class="bi bi-search" style="font-size: 2rem;"></i>
                                                        <p class="mt-2">Tidak ditemukan catatan dengan kata kunci "<?= htmlspecialchars($search) ?>"</p>
                                                    <?php else: ?>
                                                        <i class="bi bi-journal-text" style="font-size: 2rem;"></i>
                                                        <p class="mt-2">Belum ada catatan. <a href="catatan_tambah.php">Tambah catatan pertama!</a></p>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Page navigation" class="mt-3">
                                    <ul class="pagination pagination-primary justify-content-center">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                                                <i class="bi bi-chevron-left"></i>
                                            </a>
                                        </li>
                                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
                                                <i class="bi bi-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>

                            <div class="text-muted text-center mt-2">
                                Menampilkan <?= count($catatan) ?> dari <?= $totalRows ?> catatan
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

