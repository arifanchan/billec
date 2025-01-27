<?php
include_once '../../config/database.php';
include_once '../../controllers/PenggunaanController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new PenggunaanController($db);

$penggunaan = json_decode($controller->getAll());
?>

<h2>Data Penggunaan Listrik</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID Penggunaan</th>
        <th>ID Pelanggan</th>
        <th>Bulan</th>
        <th>Tahun</th>
        <th>Meter Awal</th>
        <th>Meter Akhir</th>
    </tr>
    <?php foreach ($penggunaan as $p) : ?>
        <tr>
            <td><?= $p->id_penggunaan ?></td>
            <td><?= $p->id_pelanggan ?></td>
            <td><?= $p->bulan ?></td>
            <td><?= $p->tahun ?></td>
            <td><?= $p->meter_awal ?></td>
            <td><?= $p->meter_akhir ?></td>
        </tr>
    <?php endforeach; ?>
</table>
