<?php

namespace App\Http\Livewire\Referensi;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Rombongan_belajar;
use App\Models\Peserta_didik;
use App\Models\Pembelajaran;
use App\Models\Anggota_rombel;
use App\Models\Guru;
use App\Models\Jurusan_sp;
use App\Models\Kelompok;

class DataRombonganBelajar extends Component
{
    use WithPagination;
    use LivewireAlert;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function loadPerPage(){
        $this->resetPage();
    }
    public $sortby = 'tingkat';
    public $sortbydesc = 'ASC';
    public $per_page = 10;
    public $anggota_rombel = [];
    public $pembelajaran = [];
    public $nama_mata_pelajaran = [];
    public $guru_pengajar = [];
    public $pengajar;
    public $data_kelompok;
    public $kelompok_id;
    public $no_urut;
    public $nama_kelas;
    public $anggota_rombel_id;
    public $rombongan_belajar_id;
    public $pembelajaran_id;
    public $jurusan_id;
    public $walas_id;
    public $tingkat;
    public $nama;

    public function getListeners()
    {
        return [
            'confirmed_delete',
            'pembelajaranTersimpan',
            'hapus_pembelajaran',
        ];
    }
    public function render()
    {
        $where = function($query){
            $query->where('jenis_rombel', 1);
            $query->where('semester_id', session('semester_aktif'));
            $query->where('sekolah_id', session('sekolah_id'));
        };
        return view('livewire.referensi.data-rombongan-belajar', [
            'collection' => Rombongan_belajar::where($where)->with([
                'wali_kelas' => function($query){
                    $query->select('guru_id', 'nama');
                },
                'jurusan_sp' => function($query){
                    $query->select('jurusan_sp_id', 'nama_jurusan_sp');
                },
                'kurikulum' => function($query){
                    $query->select('kurikulum_id', 'nama_kurikulum');
                },
            ])
            ->orderBy($this->sortby, $this->sortbydesc)
            ->orderBy('nama', $this->sortbydesc)
            ->when($this->search, function($query) use ($where){
                $query->where('nama', 'ILIKE', '%' . $this->search . '%');
                $query->where($where);
                $query->orWhereIn('guru_id', function($query){
                    $query->select('guru_id')
                    ->from('guru')
                    ->where('sekolah_id', session('sekolah_id'))
                    ->where('jenis_rombel', 1)
                    ->where('semester_id', session('semester_aktif'))
                    ->where('nama', 'ILIKE', '%' . $this->search . '%');
                });
                $query->where($where);
            })->paginate($this->per_page),
            'all_jurusan' => Jurusan_sp::get(),
            'all_guru' => Guru::get(),
            'breadcrumbs' => [
                ['link' => "/", 'name' => "Beranda"], ['link' => '#', 'name' => 'Referensi'], ['name' => "Data Rombongan Belajar"]
            ],
            'tombol_add' => [
                'wire' => 'addModal',
                'color' => 'primary',
                'text' => 'Tambah Data',
            ],
        ]);
    }
    public function addModal(){
        $this->emit('showModal');
    }
    public function store(){
        $this->validate(
            [
                'jurusan_id' => 'required',
                'walas_id' => 'required|unique:rombongan_belajar,guru_id',
                'nama' => 'unique:rombongan_belajar,nama',
                'tingkat' => 'required',
            ],
            [
                'jurusan_id.required' => 'jurusan Jurusan tidak boleh kosong!',
                'walas_id.required' => 'walas tidak boleh kosong!',
                'nama.unique' => 'Nama Rombel sudah digunakan!',
                'tingkat.required' => 'tingkat Jurusan tidak boleh kosong!',
            ]
        );
        $jurusan = Jurusan_sp::find($this->jurusan_id)->first();
        if($jurusan){
            Rombongan_belajar::create([
                'rombongan_belajar_id'         => Str::uuid(),
                'sekolah_id'		    => session('sekolah_id'),
                'semester_id'           => session('semester_aktif'),
                'jurusan_id'		    => $jurusan->jurusan_id,
                'jurusan_sp_id'		    => $jurusan->jurusan_sp_id,
                'guru_id'		        => $this->walas_id,
                'nama'		            => ucwords($this->tingkat.' '.$jurusan->nama_jurusan_sp),
                'tingkat'               => $this->tingkat,
                'last_sync'			    => now(),
            ]);
        }

        $this->reset(['walas_id','jurusan_id','tingkat']);
        $this->alert('success', 'Berhasil', [
            'text' => 'Data Jurusan berhasil disimpan!'
        ]);
        $this->emit('close-modal');
    }


