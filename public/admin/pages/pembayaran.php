<?php
include_once '../../config/database.php';
include_once '../../controllers/PembayaranController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new PembayaranController($db);

$pembayaran = json_decode($controller->getAll());
?>

<h2>Data Pembayaran</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID Pembayaran</th>
        <th>ID Tagihan</th>
        <th>ID Pelanggan</th>
        <th>Tanggal Pembayaran</th>
        <th>Total Bayar</th>
    </tr>
    <?php foreach ($pembayaran as $p) : ?>
        <tr>
            <td><?= $p->id_pembayaran ?></td>
            <td><?= $p->id_tagihan ?></td>
            <td><?= $p->id_pelanggan ?></td>
            <td><?= $p->tanggal_pembayaran ?></td>
            <td><?= $p->total_bayar ?></td>
        </tr>
    <?php endforeach; ?>
</table>
