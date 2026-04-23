<?php
include 'koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anggota WHERE id_anggota = $id"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    $email = $_POST['email'];
    
    $query = "UPDATE anggota SET nama_anggota='$nama', alamat='$alamat', no_telepon='$telepon', email='$email' WHERE id_anggota=$id";
    mysqli_query($conn, $query);
    echo "<script>alert('Data berhasil diupdate!'); window.location='anggota.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Anggota</title>
</head>
<body>
    <h1>Edit Anggota</h1>
    <form method="POST">
        <label>Nama:</label><br>
        <input type="text" name="nama" value="<?= $data['nama_anggota'] ?>" required><br><br>
        
        <label>Alamat:</label><br>
        <textarea name="alamat"><?= $data['alamat'] ?></textarea><br><br>
        
        <label>Telepon:</label><br>
        <input type="text" name="telepon" value="<?= $data['no_telepon'] ?>"><br><br>
        
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= $data['email'] ?>"><br><br>
        
        <button type="submit">Update</button>
        <a href="anggota.php">Batal</a>
    </form>
</body>
</html>