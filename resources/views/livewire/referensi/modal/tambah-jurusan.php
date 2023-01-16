<div>
    <div wire:ignore.self class="modal fade" id="tambahJurusan" tabindex="-1" aria-labelledby="tambahJurusanLabel" aria-hidden="true" data-bs-backdrop="true">
        <div class="modal-dialog modal-ml modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahJurusanLabel">Tambah Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <label for="nama" class="col-sm-3 col-form-label">Id Jurusan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" wire:model="id">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="nama" class="col-sm-3 col-form-label">Nama Jurusan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" wire:model="nama">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" wire:click.prevent="store()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
