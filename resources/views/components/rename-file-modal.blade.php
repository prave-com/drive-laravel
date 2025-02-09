<div class="modal fade" id="renameModal{{ $file->id }}" tabindex="-1"
    aria-labelledby="renameModalLabel{{ $file->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameModalLabel{{ $file->id }}">Rename File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="renameForm{{ $file->id }}" action="{{ route('files.update', $file) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="name" value="{{ $file->name }}" required class="form-control">
                    <x-input-error :messages="$errors->getBag('rename_file_' . $file->id)->get('name')" class="mt-2" />
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="renameForm{{ $file->id }}">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
