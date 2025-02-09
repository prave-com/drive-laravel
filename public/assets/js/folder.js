document.addEventListener("DOMContentLoaded", function () {
    // Menangkap semua elemen dengan class 'folder-item'
    const folderItems = document.querySelectorAll(".folder-item");

    folderItems.forEach((item) => {
        // Cek double-click pada setiap folder-item
        item.addEventListener("dblclick", function (event) {
            // Mengecek apakah klik terjadi pada bagian dropdown atau tombol aksi
            const dropdownMenu = item.querySelector(".dropdown-menu");
            const actionMenu = item.querySelector(".action-menu");

            // Jika yang diklik adalah tombol aksi atau menu dropdown, hentikan aksi
            if (
                dropdownMenu.contains(event.target) ||
                actionMenu.contains(event.target)
            ) {
                event.stopPropagation();
                return;
            }

            // Jika tidak, buka folder
            const folderUrl = item.getAttribute("data-url");
            window.location.href = folderUrl;
        });
    });
});
