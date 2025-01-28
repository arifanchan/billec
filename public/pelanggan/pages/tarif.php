<h2>Daftar Tarif Listrik</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Daya (VA)</th>
            <th>Tarif per kWh (Rp)</th>
        </tr>
    </thead>
    <tbody id="tarif-table-body">
        <!-- Data tarif akan dimuat dengan JavaScript -->
    </tbody>
</table>

<script>
document.addEventListener("DOMContentLoaded", async function () {
    try {
        const response = await fetch("../../api/tarifAPI.php", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });

        if (!response.ok) {
            throw new Error("Gagal memuat data tarif");
        }

        const tarif = await response.json();
        const tableBody = document.getElementById("tarif-table-body");

        tarif.forEach(t => {
            const row = `
                <tr>
                    <td>${t.daya}</td>
                    <td>${t.tarifperkwh}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    } catch (error) {
        alert(error.message);
    }
});
</script>
