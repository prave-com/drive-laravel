document.addEventListener("DOMContentLoaded", function () {
    // Ambil elemen input radio dan custom_quota
    const requestQuotaRadios = document.querySelectorAll(
        "input[name='request_quota']",
    );
    const customQuota = document.getElementById("custom_quota");

    // Tambahkan event listener ke setiap radio button
    requestQuotaRadios.forEach((radio) => {
        radio.dataset.selected = "false"; // Tambahkan atribut untuk melacak status pilihan

        radio.addEventListener("click", function () {
            if (radio.dataset.selected === "true") {
                // Jika tombol radio diklik kembali saat sudah aktif, batalkan pilihan
                radio.checked = false;
                radio.dataset.selected = "false"; // Ubah status menjadi tidak terpilih

                // Hapus kelas aktif dari label
                const label = document.querySelector(
                    `label[for="${radio.id}"]`,
                );
                if (label) label.classList.remove("active");

                // Aktifkan kembali custom_quota
                customQuota.disabled = false;
                return;
            }

            // Jika tombol radio dipilih pertama kali
            requestQuotaRadios.forEach((btn) => {
                btn.dataset.selected = "false"; // Reset status semua radio button lainnya
                const label = document.querySelector(`label[for="${btn.id}"]`);
                if (label) label.classList.remove("active");
            });

            radio.dataset.selected = "true"; // Tandai tombol sebagai dipilih
            customQuota.disabled = true; // Nonaktifkan custom_quota
            customQuota.value = ""; // Kosongkan nilai custom_quota

            // Tambahkan kelas aktif ke label tombol yang dipilih
            const selectedLabel = document.querySelector(
                `label[for="${radio.id}"]`,
            );
            if (selectedLabel) selectedLabel.classList.add("active");
        });
    });

    // Event listener untuk input custom_quota
    customQuota?.addEventListener("input", function () {
        // Pastikan hanya angka yang bisa dimasukkan dan nilai tidak melebihi 999
        customQuota.value = customQuota.value.replace(/[^0-9]/g, "");
        if (customQuota.value > 999) {
            customQuota.value = 999;
        }

        // Nonaktifkan semua radio button jika custom_quota diisi
        if (customQuota.value !== "") {
            requestQuotaRadios.forEach((radio) => {
                radio.checked = false; // Uncheck semua radio button
                radio.dataset.selected = "false"; // Reset status pilihan
                const label = document.querySelector(
                    `label[for="${radio.id}"]`,
                );
                if (label) label.classList.remove("active");
            });
        }
    });
});
