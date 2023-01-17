<div>
    @include('panels.breadcrumb')
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                @include('components.navigasi-table')
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width=5% class="text-center align-middle">No</th>
                                <th class="text-center align-middle">Jurusan</th>
                                <th width=10% class="text-center align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($collection->count())
                                @foreach($collection as $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                    <td>{{$item->nama_jurusan_sp}}</td>
                                    @role(['admin', 'waka', 'tu', 'wali'], session('semester_id'))
                                    <td class="text-center"><button class="btn btn-danger btn-sm" wire:click.prevent="hapusJurusan('{{$item->jurusan_sp_id}}')">Hapus</button></td>
                                    @endrole
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="7">Tidak ada data untuk ditampilkan</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="row justify-content-between mt-2">
                    <div class="col-6">
                        @if($collection->count())
                        <p>Menampilkan {{ $collection->firstItem() }} sampai {{ $collection->firstItem() + $collection->count() - 1 }} dari {{ $collection->total() }} data</p>
                        @endif
                    </div>
                    <div class="col-6">
                        {{ $collection->onEachSide(1)->links('components.custom-pagination-links-view') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.referensi.modal.tambah-jurusan')
    {{-- @include('livewire.referensi.modal.detil-pd') --}}
    @include('components.loader')
</div>
@push('scripts')
<script>
    Livewire.on('showModal', event => {
        $('#tambahJurusan').modal('show');
    })
    Livewire.on('close-modal', event => {
        $('#tambahJurusan').modal('hide');
    })
</script>
@endpush
