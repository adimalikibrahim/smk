<div>
    <div wire:ignore.self class="modal fade" id="tambahRombel" tabindex="-1" aria-labelledby="tambahRombelLabel"
        aria-hidden="true" data-bs-backdrop="true">
        <div class="modal-dialog modal-ml modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahRombelLabel">Tambah Rombonagn Belajar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <label for="jurusan_id" class="col-sm-3 col-form-label">Jurusan</label>
                        <div class="col-sm-12" wire:ignore>
                            <select id="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror"
                                data-component-id="{{ $this->id }}" data-search-off="true"
                                data-placeholder="== Pilih Wali Kelas ==" wire:model="jurusan_id"
                                data-pharaonic="select2">
                                <option value="">== Pilih Jurusan ==</option>
                                @foreach ($all_jurusan as $jurusan)
                                    <option value="{{ $jurusan->jurusan_sp_id }}">{{ $jurusan->nama_jurusan_sp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('jurusan_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <label for="kurikulum_id" class="col-sm-3 col-form-label">Wali Kelas</label>
                        <div class="col-sm-12" wire:ignore>
                            <select id="walas_id" class="form-select @error('walas_id') is-invalid @enderror"
                                data-component-id="{{ $this->id }}" data-search-off="true"
                                data-placeholder="== Pilih Wali Kelas ==" wire:model="walas_id"
                                data-pharaonic="select2">
                                <option value="">== Pilih Wali Kelas ==</option>
                                @foreach ($all_guru as $guru)
                                    <option value="{{ $guru->guru_id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('walas_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <label for="tingkat" class="col-sm-3 col-form-label">Tingkat</label>
                        <div class="col-sm-12" wire:ignore>
                            <select id="tingkat" class="form-select" wire:model="tingkat" data-pharaonic="select2"
                                data-search-off="true" data-component-id="{{ $this->id }}"
                                data-placeholder="== Pilih Tingkat ==">
                                <option value="">== Pilih Tingkat ==</option>
                                <option value="10" selected>Santri</option>
                                <option value="11">Hamud</option>
                                <option value="12">Hamidu</option>
                            </select>
                        </div>
                        @error('tingkat')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    @error('nama')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" wire:click.prevent="store()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
