<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fitur pencarian di dashboard
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_type = isset($_GET['filter_type']) ? mysqli_real_escape_string($conn, $_GET['filter_type']) : 'all';

// Hitung statistik
$total_buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku"))['total'];
$total_anggota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='anggota'"))['total'];
$total_peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status='dipinjam'"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi"))['total'];
$total_denda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(denda) as total FROM transaksi"))['total'];

// Query transaksi terbaru dengan pencarian
$query_transaksi = "SELECT t.*, u.nama_lengkap, u.username, b.judul, b.penulis 
                   FROM transaksi t
                   JOIN users u ON t.user_id = u.id
                   JOIN buku b ON t.buku_id = b.id";

if ($search) {
    if ($filter_type == 'kode') {
        $query_transaksi .= " WHERE t.kode_transaksi LIKE '%$search%'";
    } elseif ($filter_type == 'peminjam') {
        $query_transaksi .= " WHERE u.nama_lengkap LIKE '%$search%' OR u.username LIKE '%$search%'";
    } elseif ($filter_type == 'buku') {
        $query_transaksi .= " WHERE b.judul LIKE '%$search%' OR b.penulis LIKE '%$search%'";
    } else {
        $query_transaksi .= " WHERE (t.kode_transaksi LIKE '%$search%' 
                            OR u.nama_lengkap LIKE '%$search%' 
                            OR u.username LIKE '%$search%'
                            OR b.judul LIKE '%$search%'
                            OR b.penulis LIKE '%$search%')";
    }
}

$query_transaksi .= " ORDER BY t.created_at DESC LIMIT 10";
$transaksi_terbaru = mysqli_query($conn, $query_transaksi);

// Query buku terbaru dengan pencarian
$query_buku = "SELECT * FROM buku";
if ($search && $filter_type == 'buku') {
    $query_buku .= " WHERE judul LIKE '%$search%' OR penulis LIKE '%$search%' OR penerbit LIKE '%$search%'";
}
$query_buku .= " ORDER BY created_at DESC LIMIT 5";
$buku_terbaru = mysqli_query($conn, $query_buku);

