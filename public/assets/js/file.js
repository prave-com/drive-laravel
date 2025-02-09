// TOGGLE GRID AND LIST VIEW
document.addEventListener("DOMContentLoaded", function () {
    const toggleViewBtn = document.getElementById("toggleViewBtn");
    const gridView = document.querySelector(".grid-view");
    const listView = document.querySelector(".list-view");

    // Setel tampilan awal: grid view terlihat, list view tersembunyi
    listView.classList.add("d-none");
    gridView.classList.remove("d-none");

    // Tambahkan event listener untuk tombol toggle
    toggleViewBtn.addEventListener("click", function () {
        if (gridView.classList.contains("d-none")) {
            // Jika grid tersembunyi, tampilkan grid dan sembunyikan list
            gridView.classList.remove("d-none");
            listView.classList.add("d-none");
            this.innerHTML =
                '<i class="fa-solid fa-border-all fa-sm text-white"></i> | Icon'; // Tampilkan ikon grid
        } else {
            // Jika grid terlihat, sembunyikan grid dan tampilkan list
            gridView.classList.add("d-none");
            listView.classList.remove("d-none");
            this.innerHTML =
                '<i class="fa-solid fa-list fa-sm text-white"></i> | List'; // Tampilkan ikon list
        }
    });
});

//FILE EDIT
document.addEventListener("DOMContentLoaded", function () {
    // Menambahkan event listener untuk ikon edit (fa-file-pen) di Grid View dan List View
    const editIcons = document.querySelectorAll(".edit-file");

    editIcons.forEach((icon) => {
        icon.addEventListener("click", function () {
            // Mendapatkan ID file dari atribut data-file-id
            const fileId = this.getAttribute("data-file-id");

            // Menampilkan atau menyembunyikan form rename file di Grid View dan List View
            const renameForm = document.getElementById("rename-form-" + fileId);
            if (
                renameForm.style.display === "none" ||
                renameForm.style.display === ""
            ) {
                renameForm.style.display = "block";
            } else {
                renameForm.style.display = "none";
            }
        });
    });
});

document
    .getElementById("createFolderForm")
    ?.addEventListener("submit", function (e) {
        e.preventDefault();
        const folderName = document.getElementById("name").value;

        const folderContainer = document.querySelector(".folder-container");
        const newFolder = document.createElement("div");
        newFolder.classList.add("folder-item", "card", "text-center", "me-3");
        newFolder.innerHTML = `
                  <div class="card-body">
                    <img src="folder-icon.png" alt="Folder Icon" class="img-fluid mb-2" />
                    <h6>${folderName}</h6>
                  </div>
                `;

        folderContainer.appendChild(newFolder);
        document.getElementById("createFolderForm").reset();
        const modal = bootstrap.Modal.getInstance(
            document.getElementById("createFolderModal"),
        );
        modal.hide();
    });

// RENAME FILE
document.addEventListener("DOMContentLoaded", function () {
    const modals = document.querySelectorAll(".modal");
    modals.forEach((modal) => {
        modal.addEventListener("show.bs.modal", function () {
            console.log("Modal is shown:", modal.id);
        });
    });
});
