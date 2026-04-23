<?php
require_once '../config/database.php';

/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

$total_anggota = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='anggota'")
)['total'];

$total_buku_dipinjam = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE status='dipinjam'")
)['total'];

$total_denda = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COALESCE(SUM(denda),0) AS total FROM transaksi")
)['total'];

/*
|--------------------------------------------------------------------------
| DATA LAPORAN
|--------------------------------------------------------------------------
| Error sebelumnya karena tabel buku tidak punya kolom id_buku.
| Biasanya primary key tabel buku adalah id
*/

$laporan = mysqli_query($conn, "
SELECT 
    t.id,
    t.kode_transaksi,
    t.tanggal_pinjam,
    t.tanggal_kembali,
    t.batas_kembali,
    t.status,
    t.denda,
    u.nama_lengkap,
    b.judul
FROM transaksi t
JOIN users u ON t.user_id = u.id
JOIN buku b ON t.buku_id = b.id
ORDER BY t.tanggal_pinjam DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Perpustakaan</title>

<style>
body{
    font-family: Arial, sans-serif;
    margin:0;
    padding:20px;
    background:#f4f6f9;
}

h1{
    color:#333;
}

.top-btn{
    margin-bottom:20px;
}

.top-btn a,
.top-btn button{
    text-decoration:none;
    border:none;
    padding:10px 15px;
    border-radius:6px;
    color:white;
    cursor:pointer;
    font-size:14px;
}

.back-btn{ background:#3498db; }
.print-btn{ background:#27ae60; }

.stat-container{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:25px;
}

.stat-box{
    flex:1;
    min-width:220px;
    background:#2c3e50;
    color:white;
    padding:20px;
    border-radius:10px;
    text-align:center;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th, td{
    padding:10px;
    border:1px solid #ddd;
}

th{
    background:#34495e;
    color:white;
}

tr:nth-child(even){
    background:#f9f9f9;
}

.status-pinjam{
    color:#e67e22;
    font-weight:bold;
}

.status-kembali{
    color:#27ae60;
    font-weight:bold;
}

@media print{
    .top-btn{ display:none; }
    body{
        background:white;
        padding:0;
    }
}
</style>
</head>

<body>

<h1>Laporan Perpustakaan</h1>

<div class="top-btn">
    <a href="index.php" class="back-btn">← Dashboard</a>
    <button onclick="window.print()" class="print-btn">Cetak</button>
</div>

<div class="stat-container">
    <div class="stat-box">
        <h3>Total Anggota</h3>
        <h2><?= $total_anggota; ?></h2>
    </div>

    <div class="stat-box">
        <h3>Buku Dipinjam</h3>
        <h2><?= $total_buku_dipinjam; ?></h2>
    </div>

    <div class="stat-box">
        <h3>Total Denda</h3>
        <h2>Rp <?= number_format($total_denda,0,',','.'); ?></h2>
    </div>
</div>

<h3>Detail Transaksi</h3>

<table>
<tr>
    <th>No</th>
    <th>Kode</th>
    <th>Nama Anggota</th>
    <th>Judul Buku</th>
    <th>Tanggal Pinjam</th>
    <th>Tanggal Kembali</th>
    <th>Status</th>
    <th>Denda</th>
</tr>

<?php $no=1; while($row = mysqli_fetch_assoc($laporan)) : ?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= $row['kode_transaksi']; ?></td>
    <td><?= $row['nama_lengkap']; ?></td>
    <td><?= $row['judul']; ?></td>
    <td><?= $row['tanggal_pinjam']; ?></td>
    <td><?= $row['tanggal_kembali'] ?: '-'; ?></td>
    <td>
        <?php if($row['status']=='dipinjam'): ?>
            <span class="status-pinjam">Dipinjam</span>
        <?php else: ?>
            <span class="status-kembali">Dikembalikan</span>
        <?php endif; ?>
    </td>
    <td>Rp <?= number_format($row['denda'],0,',','.'); ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>