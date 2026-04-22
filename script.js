document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("#sewa form");
  const dataTableBody = document.querySelector("#data tbody");
  const tanggalSewa = document.getElementById("tanggal_sewa");
  const tanggalKembali = document.getElementById("tanggal_kembali");
  const alatSelect = document.getElementById("alat");
  const jumlahInput = document.getElementById("jumlah");

  const hargaAlat = {
    "Tenda 4 Orang": 50000,
    "Sleeping Bag": 20000,
    "Carrier 60L": 35000,
    "Kompor Portable": 25000
  };

  // Buat elemen hasil total otomatis
  const hasilBox = document.createElement("div");
  hasilBox.id = "hasil-total";
  hasilBox.style.marginTop = "20px";
  hasilBox.style.padding = "15px";
  hasilBox.style.borderRadius = "10px";
  hasilBox.style.backgroundColor = "#f4f8f2";
  hasilBox.style.border = "1px solid #cfd8c8";
  hasilBox.innerHTML = "<strong>Informasi Penyewaan:</strong><br>Silakan isi form untuk melihat ringkasan.";
  form.appendChild(hasilBox);

  // Format Rupiah
  function formatRupiah(angka) {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0
    }).format(angka);
  }

  // Hitung selisih hari
  function hitungLamaSewa(mulai, selesai) {
    const tglMulai = new Date(mulai);
    const tglSelesai = new Date(selesai);
    const selisih = tglSelesai - tglMulai;
    const hari = Math.ceil(selisih / (1000 * 60 * 60 * 24));
    return hari;
  }

  // Update minimal tanggal kembali
  tanggalSewa.addEventListener("change", function () {
    tanggalKembali.min = this.value;
  });

  // Preview ringkasan saat input berubah
  function updateRingkasan() {
    const alat = alatSelect.value;
    const jumlah = parseInt(jumlahInput.value) || 0;
    const mulai = tanggalSewa.value;
    const selesai = tanggalKembali.value;

    if (alat && jumlah > 0 && mulai && selesai) {
      const lama = hitungLamaSewa(mulai, selesai);
      if (lama > 0) {
        const total = hargaAlat[alat] * jumlah * lama;
        hasilBox.innerHTML = `
          <strong>Informasi Penyewaan:</strong><br>
          Alat: <b>${alat}</b><br>
          Harga per hari: <b>${formatRupiah(hargaAlat[alat])}</b><br>
          Jumlah: <b>${jumlah}</b><br>
          Lama sewa: <b>${lama} hari</b><br>
          Total biaya: <b>${formatRupiah(total)}</b>
        `;
      }
    }
  }

  alatSelect.addEventListener("change", updateRingkasan);
  jumlahInput.addEventListener("input", updateRingkasan);
  tanggalSewa.addEventListener("change", updateRingkasan);
  tanggalKembali.addEventListener("change", updateRingkasan);

  // Submit form
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const nama = document.getElementById("nama").value.trim();
    const alat = alatSelect.value;
    const jumlah = parseInt(jumlahInput.value);
    const mulai = tanggalSewa.value;
    const selesai = tanggalKembali.value;

    if (!nama || !alat || !jumlah || !mulai || !selesai) {
      alert("Semua data harus diisi.");
      return;
    }

    const lamaSewa = hitungLamaSewa(mulai, selesai);

    if (lamaSewa <= 0) {
      alert("Tanggal kembali harus setelah tanggal sewa.");
      return;
    }

    const totalHarga = hargaAlat[alat] * jumlah * lamaSewa;

    const jumlahBaris = dataTableBody.rows.length + 1;
    const idSewa = "SW" + String(jumlahBaris).padStart(3, "0");

    const barisBaru = document.createElement("tr");
    barisBaru.innerHTML = `
      <td>${idSewa}</td>
      <td>${nama}</td>
      <td>${alat}</td>
      <td>${jumlah}</td>
      <td>${mulai}</td>
      <td>${selesai}</td>
      <td>Dipinjam</td>
    `;

    dataTableBody.appendChild(barisBaru);

    alert(
      "Data penyewaan berhasil disimpan!\n\n" +
      "ID Sewa: " + idSewa + "\n" +
      "Nama: " + nama + "\n" +
      "Alat: " + alat + "\n" +
      "Lama Sewa: " + lamaSewa + " hari\n" +
      "Total: " + formatRupiah(totalHarga)
    );

    form.reset();
    hasilBox.innerHTML = "<strong>Informasi Penyewaan:</strong><br>Silakan isi form untuk melihat ringkasan.";
    tanggalKembali.min = "";

    document.getElementById("data").scrollIntoView({
      behavior: "smooth"
    });
  });

  // Smooth scroll untuk menu navigasi
  const navLinks = document.querySelectorAll('nav a[href^="#"]');
  navLinks.forEach(link => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({
          behavior: "smooth"
        });
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const modalHapus = document.getElementById("modalHapus");
  const btnBatal = document.getElementById("btnBatal");
  const btnKonfirmasiHapus = document.getElementById("btnKonfirmasiHapus");
  const semuaBtnHapus = document.querySelectorAll(".btn-hapus");

  semuaBtnHapus.forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const linkHapus = this.getAttribute("href");
      btnKonfirmasiHapus.setAttribute("href", linkHapus);
      modalHapus.classList.add("show");
    });
  });

  btnBatal.addEventListener("click", function () {
    modalHapus.classList.remove("show");
  });

  modalHapus.addEventListener("click", function (e) {
    if (e.target === modalHapus) {
      modalHapus.classList.remove("show");
    }
  });
});

