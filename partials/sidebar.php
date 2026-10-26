<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="dashboard.php"><img src="assets/images/logo/logo.png" alt="Logo" srcset=""></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                    <a href="dashboard.php" class="sidebar-link">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-title">Data</li>

                <li class="sidebar-item <?= in_array($currentPage, ['catatan.php', 'catatan_tambah.php', 'catatan_edit.php']) ? 'active' : '' ?>">
                    <a href="catatan.php" class="sidebar-link">
                        <i class="bi bi-journal-text"></i>
                        <span>Catatan Harian</span>
                    </a>
                </li>

                <li class="sidebar-item <?= $currentPage === 'catatan_print.php' ? 'active' : '' ?>">
                    <a href="catatan_print.php" class="sidebar-link">
                        <i class="bi bi-printer"></i>
                        <span>Cetak Laporan</span>
                    </a>
                </li>

                <li class="sidebar-title">Akun</li>

                <li class="sidebar-item">
                    <a href="logout.php" class="sidebar-link" onclick="return confirm('Yakin ingin logout?')">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>

