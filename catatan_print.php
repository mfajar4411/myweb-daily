<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/database.php';

$userId = $_SESSION['user_id'];
$singleId = isset($_GET['id']) ? (int)$_GET['id'] : null;

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$isPrintMode = isset($_GET['print']) && $_GET['print'] == '1';

// Query data
$params = [$userId];
$whereClauses = ["user_id = ?"];

if ($singleId) {
    $whereClauses[] = "id = ?";
    $params[] = $singleId;
} else {
    if (!empty($startDate)) {
        $whereClauses[] = "tanggal >= ?";
        $params[] = $startDate;
    }
    if (!empty($endDate)) {
        $whereClauses[] = "tanggal <= ?";
        $params[] = $endDate;
    }
}

$whereSql = implode(" AND ", $whereClauses);
$stmt = $pdo->prepare("SELECT * FROM catatan_harian WHERE $whereSql ORDER BY tanggal ASC, created_at ASC");
$stmt->execute($params);
$catatanList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Jika single ID, kita bisa cetak format lembar catatan per nomor
$isSingleView = ($singleId && count($catatanList) === 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printout Catatan Harian</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <style>
        body {
            background-color: #f2f4f8;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .print-container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-radius: 8px;
        }
        .report-header {
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .report-header h2 {
            font-size: 22px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .report-header p {
            margin-bottom: 0;
            font-size: 14px;
            color: #666;
        }
        .table-print {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table-print th, .table-print td {
            border: 1px solid #333;
            padding: 8px 12px;
            font-size: 13px;
        }
        .table-print th {
            background-color: #f0f0f0 !important;
            font-weight: bold;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            text-align: center;
            width: 220px;
        }
        .signature-space {
            height: 70px;
        }
        @media print {
            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                max-width: 100%;
                margin: 0;
                padding: 15mm 15mm;
                box-shadow: none;
                border-radius: 0;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

<div class="no-print bg-white border-bottom py-3 mb-4 shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="max-width: 900px;">
        <div>
            <a href="catatan.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Data
            </a>
            <a href="dashboard.php" class="btn btn-outline-primary btn-sm ms-1">
                <i class="bi bi-house"></i> Dashboard
            </a>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-success btn-sm">
                <i class="bi bi-printer"></i> Cetak Sekarang (Print / PDF)
            </button>
        </div>
    </div>
</div>

<!-- Form Filter Pencarian Tanggal (No Print) -->
<?php if (!$singleId): ?>
<div class="container-fluid no-print mb-3" style="max-width: 900px;">
    <div class="card p-3 shadow-sm border-0">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label font-bold text-sm">Dari Tanggal:</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= htmlspecialchars($startDate) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label font-bold text-sm">Sampai Tanggal:</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= htmlspecialchars($endDate) ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-filter"></i> Filter Periode
                </button>
                <a href="catatan_print.php" class="btn btn-light-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Area Printout -->
<div class="print-container">
    <div class="report-header text-center">
        <h2>LAPORAN CATATAN HARIAN</h2>
        <p>Pengguna: <strong><?= htmlspecialchars($_SESSION['user_nama']) ?></strong> | Dicetak pada: <?= date('d/m/Y H:i') ?></p>
        <?php if (!empty($startDate) || !empty($endDate)): ?>
            <p class="mt-1">
                Periode: 
                <?= !empty($startDate) ? date('d/m/Y', strtotime($startDate)) : 'Awal' ?> 
                s/d 
                <?= !empty($endDate) ? date('d/m/Y', strtotime($endDate)) : 'Sekarang' ?>
            </p>
        <?php endif; ?>
    </div>

    <?php if ($isSingleView): 
        $item = $catatanList[0];
    ?>
        <!-- Mode Lembar Cetak Satuan -->
        <table class="table table-bordered mt-4">
            <tbody>
                <tr>
                    <th style="width: 25%; background: #f8f9fa;">Nomor Catatan</th>
                    <td><strong><?= htmlspecialchars($item['nomor']) ?></strong></td>
                </tr>
                <tr>
                    <th style="background: #f8f9fa;">Tanggal</th>
                    <td><?= date('d F Y', strtotime($item['tanggal'])) ?></td>
                </tr>
                <tr>
                    <th style="background: #f8f9fa;">Deskripsi Kegiatan</th>
                    <td style="white-space: pre-wrap; min-height: 120px;"><?= nl2br(htmlspecialchars($item['deskripsi'])) ?></td>
                </tr>
                <tr>
                    <th style="background: #f8f9fa;">Keterangan</th>
                    <td style="white-space: pre-wrap;"><?= !empty($item['keterangan']) ? nl2br(htmlspecialchars($item['keterangan'])) : '-' ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <!-- Mode Tabel Laporan Daftar Catatan -->
        <table class="table-print">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Nomor</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 40%;">Deskripsi</th>
                    <th style="width: 25%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($catatanList) > 0): ?>
                    <?php foreach ($catatanList as $idx => $row): ?>
                        <tr>
                            <td class="text-center"><?= $idx + 1 ?></td>
                            <td class="text-center font-bold"><strong><?= htmlspecialchars($row['nomor']) ?></strong></td>
                            <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></td>
                            <td><?= !empty($row['keterangan']) ? nl2br(htmlspecialchars($row['keterangan'])) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">Tidak ada data catatan pada periode ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="signature-section">
        <div class="signature-box">
            <p><?= date('d F Y') ?></p>
            <p>Petugas / Pengguna,</p>
            <div class="signature-space"></div>
            <p class="font-bold border-bottom pb-1 mb-0"><strong><?= htmlspecialchars($_SESSION['user_nama']) ?></strong></p>
            <small class="text-muted">Username: <?= htmlspecialchars($_SESSION['user_username']) ?></small>
        </div>
    </div>
</div>

<script>
    // Otomatis popup print jika parameter ?print=1 disertakan
    <?php if ($isPrintMode): ?>
        window.onload = function() {
            window.print();
        };
    <?php endif; ?>
</script>

</body>
</html>

