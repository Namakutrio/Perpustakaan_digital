<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon  = mysqli_real_escape_string($conn, $_POST['telepon']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);

    $query = "INSERT INTO anggota (nama_anggota, alamat, no_telepon, email)
              VALUES ('$nama', '$alamat', '$telepon', '$email')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Anggota berhasil ditambahkan!');
                window.location='anggota.php';
              </script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Anggota</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Roboto',sans-serif;
    background:#ffffff;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:30px;
}

.container{
    width:100%;
    max-width:600px;
    background:#ffffff;
    padding:35px;
    border-radius:18px;
    box-shadow:0 15px 40px rgba(0,0,0,0.15);
}

h1{
    text-align:center;
    margin-bottom:30px;
    color:#1e293b;
}

.form-group{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
    color:#334155;
}

input, textarea{
    width:100%;
    padding:12px 15px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:15px;
    outline:none;
    transition:0.3s;
}

input:focus,
textarea:focus{
    border-color:#3b82f6;
    box-shadow:0 0 0 3px rgba(59,130,246,0.15);
}

textarea{
    resize:none;
    height:100px;
}

.button-group{
    display:flex;
    gap:10px;
    margin-top:25px;
}

button,
.btn-batal{
    flex:1;
    padding:12px;
    border:none;
    border-radius:10px;
    font-size:16px;
    cursor:pointer;
    text-decoration:none;
    text-align:center;
    transition:0.3s;
}

button{
    background:#22c55e;
    color:white;
}

button:hover{
    background:#16a34a;
}

.btn-batal{
    background:#ef4444;
    color:white;
}

.btn-batal:hover{
    background:#dc2626;
}

@media(max-width:600px){
    .button-group{
        flex-direction:column;
    }
}
</style>
</head>

<body>

<div class="container">

    <h1>Tambah Anggota Baru</h1>

    <form method="POST">

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required>
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat"></textarea>
        </div>

        <div class="form-group">
            <label>No. Telepon</label>
            <input type="text" name="telepon">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email">
        </div>

        <div class="button-group">
            <button type="submit">Simpan</button>
            <a href="anggota.php" class="btn-batal">Batal</a>
        </div>

    </form>

</div>

</body>
</html>