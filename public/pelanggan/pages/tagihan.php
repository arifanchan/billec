<h2>Tagihan Saya</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>ID Tagihan</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Jumlah Meter</th>
            <th>Total Tagihan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody id="tagihan-table-body">
        <!-- Data akan dimuat dengan JavaScript -->
    </tbody>
</table>

<script>
document.addEventListener("DOMContentLoaded", async function () {
    try {
        const response = await fetch("../../api/tagihanAPI.php", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });

        if (!response.ok) {
            throw new Error("Gagal memuat data tagihan");
        }

        const tagihan = await response.json();
        const tableBody = document.getElementById("tagihan-table-body");

        tagihan.forEach(t => {
            const row = `
                <tr>
                    <td>${t.id_tagihan}</td>
                    <td>${t.bulan}</td>
                    <td>${t.tahun}</td>
                    <td>${t.jumlah_meter}</td>
                    <td>${t.total_tagihan}</td>
                    <td>${t.status}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    } catch (error) {
        alert(error.message);
    }
});
</script>
