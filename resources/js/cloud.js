// SIDEBAR ACTIVE STATE

// TOGGLE SIDEBAR
const menuBar = document.querySelector("#content .navbar .bx.bx-menu");
const sidebar = document.getElementById("sidebar");

menuBar.addEventListener("click", () => {
    sidebar.classList.toggle("hide");
});

// RESPONSIVE SEARCH TOGGLE
const searchButton = document.querySelector(
    "#content .navbar form .input-group button",
);
const searchForm = document.querySelector("#content .navbar form");
const searchIcon = searchButton.querySelector(".bx");

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

// SIDEBAR ICON BEHAVIOR

// UPLOAD FILE SIDEBAR
document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.querySelector(".file-selector-input");
    const dropArea = document.querySelector(".drop-section");
    const dropHere = document.querySelector(".drop-here");
    const browseButton = document.querySelector(".file-selector");

    // Fungsi untuk menampilkan nama file yang dipilih
    function handleFiles(files) {
        for (let i = 0; i < files.length; i++) {
            console.log("File uploaded:", files[i].name);
        }
    }

    // Event listener untuk input file
    fileInput.addEventListener("change", function (event) {
        const files = event.target.files;
        handleFiles(files);
    });

    // Prevent default drag behaviors
    ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop area when item is dragged over it
    ["dragenter", "dragover"].forEach((eventName) => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    // Remove highlight when item is no longer hovering
    ["dragleave", "drop"].forEach((eventName) => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    // Handle dropped files
    dropArea.addEventListener("drop", (event) => {
        const dt = event.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    });

    // Click event for the browse button
    browseButton.addEventListener("click", function () {
        fileInput.click(); // Trigger the file input click
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        dropHere.classList.add("highlight"); // Add highlight class
    }

    function unhighlight() {
        dropHere.classList.remove("highlight"); // Remove highlight class
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const borderUpload = document.querySelector(".border-upload");
    const fileInput = document.getElementById("fileInput");

    // Event listener untuk border upload
    borderUpload.addEventListener("click", function () {
        fileInput.click(); // Trigger the file input click
    });

    // Event listener untuk input file
    fileInput.addEventListener("change", function (event) {
        const fileList = event.target.files;
        if (fileList.length > 0) {
            console.log("File selected:", fileList[0].name);
            // Lakukan sesuatu dengan file yang diupload, misalnya menguploadnya ke server
        }
    });
});
