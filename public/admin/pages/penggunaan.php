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
        <th>ID Pelanggan</th>
        <th>Nama Pelanggan</th>
        <th>Bulan</th>
        <th>Tahun</th>
        <th>Meter Awal</th>
        <th>Meter Akhir</th>
        <th>Total Pemakaian</th>
    </tr>
    <?php foreach ($penggunaan as $p) : ?>
        <tr>
            <td><?= $p->id_pelanggan ?></td>
            <td><?= $p->nama_pelanggan ?></td>
            <td><?= $p->bulan ?></td>
            <td><?= $p->tahun ?></td>
            <td><?= $p->meter_awal ?></td>
            <td><?= $p->meter_akhir ?></td>
            <td><?= $p->total_penggunaan ?></td>
        </tr>
    <?php endforeach; ?>
</table>
