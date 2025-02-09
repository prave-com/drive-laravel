// TOGGLE SIDEBAR
document.addEventListener("DOMContentLoaded", function () {
    // Toggle Sidebar
    const menuBar = document.querySelector("#content .navbar .bx.bx-menu");
    const sidebar = document.getElementById("sidebar");

    if (menuBar && sidebar) {
        menuBar.addEventListener("click", () => {
            sidebar.classList.toggle("hide"); // Toggle 'hide' class to show/hide the sidebar
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // RESPONSIVE SEARCH TOGGLE
    const searchButton = document.querySelector(
        "#content .navbar form .input-group button",
    );
    const searchForm = document.querySelector("#content .navbar form");
    const searchIcon = searchButton.querySelector(".bx");

    // Toggle search form on small screen sizes
    searchButton.addEventListener("click", (e) => {
        if (window.innerWidth < 576) {
            e.preventDefault();
            searchForm.classList.toggle("show");
            searchIcon.classList.toggle(
                "bx-x",
                searchForm.classList.contains("show"),
            );
            searchIcon.classList.toggle(
                "bx-search",
                !searchForm.classList.contains("show"),
            );
        }
    });

    // Adjust sidebar and search form visibility on resize
    window.addEventListener("resize", () => {
        if (window.innerWidth > 576) {
            searchForm.classList.remove("show");
            searchIcon.classList.replace("bx-x", "bx-search");
        }
        if (window.innerWidth < 768) {
            sidebar.classList.add("hide");
        } else {
            sidebar.classList.remove("hide");
        }
    });
});

// UPLOAD FILE BERKAS
// Trigger file input when the upload area is clicked
function triggerFileInput() {
    document.getElementById("fileInput").click();
}

// Auto-submit the form when files are selected
document.addEventListener("DOMContentLoaded", function () {
    // Listen for the file input change event and automatically submit the form
    document
        .getElementById("fileInput")
        .addEventListener("change", function () {
            if (this.files.length > 0) {
                // Submit the form when files are selected
                document.getElementById("uploadForm").submit();

                // After submitting the form, show SweetAlert for success
                Swal.fire({
                    position: "center", // Posisi di tengah (default)
                    icon: "success",
                    title: "File berhasil diupload!",
                    showConfirmButton: false,
                    timer: 1500,
                });
            }
        });
});

//SWEETALERT DELETE PERMENTLY
document.addEventListener("DOMContentLoaded", function () {
    // Tambahkan event listener pada semua tombol dengan class deleteButton
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("deleteButton")) {
            // Cegah form agar tidak langsung dikirim
            e.preventDefault();

            // Tampilkan dialog konfirmasi SweetAlert
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Tindakan ini tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika dikonfirmasi, kirim form terkait tombol yang ditekan
                    e.target.closest(".deleteForm").submit();
                }
            });
        }
    });
});

//SWEETALERT PULIHKAN FOLDER
document.addEventListener("DOMContentLoaded", function () {
    // Listen for the restore button click event
    document.querySelectorAll(".restoreButton").forEach((button) => {
        button.addEventListener("click", function () {
            const form = this.closest("form");

            // Submit the form and show SweetAlert
            form.submit();

            Swal.fire({
                position: "center",
                icon: "success",
                title: "Folder berhasil dipulihkan!",
                showConfirmButton: false,
                timer: 1500,
            });
        });
    });
});

//SWEETALERT PULIHKAN FILE
document.addEventListener("DOMContentLoaded", function () {
    // Listen for restore button click event for files
    document.querySelectorAll(".restoreFileButton").forEach((button) => {
        button.addEventListener("click", function () {
            const form = this.closest("form");

            // Submit the form and show SweetAlert
            form.submit();

            Swal.fire({
                position: "center",
                icon: "success",
                title: "File berhasil dipulihkan!",
                showConfirmButton: false,
                timer: 1500,
            });
        });
    });
});

// CHART COUNT FORMULIR
// Ambil elemen textarea dan char-count
const textarea = document.getElementById("reason");
const charCount = document.getElementById("charCount");

// Fungsi untuk mengupdate jumlah karakter
textarea?.addEventListener("input", function () {
    const currentLength = textarea.value.length;
    charCount.textContent = `${currentLength} / 200`; // Menampilkan jumlah karakter yang sudah diinput
});
