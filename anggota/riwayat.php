<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'anggota') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ======================================================
   FILTER STATUS
====================================================== */
$status_filter = isset($_GET['status'])
    ? mysqli_real_escape_string($conn, $_GET['status'])
    : 'semua';

/* ======================================================
   QUERY RIWAYAT TERHUBUNG DENGAN index.php
   memakai tabel transaksi
====================================================== */
$query = "SELECT t.*, b.judul, b.penulis, b.penerbit, b.cover
          FROM transaksi t
          JOIN buku b ON t.buku_id = b.id
          WHERE t.user_id = '$user_id'";

if ($status_filter == 'dipinjam') {
    $query .= " AND t.status='dipinjam'";
} elseif ($status_filter == 'dikembalikan') {
    $query .= " AND t.status='dikembalikan'";
}

$query .= " ORDER BY t.created_at DESC";

$riwayat = mysqli_query($conn, $query);

/* ======================================================
   STATISTIK
====================================================== */
$total_semua = mysqli_fetch_assoc(mysqli_query($conn,
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
<title>Riwayat Peminjaman</title>
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
        <h1>Riwayat Peminjaman</h1>
        <p>Selamat datang,
            <strong><?= $_SESSION['nama_lengkap']; ?></strong>
        </p>
        <small>No Anggota:
            <?= $_SESSION['no_anggota'] ?? '-'; ?>
        </small>
    </div>

    <a href="../logout.php" class="btn-logout">Logout</a>
</div>

<!-- MENU SAMA SEPERTI index.php -->
<div class="menu">
    <a href="index.php" class="menu-item">🏠 Dashboard</a>
    <a href="pinjam_buku.php" class="menu-item">📖 Pinjam Buku</a>
    <a href="riwayat.php" class="menu-item active">📋 Riwayat</a>
</div>

<!-- STATISTIK -->
<div class="stats">
    <div class="stat-card">
        <h4>Total</h4>
        <div class="stat-number"><?= $total_semua; ?></div>
    </div>

    <div class="stat-card">
        <h4>Dipinjam</h4>
        <div class="stat-number"><?= $total_dipinjam; ?></div>
    </div>

    <div class="stat-card">
        <h4>Selesai</h4>
        <div class="stat-number"><?= $total_dikembalikan; ?></div>
    </div>
</div>

<!-- FILTER -->
<div class="section">
<h2>Filter Status</h2>

<a href="?status=semua" class="btn <?= $status_filter=='semua'?'active':''; ?>">Semua</a>

<a href="?status=dipinjam" class="btn <?= $status_filter=='dipinjam'?'active':''; ?>">Dipinjam</a>

<a href="?status=dikembalikan" class="btn <?= $status_filter=='dikembalikan'?'active':''; ?>">Dikembalikan</a>

</div>

<!-- TABEL -->
<div class="section">

<?php if(mysqli_num_rows($riwayat) > 0): ?>

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

<?php while($row = mysqli_fetch_assoc($riwayat)): ?>

<?php
$is_late = (
$row['status']=='dipinjam' &&
strtotime($row['tanggal_kembali_rencana']) < time()
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

<td><?= $row['kode_transaksi']; ?></td>

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
<?php if($row['status']=='dikembalikan'): ?>
✅ Dikembalikan
<?php elseif($is_late): ?>
⚠️ Terlambat
<?php else: ?>
📖 Dipinjam
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
Belum ada riwayat peminjaman
<br><br>
<a href="pinjam_buku.php">Pinjam Buku Sekarang</a>
</div>

<?php endif; ?>

</div>

</div>
</body>
</html>