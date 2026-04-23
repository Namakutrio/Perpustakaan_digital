<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fitur pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_type = isset($_GET['filter_type']) ? mysqli_real_escape_string($conn, $_GET['filter_type']) : 'all';

// Ambil buku yang sedang dipinjam dengan pencarian
$query_pinjam = "SELECT t.*, b.judul, b.penulis, b.penerbit, b.tahun_terbit
                 FROM transaksi t
                 JOIN buku b ON t.buku_id = b.id 
                 WHERE t.user_id = $user_id AND t.status = 'dipinjam'";

if ($search) {
    if ($filter_type == 'judul') {
        $query_pinjam .= " AND b.judul LIKE '%$search%'";
    } elseif ($filter_type == 'penulis') {
        $query_pinjam .= " AND b.penulis LIKE '%$search%'";
    } else {
        $query_pinjam .= " AND (b.judul LIKE '%$search%' OR b.penulis LIKE '%$search%' OR t.kode_transaksi LIKE '%$search%')";
    }
}
$pinjaman_aktif = mysqli_query($conn, $query_pinjam);

// Ambil riwayat peminjaman dengan pencarian
$query_riwayat = "SELECT t.*, b.judul, b.penulis, b.penerbit 
                  FROM transaksi t
                  JOIN buku b ON t.buku_id = b.id 
                  WHERE t.user_id = $user_id AND t.status = 'dikembalikan'";

if ($search) {
    if ($filter_type == 'judul') {
        $query_riwayat .= " AND b.judul LIKE '%$search%'";
    } elseif ($filter_type == 'penulis') {
        $query_riwayat .= " AND b.penulis LIKE '%$search%'";
    } elseif ($filter_type == 'kode') {
        $query_riwayat .= " AND t.kode_transaksi LIKE '%$search%'";
    } else {
        $query_riwayat .= " AND (b.judul LIKE '%$search%' OR b.penulis LIKE '%$search%' OR t.kode_transaksi LIKE '%$search%')";
    }
}
$query_riwayat .= " ORDER BY t.created_at DESC LIMIT 10";
$riwayat = mysqli_query($conn, $query_riwayat);

// Hitung statistik
$total_pernah_dipinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE user_id=$user_id"))['total'];
$total_sedang_dipinjam = mysqli_num_rows($pinjaman_aktif);
$total_denda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(denda) as total FROM transaksi WHERE user_id=$user_id"))['total'];

// Rekomendasi buku (buku terbaru yang belum pernah dipinjam)
$query_rekomendasi = "SELECT * FROM buku WHERE stok > 0 AND id NOT IN (
                        SELECT buku_id FROM transaksi WHERE user_id=$user_id
                      ) ORDER BY created_at DESC LIMIT 4";