    public function getAnggota($rombongan_belajar_id){
        $this->rombongan_belajar_id = $rombongan_belajar_id;
        $this->anggota_rombel = Peserta_didik::with(['anggota_rombel' => function($query) use ($rombongan_belajar_id){
            $query->where('rombongan_belajar_id', $rombongan_belajar_id);
        }])->whereHas('anggota_rombel', function($query) use ($rombongan_belajar_id){
            $query->where('rombongan_belajar_id', $rombongan_belajar_id);
        })->orderBy('nama')->get();
        $this->getRombel();
    }
    public function getPembelajaran($rombongan_belajar_id){
        $this->reset(['pembelajaran', 'pengajar', 'kelompok_id', 'no_urut', 'nama_mata_pelajaran', 'guru_pengajar', 'data_kelompok']);
        $this->rombongan_belajar_id = $rombongan_belajar_id;
        $rombongan_belajar = Rombongan_belajar::find($this->rombongan_belajar_id);
        $merdeka = Str::of($rombongan_belajar->kurikulum->nama_kurikulum)->contains('Merdeka');
        if($merdeka){
            $kurikulum = 2022;
        } else {
            $kurikulum = 2017;
        }
        $this->getPengajar();
        $this->getKelompok($kurikulum);
        $this->getRombel();
        $this->pembelajaran = Pembelajaran::where('rombongan_belajar_id', $rombongan_belajar_id)->whereNull('induk_pembelajaran_id')->orderBy('mata_pelajaran_id')->get();
        $pengajar = [];
        $kelompok_id = [];
        $no_urut = [];
        $nama_mata_pelajaran = [];
        $pembelajaran_id = [];
        foreach($this->pembelajaran as $urut => $pembelajaran){
            $pengajar[$pembelajaran->pembelajaran_id] = $pembelajaran->guru_pengajar_id;
            $kelompok_id[$pembelajaran->pembelajaran_id] = $pembelajaran->kelompok_id;
            $no_urut[$pembelajaran->pembelajaran_id] = $pembelajaran->no_urut;
            $nama_mata_pelajaran[$pembelajaran->pembelajaran_id] = $pembelajaran->nama_mata_pelajaran;
            $pembelajaran_id[] = $pembelajaran->pembelajaran_id;
        }
        $this->pengajar = $pengajar;
        $this->kelompok_id = $kelompok_id;
        $this->no_urut = $no_urut;
        $this->nama_mata_pelajaran = $nama_mata_pelajaran;
        $this->pembelajaran_id = $pembelajaran_id;
        $this->emit('show-pembelajaran');
        $this->dispatchBrowserEvent('pembelajaran', [
            'guru_pengajar' => $this->guru_pengajar,
            'data_kelompok' => $this->data_kelompok,
            'kelompok_id' => $this->kelompok_id,
            'pengajar' => $this->pengajar,
            'no_urut' => $this->no_urut,
            'nama_mata_pelajaran' => $this->nama_mata_pelajaran,
            'pembelajaran_id' => $this->pembelajaran_id,
        ]);
        /*foreach($this->pembelajaran as $urut => $pembelajaran){
            $this->dispatchBrowserEvent('pharaonic.select2.load', [
                'component' => $this->id,
                'target'    => '#pengajar_'.$urut,
            ]);
            $this->dispatchBrowserEvent('pharaonic.select2.load', [
                'component' => $this->id,
                'target'    => '#kelompok_id_'.$urut,
            ]);
        }*/
        $this->dispatchBrowserEvent('pharaonic.select2.init');
    }
    public function simpanPembelajaran(){
        $this->validate(
            [
                'nama_mata_pelajaran.*' => 'required',
                'no_urut.*' => 'nullable|numeric',
            ],
            [
                'nama_mata_pelajaran.*.required' => 'Nama Mata Pelajaran tidak boleh kosong!',
                'no_urut.*.numeric' => 'Nomor urut harus berupa angka!',
            ]
        );
        foreach($this->pembelajaran_id as $urut => $pembelajaran_id){
            $update = Pembelajaran::find($pembelajaran_id);
            $update->nama_mata_pelajaran = $this->nama_mata_pelajaran[$update->pembelajaran_id];
            $update->guru_pengajar_id = (Str::isUuid($this->pengajar[$update->pembelajaran_id])) ? $this->pengajar[$update->pembelajaran_id] : NULL;
            $update->kelompok_id = (is_numeric($this->kelompok_id[$update->pembelajaran_id])) ? $this->kelompok_id[$update->pembelajaran_id] : NULL;
            $update->no_urut = $this->no_urut[$update->pembelajaran_id];
            $update->save();
        }
        $this->alert('success', 'Pembelajaran berhasil disimpan', [
            'showConfirmButton' => true,
            'confirmButtonText' => 'OK',
            'onConfirmed' => 'pembelajaranTersimpan',
            'allowOutsideClick' => false,
            'timer' => null
        ]);
    }
    public function pembelajaranTersimpan(){
        $this->reset(['nama_kelas', 'pembelajaran', 'pengajar', 'kelompok_id', 'no_urut']);
        $this->emit('close-modal');
    }
    public function keluarkanAnggota($anggota_rombel_id, $rombongan_belajar_id){
        $this->rombongan_belajar_id = $rombongan_belajar_id;
        $this->anggota_rombel_id = $anggota_rombel_id;
        $this->alert('question', 'Apakah Anda yakin?', [
            'text' => 'Tindakan ini tidak dapat dikembalikan',
            'showConfirmButton' => true,
            'confirmButtonText' => 'OK',
            'onConfirmed' => 'confirmed_delete',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'allowOutsideClick' => false,
            'timer' => null
        ]);
    }
    public function confirmed_delete(){
        $a = Anggota_rombel::find($this->anggota_rombel_id)->first();
        $p = Peserta_didik::where('peserta_didik_id', $a->peserta_didik_id)->update(['diterima_kelas' => null]);
        $a->forceDelete();
        $this->alert('success', 'Anggota Rombel berhasil dikeluarkan', [
            'showConfirmButton' => true,
            'confirmButtonText' => 'OK',
            'onConfirmed' => 'confirmed',
            'allowOutsideClick' => false,
            'timer' => null
        ]);
        $this->getAnggota($this->rombongan_belajar_id);
    }
    public function getPengajar(){
        $this->guru_pengajar = Guru::where('sekolah_id', session('sekolah_id'))->orderBy('nama')->get();
    }
    public function getKelompok($kurikulum){
        $this->data_kelompok = Kelompok::where(function($query) use ($kurikulum){
            $query->where('kurikulum', $kurikulum);
            if($kurikulum != 2022){
                $query->orWhere('kurikulum', 0);
            }
        })->orderBy('kelompok_id')->get();
    }
    public function getRombel(){
        $find = Rombongan_belajar::find($this->rombongan_belajar_id);
        $this->nama_kelas = $find->nama;
    }
    public function hapusPembelajaran($pembelajaran_id){
        $this->pembelajaran_id = $pembelajaran_id;
        $this->alert('question', 'Apakah Anda yakin?', [
            'text' => 'Tindakan ini akan menghapus Guru Pengajar, Kelompok & Nomor Urut',
            'showConfirmButton' => true,
            'confirmButtonText' => 'OK',
            'onConfirmed' => 'hapus_pembelajaran',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'allowOutsideClick' => false,
            'timer' => null
        ]);
    }
    public function hapus_pembelajaran(){
        $update = Pembelajaran::find($this->pembelajaran_id);
        $update->kelompok_id = NULL;
        $update->guru_pengajar_id = NULL;
        $update->no_urut = NULL;
        if($update->save()){
            $this->alert('success', 'Pembelajaran berhasil di reset!', [
                'showConfirmButton' => true,
                'confirmButtonText' => 'OK',
                'onConfirmed' => 'pembelajaranTersimpan',
                'allowOutsideClick' => false,
                'timer' => null
            ]);
        } else {
            $this->alert('error', 'Pembelajaran gagal di reset. Coba beberapa saat lagi!', [
                'showConfirmButton' => true,
                'confirmButtonText' => 'OK',
                'onConfirmed' => 'confirmed',
                'allowOutsideClick' => false,
                'timer' => null
            ]);
        }
    }
}