// Query anggota terbaru dengan pencarian
$query_anggota = "SELECT * FROM users WHERE role='anggota'";
if ($search && $filter_type == 'anggota') {
    $query_anggota .= " AND (nama_lengkap LIKE '%$search%' OR username LIKE '%$search%' OR no_anggota LIKE '%$search%')";
}
$query_anggota .= " ORDER BY created_at DESC LIMIT 5";
$anggota_terbaru = mysqli_query($conn, $query_anggota);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perpustakaan Digital</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Dashboard Admin</h1>
                <p>Selamat datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></p>
            </div>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>

        <!-- Form Pencarian Lanjutan -->
        <div class="dashboard-search">
            <div class="search-header">
                <h3>Pencarian Cepat</h3>
                <p>Cari transaksi, buku, atau anggota</p>
            </div>
            <form method="GET" class="advanced-search-form">
                <div class="search-row">
                    <div class="search-input-group">
                        <input type="text" name="search" placeholder="Masukkan kata kunci pencarian..."
                            value="<?= htmlspecialchars($search) ?>" class="search-input-large">
                    </div>
                    <div class="search-filter-group">
                        <select name="filter_type" class="filter-select">
                            <option value="all" <?= $filter_type == 'all' ? 'selected' : '' ?>>Semua Kategori</option>
                            <option value="kode" <?= $filter_type == 'kode' ? 'selected' : '' ?>>Kode Transaksi</option>
                            <option value="peminjam" <?= $filter_type == 'peminjam' ? 'selected' : '' ?>>Nama Peminjam</option>
                            <option value="buku" <?= $filter_type == 'buku' ? 'selected' : '' ?>>Judul/Penulis Buku</option>
                            <option value="anggota" <?= $filter_type == 'anggota' ? 'selected' : '' ?>>Nama Anggota</option>
                        </select>
                        <button type="submit" class="btn-search">Cari</button>
                        <?php if ($search): ?>
                            <a href="index.php" class="btn-reset">Reset</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <?php if ($search): ?>
                <div class="search-result-info">
                    <strong>Hasil pencarian untuk:</strong> "<?= htmlspecialchars($search) ?>"
                    di kategori <strong><?= ucfirst($filter_type) ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <!-- Menu Navigasi -->
        <div class="menu">
            <a href="buku.php" class="menu-item">Kelola Buku</a>
            <a href="anggota.php" class="menu-item">Kelola Anggota</a>
            <a href="transaksi.php" class="menu-item">Manajemen Transaksi</a>
            <a href="laporan.php" class="menu-item">Laporan</a>
        </div>

        <!-- Statistik -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-info">
                    <h3>Total Buku</h3>
                    <p class="stat-number"><?= $total_buku ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3>Total Anggota</h3>
                    <p class="stat-number"><?= $total_anggota ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <div class="stat-info">
                    <h3>Buku Dipinjam</h3>
                    <p class="stat-number"><?= $total_peminjaman ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <h3>Total Denda</h3>
                    <p class="stat-number">Rp<?= number_format($total_denda ?? 0) ?>
                </div>
            </div>
        </div>

        <!-- Hasil Pencarian - Transaksi -->
        <div class="section">
            <div class="section-header">
                <h2>Transaksi Terbaru</h2>
                <?php if ($search && mysqli_num_rows($transaksi_terbaru) > 0): ?>
                    <span class="result-count">Ditemukan <?= mysqli_num_rows($transaksi_terbaru) ?> transaksi</span>
                <?php endif; ?>
            </div>

            <?php if (mysqli_num_rows($transaksi_terbaru) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Tgl Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($transaksi_terbaru)):
                            $tgl = $row['batas_kembali'] ?? null;
                            echo !empty($tgl) ? date('d/m/Y', strtotime($tgl)) : '-';
                        ?>
                            <tr class="<?= $is_late ? 'late-row' : '' ?>">
                                <td><strong><?= $row['kode_transaksi'] ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?> <small>(<?= $row['username'] ?>)</small></td>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= htmlspecialchars($row['penulis']) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td>
                                    <?= !empty($row['tanggal_kembali'])
                                        ? date('d/m/Y', strtotime($row['tanggal_kembali']))
                                        : '-' ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'dipinjam'): ?>
                                        <?php $is_late = $is_late ?? false; ?>

                                    <?php if ($is_late): ?>
                                            <span class="status-late">⚠️ Terlambat</span>
                                        <?php else: ?>
                                            <span class="status-borrowed">📖 Dipinjam</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="status-returned">✅ Dikembalikan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="transaksi.php?search=<?= $row['kode_transaksi'] ?>" class="btn-view">Detail</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <?php if ($search): ?>
                        Tidak ada transaksi ditemukan untuk "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        Belum ada transaksi terbaru!
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hasil Pencarian - Buku Terbaru -->
        <?php if (!$search || ($search && $filter_type == 'buku')): ?>
            <div class="section">
                <div class="section-header">
                    <h2>Buku Terbaru</h2>
                    <?php if ($search && $filter_type == 'buku' && mysqli_num_rows($buku_terbaru) > 0): ?>
                        <span class="result-count">Ditemukan <?= mysqli_num_rows($buku_terbaru) ?> buku</span>
                    <?php endif; ?>
                </div>

                <?php if (mysqli_num_rows($buku_terbaru) > 0): ?>
                    <div class="buku-grid">
                        <?php while ($buku = mysqli_fetch_assoc($buku_terbaru)): ?>
                            <div class="buku-card">
                                <div class="buku-icon">📖</div>
                                <div class="buku-info">
                                    <h4><?= htmlspecialchars($buku['judul']) ?></h4>
                                    <p>Penulis: <?= htmlspecialchars($buku['penulis']) ?></p>
                                    <p>Penerbit: <?= htmlspecialchars($buku['penerbit']) ?></p>
                                    <p>Stok: <strong><?= $buku['stok'] ?></strong> buku</p>
                                    <a href="buku.php?search=<?= urlencode($buku['judul']) ?>" class="btn-view-small">Lihat Detail</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <?php if ($search && $filter_type == 'buku'): ?>
                        <div class="empty-state">Tidak ada buku ditemukan untuk "<?= htmlspecialchars($search) ?>"</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Hasil Pencarian - Anggota Terbaru -->
        <?php if (!$search || ($search && $filter_type == 'anggota')): ?>
            <div class="section">
                <div class="section-header">
                    <h2>Anggota Terbaru</h2>
                    <?php if ($search && $filter_type == 'anggota' && mysqli_num_rows($anggota_terbaru) > 0): ?>
                        <span class="result-count">Ditemukan <?= mysqli_num_rows($anggota_terbaru) ?> anggota</span>
                    <?php endif; ?>
                </div>

                <?php if (mysqli_num_rows($anggota_terbaru) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No Anggota</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($anggota = mysqli_fetch_assoc($anggota_terbaru)): ?>
                                <tr>
                                    <td><?= $anggota['no_anggota'] ?></td>
                                    <td><?= htmlspecialchars($anggota['nama_lengkap']) ?></td>
                                    <td><?= htmlspecialchars($anggota['username']) ?></td>
                                    <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $anggota['status'] ?>">
                                            <?= $anggota['status'] == 'aktif' ? '✅ Aktif' : '❌ Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="anggota.php?search=<?= urlencode($anggota['nama_lengkap']) ?>" class="btn-view-small">Detail</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <?php if ($search && $filter_type == 'anggota'): ?>
                        <div class="empty-state">Tidak ada anggota ditemukan untuk "<?= htmlspecialchars($search) ?>"</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>