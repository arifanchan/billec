<h2>Penggunaan Listrik Saya</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Meter Awal</th>
            <th>Meter Akhir</th>
            <th>Total Penggunaan (kWh)</th>
        </tr>
    </thead>
    <tbody id="penggunaan-table-body">
        <!-- Data akan dimuat dengan JavaScript -->
    </tbody>
</table>

<script>
document.addEventListener("DOMContentLoaded", async function () {
    try {
        // Kirim permintaan GET ke API penggunaan
        const response = await fetch("../../api/penggunaanAPI.php", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });

        if (!response.ok) {
            throw new Error("Gagal memuat data penggunaan");
        }

        // Parsing data dari API
        const penggunaan = await response.json();
        const tableBody = document.getElementById("penggunaan-table-body");

        // Tambahkan baris data ke tabel
        penggunaan.forEach(p => {
            const row = `
                <tr>
                    <td>${p.bulan}</td>
                    <td>${p.tahun}</td>
                    <td>${p.meter_awal}</td>
                    <td>${p.meter_akhir}</td>
                    <td>${p.total_penggunaan}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    } catch (error) {
        alert(error.message); // Tampilkan pesan kesalahan jika gagal
    }
});
</script>
