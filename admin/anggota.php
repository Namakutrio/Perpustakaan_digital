<?php
require_once '../config/database.php';

// Proses hapus anggota
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM anggota WHERE id_anggota = $id");

    echo "<script>
        alert('Anggota berhasil dihapus!');
        window.location='anggota.php';
    </script>";
    exit;
}

// Ambil data anggota
$query = mysqli_query($conn, "SELECT * FROM anggota ORDER BY tanggal_daftar DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Anggota</title>

<style>
body{
    font-family:Arial, sans-serif;
    background:#f4f6f9;
    margin:0;
    padding:20px;
}

h1{
    color:#333;
}

a{
    text-decoration:none;
}

.tambah,.kembali{
    padding:10px 15px;
    border-radius:5px;
    color:white;
    display:inline-block;
    margin-bottom:15px;
}

.tambah{background:green;}
.kembali{background:#3498db;}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th,td{
    padding:10px;
    border:1px solid #ddd;
}

th{
    background:#2c3e50;
    color:white;
}

.btn-edit{
    background:orange;
    color:white;
    padding:5px 10px;
    border-radius:4px;
}

.btn-hapus{
    background:red;
    color:white;
    padding:5px 10px;
    border-radius:4px;
}
</style>
</head>

<body>

<h1>Kelola Anggota</h1>

<a href="tambah_anggota.php" class="tambah">+ Tambah Anggota</a>
<a href="index.php" class="kembali">← Dashboard</a>

<table>
<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Alamat</th>
    <th>Telepon</th>
    <th>Email</th>
    <th>Tanggal Daftar</th>
    <th>Aksi</th>
</tr>

<?php
$no = 1;
while($row = mysqli_fetch_assoc($query)) :
?>

<tr>
    <td><?= $no++; ?></td>
    <td><?= htmlspecialchars($row['nama_anggota'] ?? '-'); ?></td>
    <td><?= htmlspecialchars($row['alamat'] ?? '-'); ?></td>
    <td><?= htmlspecialchars($row['no_telepon'] ?? '-'); ?></td>
    <td><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
    <td><?= htmlspecialchars($row['tanggal_daftar'] ?? '-'); ?></td>
    <td>
        <a href="edit_anggota.php?id=<?= $row['id_anggota']; ?>" class="btn-edit">Edit</a>

        <a href="?hapus=<?= $row['id_anggota']; ?>"
           class="btn-hapus"
           onclick="return confirm('Yakin ingin hapus anggota ini?')">
           Hapus
        </a>
    </td>
</tr>

<?php endwhile; ?>

</table>

</body>
</html>