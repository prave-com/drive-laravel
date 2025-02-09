document.addEventListener("DOMContentLoaded", function () {
    // MODAL SHARE
    // Tambahkan event listener untuk membuka modal dan memperbarui nama file
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach((button) => {
        button.addEventListener("click", function (event) {
            // Ambil nama file dari data attribute
            var fileName = event.currentTarget.getAttribute("data-file-name");

            // Update nama file di dalam modal
            var fileNameElement = document.getElementById("fileNameModal");
            if (fileNameElement) {
                fileNameElement.innerText = fileName;
            }
        });
    });

    // INFORMASI FILE
    // Menambahkan event listener ke semua elemen yang memicu offcanvas
    document
        .querySelectorAll('[data-bs-toggle="offcanvas"]')
        .forEach((item) => {
            item.addEventListener("click", function (event) {
                // Ambil data file dari atribut data
                const fileName = this.getAttribute("data-file-name");
                const fileType = this.getAttribute("data-file-type");
                const fileSize = this.getAttribute("data-file-size");
                const fileLocation = this.getAttribute("data-file-location");
                const fileOwner = this.getAttribute("data-file-owner");
                const fileDate = this.getAttribute("data-file-date");

                // Debugging: Periksa apakah data sudah benar
                console.log(
                    fileName,
                    fileType,
                    fileSize,
                    fileLocation,
                    fileOwner,
                    fileDate,
                );

                // Perbarui konten offcanvas
                document.getElementById("fileName").textContent =
                    fileName || "Tidak tersedia";
                document.getElementById("fileType").textContent =
                    fileType || "Tidak tersedia";
                document.getElementById("fileSize").textContent =
                    fileSize || "Tidak tersedia";
                document.getElementById("fileLocation").textContent =
                    fileLocation || "Tidak tersedia";
                document.getElementById("fileOwner").textContent =
                    fileOwner || "Tidak tersedia";
                document.getElementById("fileDate").textContent =
                    fileDate || "Tidak tersedia";
            });
        });
});
