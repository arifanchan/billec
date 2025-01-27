<?php
include_once '../../config/database.php';
include_once '../../controllers/PelangganController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new PelangganController($db);

$pelanggan = json_decode($controller->getAll());
?>

<h2>Data Pelanggan</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Nama Pelanggan</th>
        <th>Alamat</th>
    </tr>
    <?php foreach ($pelanggan as $p) : ?>
        <tr>
            <td><?= $p->id_pelanggan ?></td>
            <td><?= $p->username ?></td>
            <td><?= $p->nama_pelanggan ?></td>
            <td><?= $p->alamat ?></td>
        </tr>
    <?php endforeach; ?>
</table>
