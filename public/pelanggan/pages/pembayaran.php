<h2>Pembayaran Saya</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>ID Pembayaran</th>
            <th>ID Tagihan</th>
            <th>Tanggal Pembayaran</th>
            <th>Total Bayar</th>
        </tr>
    </thead>
    <tbody id="pembayaran-table-body">
        <!-- Data akan dimuat dengan JavaScript -->
    </tbody>
</table>

<script>
document.addEventListener("DOMContentLoaded", async function () {
    try {
        const response = await fetch("../../api/pembayaranAPI.php", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });

        if (!response.ok) {
            throw new Error("Gagal memuat data pembayaran");
        }

        const pembayaran = await response.json();
        const tableBody = document.getElementById("pembayaran-table-body");

        pembayaran.forEach(p => {
            const row = `
                <tr>
                    <td>${p.id_pembayaran}</td>
                    <td>${p.id_tagihan}</td>
                    <td>${p.tanggal_pembayaran}</td>
                    <td>${p.total_bayar}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    } catch (error) {
        alert(error.message);
    }
});
</script>
