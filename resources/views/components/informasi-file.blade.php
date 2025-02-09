<!-- Offcanvas for File Information -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="fileInfoOffcanvas" aria-labelledby="fileInfoOffcanvasLabel">
    <div class="offcanvas-header">
        <h4 class="offcanvas-title" id="fileInfoOffcanvasLabel"><strong>Detail File</strong>
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <p><strong>Nama File:</strong> <span id="fileName"></span></p>
        <p><strong>Jenis File:</strong> <span id="fileType"></span></p>
        <p><strong>Ukuran:</strong> <span id="fileSize"></span></p>
        <p><strong>Lokasi:</strong> <a href="{{ route('folders.show', $file->parent) }}"><span
                    id="fileLocation"></span></a></p>
        <p><strong>Pemilik:</strong> <span id="fileOwner"></span></p>
        <p><strong>Tanggal Akses:</strong> <span id="fileDate"></span></p>
    </div>

</div>
