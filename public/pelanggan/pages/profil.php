<?php
// Profil pelanggan ditampilkan dari data sesi
$nama_pelanggan = htmlspecialchars($_SESSION['user']['nama_pelanggan'], ENT_QUOTES, 'UTF-8');
$alamat = htmlspecialchars($_SESSION['user']['alamat'], ENT_QUOTES, 'UTF-8');
?>

<h2>Profil Pelanggan</h2>
<p><strong>Nama Pelanggan:</strong> <?= $nama_pelanggan ?></p>
<p><strong>Alamat:</strong> <?= $alamat ?></p>
