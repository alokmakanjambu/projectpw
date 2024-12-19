// File ini untuk fungsi JavaScript di masa depan
document.addEventListener("DOMContentLoaded", function () {
  // Kode JavaScript akan ditambahkan di sini
});

//kendaraan_keluar.php

// Script untuk sidebar
let sidebar = document.querySelector(".sidebar");
let sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function () {
  sidebar.classList.toggle("active");
};

// Script untuk modal
document.addEventListener("DOMContentLoaded", function () {
  var modal = document.getElementById("prosesKeluarModal");
  modal.addEventListener("show.bs.modal", function (event) {
    var button = event.relatedTarget;
    var id = button.getAttribute("data-id");
    var plat = button.getAttribute("data-plat");
    var jenisKendaraan = button.getAttribute("data-jenis");
    var waktuMasuk = button.getAttribute("data-waktu-masuk");

    // Set nilai ke form
    document.getElementById("id_parkir").value = id;
    document.getElementById("modal_plat_nomor").value = plat;

    // Format waktu masuk
    var waktuMasukDate = new Date(waktuMasuk);
    var waktuMasukFormatted = waktuMasukDate.toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      hour12: false,
    });
    document.getElementById("modal_waktu_masuk").value = waktuMasukFormatted;

    // Format waktu keluar (waktu sekarang)
    var waktuKeluar = new Date();
    var waktuKeluarFormatted = waktuKeluar.toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      hour12: false,
    });
    document.getElementById("modal_waktu_keluar").value = waktuKeluarFormatted;

    // Hitung durasi dan biaya
    var durasi = Math.ceil((waktuKeluar - waktuMasukDate) / (1000 * 60 * 60)); // Durasi dalam jam
    document.getElementById("modal_durasi").value = durasi + " jam";

    // Hitung biaya
    var tarifPerJam = jenisKendaraan === "Motor" ? 2000 : 4000;
    var biaya = durasi * tarifPerJam;
    document.getElementById("modal_biaya").value = biaya;

    // Hitung kembalian otomatis
    var inputJumlahBayar = document.getElementById("jumlah_bayar");
    var spanKembalian = document.getElementById("kembalian");

    inputJumlahBayar.addEventListener("input", function () {
      var jumlahBayar = parseInt(this.value) || 0;
      var kembalian = jumlahBayar - biaya;
      spanKembalian.textContent =
        kembalian >= 0
          ? "Rp " + kembalian.toLocaleString("id-ID")
          : "Pembayaran kurang";
      spanKembalian.className = kembalian >= 0 ? "text-success" : "text-danger";
    });
  });
});

//cetak_struk

function cetakStruk() {
  // Ambil data dari form
  const plat = document.getElementById("modal_plat_nomor").value;
  const waktuMasuk = document.getElementById("modal_waktu_masuk").value;
  const waktuKeluar = document.getElementById("modal_waktu_keluar").value;
  const durasi = document.getElementById("modal_durasi").value;
  const biaya = document.getElementById("modal_biaya").value;
  const bayar = document.getElementById("jumlah_bayar").value;
  const kembalian = document.getElementById("kembalian").textContent;

  // Isi template struk
  document.getElementById("struk_plat").textContent = plat;
  document.getElementById("struk_masuk").textContent = waktuMasuk;
  document.getElementById("struk_keluar").textContent = waktuKeluar;
  document.getElementById("struk_durasi").textContent = durasi;
  document.getElementById("struk_biaya").textContent =
    "Rp " + parseInt(biaya).toLocaleString("id-ID");
  document.getElementById("struk_bayar").textContent =
    "Rp " + parseInt(bayar).toLocaleString("id-ID");
  document.getElementById("struk_kembalian").textContent = kembalian;

  // Buka window baru untuk cetak
  const strukWindow = window.open("", "_blank", "width=300,height=400");
  strukWindow.document.write("<html><head><title>Struk Parkir</title>");
  strukWindow.document.write("<style>");
  strukWindow.document.write(`
        body { font-family: monospace; padding: 20px; }
        .struk { width: 280px; }
        .text-center { text-align: center; }
        hr { border-top: 1px dashed #000; }
    `);
  strukWindow.document.write("</style></head><body>");
  strukWindow.document.write(
    document.getElementById("strukTemplate").innerHTML
  );
  strukWindow.document.write("</body></html>");

  // Cetak dan tutup window
  setTimeout(() => {
    strukWindow.print();
    strukWindow.close();
  }, 500);
}

document.addEventListener("DOMContentLoaded", function () {
  // ... kode modal yang sudah ada ...

  // Tambahkan fungsi cetak struk
  window.cetakStruk = function () {
    // Ambil data dari form
    const plat = document.getElementById("modal_plat_nomor").value;
    const waktuMasuk = document.getElementById("modal_waktu_masuk").value;
    const waktuKeluar = document.getElementById("modal_waktu_keluar").value;
    const durasi = document.getElementById("modal_durasi").value;
    const biaya = document.getElementById("modal_biaya").value;
    const bayar = document.getElementById("jumlah_bayar").value;
    const kembalian = document.getElementById("kembalian").textContent;

    // Buat konten struk
    const strukContent = `
            <div style="font-family: monospace; width: 300px; padding: 20px;">
                <h3 style="text-align: center;">ParkEase</h3>
                <p style="text-align: center;">Struk Parkir</p>
                <hr style="border-top: 1px dashed #000;">
                <p>Plat Nomor: ${plat}</p>
                <p>Waktu Masuk: ${waktuMasuk}</p>
                <p>Waktu Keluar: ${waktuKeluar}</p>
                <p>Durasi: ${durasi}</p>
                <hr style="border-top: 1px dashed #000;">
                <p>Total Biaya: Rp ${parseInt(biaya).toLocaleString(
                  "id-ID"
                )}</p>
                <p>Bayar: Rp ${parseInt(bayar).toLocaleString("id-ID")}</p>
                <p>Kembalian: ${kembalian}</p>
                <hr style="border-top: 1px dashed #000;">
                <p style="text-align: center;">Terima Kasih</p>
            </div>
        `;

    // Buka window baru untuk cetak
    const printWindow = window.open("", "_blank", "width=400,height=600");
    printWindow.document.write(
      "<html><head><title>Struk Parkir</title></head><body>"
    );
    printWindow.document.write(strukContent);
    printWindow.document.write("</body></html>");

    // Cetak
    setTimeout(() => {
      printWindow.print();
      printWindow.close();
    }, 500);
  };
});
