<div>
    @include('panels.breadcrumb')
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                @include('components.navigasi-table')
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Nama</th>
                            <th class="text-center">NISN</th>
                            <th class="text-center">L/P</th>
                            <th class="text-center">Tempat, Tanggal Lahir</th>
                            <th class="text-center">Kelas</th>
                            @role(['admin', 'waka', 'tu', 'wali'], session('semester_id'))
                                <th class="text-center">Detil</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @if ($collection->count())
                            @foreach ($collection as $item)
                                <tr>
                                    <td>{{ $item->nama }}</td>
                                    <td class="text-center">{{ $item->nisn }}</td>
                                    <td class="text-center">{{ $item->jenis_kelamin }}</td>
                                    <td>{{ $item->tempat_lahir }}, {{ $item->tanggal_lahir }}</td>
                                    @if ($item->anggota_rombel)
                                        <td>{{ $item->anggota_rombel->rombongan_belajar->nama }}</td>
                                    @else
                                        <td class="text-center">-</td>
                                    @endif
                                    @role(['admin', 'waka', 'tu', 'wali'], session('semester_id'))
                                        <td class="text-center"><button class="btn btn-info btn-sm"
                                                wire:click="getID('{{ $item->peserta_didik_id }}')">Detil</button></td>
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
                <div class="row justify-content-between mt-2">
                    <div class="col-6">
                        @if ($collection->count())
                            <p>Menampilkan {{ $collection->firstItem() }} sampai
                                {{ $collection->firstItem() + $collection->count() - 1 }} dari
                                {{ $collection->total() }} data</p>
                        @endif
                    </div>
                    <div class="col-6">
                        {{ $collection->onEachSide(1)->links('components.custom-pagination-links-view') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.referensi.modal.import-pd')
    @include('livewire.referensi.modal.detil-pd')
    @include('livewire.referensi.modal.progress')
    @include('components.loader')
</div>
@push('scripts')
    <script>
        Livewire.on('showModal', event => {
            $('#pdModal').modal('show');
        })
        Livewire.on('close-modal', event => {
            $('#pdModal').modal('hide');
        })
        Livewire.on('show-modal', event => {
            $('#detilPD').modal('show');
        })
        Livewire.on('progress', event => {
            $('#progressBar').modal('show');
        })
        Livewire.on('close-modal', event => {
            $('#detilPD').modal('hide');
        })
        // window.addEventListener('data_jurusan', event => {
        //     console.log(event.detail.data_jurusan);
        //     $('#filter_jurusan').html('<option value="">== Filter Jurusan ==</option>')
        //     $('#filter_rombel').html('<option value="">== Filter Rombel ==</option>')
        //     $.each(event.detail.data_jurusan, function(i, item) {
        //         $('#filter_jurusan').append($('<option>', {
        //             value: item.jurusan_sp_id,
        //             text: item.nama_jurusan_sp
        //         }));
        //     });
        // })
        // window.addEventListener('data_rombel', event => {
        //     $('#filter_rombel').html('<option value="">== Filter Rombel ==</option>')
        //     $.each(event.detail.data_rombel, function(i, item) {
        //         $('#filter_rombel').append($('<option>', {
        //             value: item.rombongan_belajar_id,
        //             text: item.nama
        //         }));
        //     });
        // })
    </script>
@endpush