$rekomendasi = mysqli_query($conn, $query_rekomendasi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota - Perpustakaan Digital</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Dashboard Anggota</h1>
                <p>Selamat datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></p>
                <small>No Anggota: <?= $_SESSION['no_anggota'] ?? 'Belum ada' ?></small>
            </div>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
        
        <!-- Form Pencarian Lanjutan -->
        <div class="dashboard-search">
            <div class="search-header">
                <h3>🔍 Cari Peminjaman Saya</h3>
                <p>Cari berdasarkan judul buku, penulis, atau kode transaksi</p>
            </div>
            <form method="GET" class="advanced-search-form">
                <div class="search-row">
                    <div class="search-input-group">
                        <input type="text" name="search" placeholder="Masukkan judul, penulis, atau kode transaksi..." 
                               value="<?= htmlspecialchars($search) ?>" class="search-input-large">
                    </div>
                    <div class="search-filter-group">
                        <select name="filter_type" class="filter-select">
                            <option value="all" <?= $filter_type == 'all' ? 'selected' : '' ?>>Semua Kategori</option>
                            <option value="judul" <?= $filter_type == 'judul' ? 'selected' : '' ?>>Judul Buku</option>
                            <option value="penulis" <?= $filter_type == 'penulis' ? 'selected' : '' ?>>Nama Penulis</option>
                            <option value="kode" <?= $filter_type == 'kode' ? 'selected' : '' ?>>Kode Transaksi</option>
                        </select>
                        <button type="submit" class="btn-search">Cari</button>
                        <?php if($search): ?>
                            <a href="index.php" class="btn-reset">Reset</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
            
            <?php if($search): ?>
                <div class="search-result-info">
                    <strong>Hasil pencarian untuk:</strong> "<?= htmlspecialchars($search) ?>" 
                    di kategori <strong><?= ucfirst($filter_type) ?></strong>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Statistik Anggota -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-info">
                    <h3>Total Peminjaman</h3>
                    <p class="stat-number"><?= $total_pernah_dipinjam ?></p>
                    <small>Sepanjang masa</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <div class="stat-info">
                    <h3>Sedang Dipinjam</h3>
                    <p class="stat-number"><?= $total_sedang_dipinjam ?></p>
                    <small>Buku aktif</small>
                </div>
            </div>
            <?php if($total_denda > 0): ?>
                <div class="stat-card warning">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <h3>Total Denda</h3>
                        <p class="stat-number">Rp <?= number_format($total_denda, 0, ',', '.') ?></p>
                        <small>⚠️ Segera lunasi</small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Menu Navigasi -->
        <div class="menu">
            <a href="pinjam_buku.php" class="menu-item">📖 Pinjam Buku</a>
            <a href="transaksi_saya.php" class="menu-item">💰 Riwayat Transaksi</a>
        </div>
        
        <!-- Buku yang Sedang Dipinjam -->
        <div class="section">
            <div class="section-header">
                <h2>Buku yang Sedang Dipinjam</h2>
                <?php if($search && mysqli_num_rows($pinjaman_aktif) > 0): ?>
                    <span class="result-count">Ditemukan <?= mysqli_num_rows($pinjaman_aktif) ?> buku</span>
                <?php endif; ?>
            </div>
            
            <?php if(mysqli_num_rows($pinjaman_aktif) > 0): ?>
                <div class="borrowed-books">
                    <?php while($row = mysqli_fetch_assoc($pinjaman_aktif)): 
                        $today = new DateTime();
                        $return_date = new DateTime($row['tanggal_kembali']);
                        $diff = $today->diff($return_date);
                        $sisa_hari = $return_date > $today ? $diff->days : -$diff->days;
                        $is_late = $return_date < $today;
                        $progress = $return_date > $today ? (7 - $sisa_hari) / 7 * 100 : 100;
                    ?>
                    <div class="borrowed-card <?= $is_late ? 'late-card' : '' ?>">
                        <div class="borrowed-header">
                            <div class="book-title"><?= htmlspecialchars($row['judul']) ?></div>
                            <div class="book-code"><?= $row['kode_transaksi'] ?></div>
                        </div>
                        <div class="borrowed-details">
                            <div class="detail-item">
                                <span class="detail-label">Penulis:</span>
                                <span class="detail-value"><?= htmlspecialchars($row['penulis']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Tanggal Pinjam:</span>
                                <span class="detail-value"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Batas Kembali:</span>
                                <span class="detail-value <?= $is_late ? 'text-danger' : 'text-success' ?>">
                                    <?= date('d/m/Y', strtotime($row['tanggal_kembali'])) ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Sisa Waktu:</span>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $progress ?>%; background: <?= $is_late ? '#f56565' : '#48bb78' ?>"></div>
                                    <span class="progress-text">
                                        <?php if($is_late): ?>
                                            ⚠️ Terlambat <?= $sisa_hari ?> hari
                                        <?php else: ?>
                                            ✅ <?= $sisa_hari ?> hari lagi
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="borrowed-actions">
                            <a href="kembali_buku.php?id=<?= $row['id'] ?>" class="btn-return" 
                               onclick="return confirm('Kembalikan buku <?= htmlspecialchars($row['judul']) ?>?')">
                               📖 Kembalikan Sekarang
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <?php if($search): ?>
                        Tidak ada buku ditemukan untuk "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        Tidak ada buku yang sedang dipinjam. 
                        <a href="pinjam_buku.php">Pinjam buku sekarang →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Riwayat Peminjaman -->
        <div class="section">
            <div class="section-header">
                <h2>Riwayat Peminjaman Terbaru</h2>
                <?php if($search && mysqli_num_rows($riwayat) > 0): ?>
                    <span class="result-count">Ditemukan <?= mysqli_num_rows($riwayat) ?> transaksi</span>
                <?php endif; ?>
            </div>
            
            <?php if(mysqli_num_rows($riwayat) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Denda</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($riwayat)): ?>
                        <tr>
                            <td><strong><?= $row['kode_transaksi'] ?></strong></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= htmlspecialchars($row['penulis']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                            <td><?= $row['tanggal_kembali'] ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-' ?></td>
                            <td class="<?= $row['denda'] > 0 ? 'text-danger' : '' ?>">
                                <?php if($row['denda'] > 0): ?>
                                    Rp <?= number_format($row['denda'], 0, ',', '.') ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="status-returned">✅ Dikembalikan</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <?php if(mysqli_num_rows($riwayat) >= 10): ?>
                    <div class="view-all">
                        <a href="transaksi_saya.php">Lihat semua riwayat (<?= $total_pernah_dipinjam ?> transaksi) →</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-state">
                    <?php if($search): ?>
                        Tidak ada riwayat ditemukan untuk "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        Belum ada riwayat peminjaman!
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Rekomendasi Buku -->
        <?php if(!$search && mysqli_num_rows($rekomendasi) > 0): ?>
        <div class="section">
            <div class="section-header">
                <h2>Rekomendasi Buku untuk Anda</h2>
                <p>Buku-buku terbaru yang mungkin Anda sukai</p>
            </div>
            <div class="buku-grid">
                <?php while($buku = mysqli_fetch_assoc($rekomendasi)): ?>
                <div class="buku-card">
                    <div class="buku-icon">📖</div>
                    <div class="buku-info">
                        <h4><?= htmlspecialchars($buku['judul']) ?></h4>
                        <p>Penulis: <?= htmlspecialchars($buku['penulis']) ?></p>
                        <p>Penerbit: <?= htmlspecialchars($buku['penerbit']) ?></p>
                        <p>Stok: <strong><?= $buku['stok'] ?></strong> buku</p>
                        <a href="pinjam_buku.php?search=<?= urlencode($buku['judul']) ?>" class="btn-pinjam-small">Pinjam Sekarang</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>