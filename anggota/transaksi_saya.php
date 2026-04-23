<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =====================================
   AMBIL TRANSAKSI TERHUBUNG INDEX.PHP
===================================== */
$query = "SELECT t.*, b.judul, b.penulis, b.penerbit, b.cover
          FROM transaksi t
          JOIN buku b ON t.buku_id = b.id
          WHERE t.user_id = '$user_id'
          ORDER BY t.created_at DESC";

$transaksi = mysqli_query($conn, $query);

/* =====================================
   STATISTIK
===================================== */
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM transaksi WHERE user_id='$user_id'"))['total'];

$total_dipinjam = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM transaksi WHERE user_id='$user_id' AND status='dipinjam'"))['total'];

$total_dikembalikan = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM transaksi WHERE user_id='$user_id' AND status='dikembalikan'"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transaksi Saya</title>
<link rel="stylesheet" href="../assets/style.css">

<style>
.cover{
    width:55px;
    height:75px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #ddd;
}

.no-cover{
    width:55px;
    height:75px;
    background:#eee;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:10px;
    color:#777;
    border-radius:8px;
}
</style>

</head>
<body>

<div class="container">

<!-- HEADER -->
<div class="header">
    <div>
        <h1>Transaksi Saya</h1>
        <p>Selamat datang,
            <strong><?= $_SESSION['nama_lengkap']; ?></strong>
        </p>
        <small>No Anggota:
            <?= $_SESSION['no_anggota'] ?? '-'; ?>
        </small>
    </div>

    <a href="../logout.php" class="btn-logout">Logout</a>
</div>

<!-- MENU -->
<div class="menu">
    <a href="index.php" class="menu-item">Dashboard</a>
    <a href="pinjam_buku.php" class="menu-item">Pinjam Buku</a>
    <a href="riwayat.php" class="menu-item">Riwayat</a>
    <a href="transaksi_saya.php" class="menu-item active">Transaksi</a>
</div>

<!-- STATISTIK -->
<div class="stats">
    <div class="stat-card">
        <h3>Total Transaksi</h3>
        <div class="stat-number"><?= $total_transaksi; ?></div>
    </div>

    <div class="stat-card">
        <h3>Sedang Dipinjam</h3>
        <div class="stat-number"><?= $total_dipinjam; ?></div>
    </div>

    <div class="stat-card">
        <h3>Selesai</h3>
        <div class="stat-number"><?= $total_dikembalikan; ?></div>
    </div>
</div>

<!-- TABEL -->
<?php if(mysqli_num_rows($transaksi) > 0): ?>

<table class="data-table">

<thead>
<tr>
<th>Cover</th>
<th>Kode</th>
<th>Judul</th>
<th>Penulis</th>
<th>Tgl Pinjam</th>
<th>Batas</th>
<th>Kembali</th>
<th>Status</th>
<th>Denda</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($transaksi)): ?>

<?php
$is_late = (
$row['status'] == 'dipinjam' &&
strtotime($row['tanggal_kembali']) < time()
);
?>

<tr>

<td>
<?php if(!empty($row['cover'])): ?>
<img src="../uploads/<?= $row['cover']; ?>" class="cover">
<?php else: ?>
<div class="no-cover">No Cover</div>
<?php endif; ?>
</td>

<td><strong><?= $row['kode_transaksi']; ?></strong></td>

<td><?= htmlspecialchars($row['judul']); ?></td>

<td><?= htmlspecialchars($row['penulis']); ?></td>

<td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>

<td><?= date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>

<td>
<?= $row['tanggal_kembali']
? date('d/m/Y', strtotime($row['tanggal_kembali']))
: '-'; ?>
</td>

<td>
<?php if($row['status'] == 'dipinjam'): ?>

    <?php if($is_late): ?>
        Terlambat
    <?php else: ?>
        Dipinjam
    <?php endif; ?>

<?php else: ?>
    ✅ Dikembalikan
<?php endif; ?>
</td>

<td>
<?= $row['denda'] > 0
? 'Rp '.number_format($row['denda'],0,',','.')
: '-'; ?>
</td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

<?php else: ?>

<div class="empty-state">
Belum ada transaksi peminjaman
<br><br>
<a href="pinjam_buku.php">Pinjam Buku Sekarang</a>
</div>

<?php endif; ?>

</div>
</body>
</html>