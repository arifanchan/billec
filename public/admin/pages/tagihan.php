<?php
include_once '../../config/database.php';
include_once '../../controllers/TagihanController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new TagihanController($db);

$tagihan = json_decode($controller->getAll());
?>

<h2>Data Tagihan</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID Tagihan</th>
        <th>ID Pelanggan</th>
        <th>Bulan</th>
        <th>Tahun</th>
        <th>Jumlah Meter</th>
        <th>Status</th>
    </tr>
    <?php foreach ($tagihan as $t) : ?>
        <tr>
            <td><?= $t->id_tagihan ?></td>
            <td><?= $t->id_pelanggan ?></td>
            <td><?= $t->bulan ?></td>
            <td><?= $t->tahun ?></td>
            <td><?= $t->jumlah_meter ?></td>
            <td><?= $t->status ?></td>
        </tr>
    <?php endforeach; ?>
</table>
