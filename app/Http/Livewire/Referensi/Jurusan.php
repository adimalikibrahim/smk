<?php

namespace App\Http\Livewire\Referensi;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Jurusan_sp;

class Jurusan extends Component
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
    public $sortbydesc = 'ASC';
    public $per_page = 10;
    public $nama;
    public $jurusan;
    public $jurusan_sp_id;

    public function getListeners()
    {
        return [
            'confirmed_delete',
        ];
    }
    public function render(){
        return view('livewire.referensi.jurusan', [
            'collection' => Jurusan_sp::orderBy('nama_jurusan_sp', $this->sortbydesc)
            ->when($this->search, function($query){
                $query->where('nama_jurusan_sp', 'ILIKE', '%' . $this->search . '%');
            })->paginate($this->per_page),
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
                'nama' => 'required|unique:jurusan_sp,nama_jurusan_sp',
            ],
            [
                'nama.required' => 'Nama Jurusan tidak boleh kosong!',
            ]
        );
        $id_jurusan = Jurusan_sp::latest()->first('jurusan_id');
        if(!$id_jurusan){
            Jurusan_sp::create([
                'jurusan_sp_id'         => Str::uuid(),
                'jurusan_sp_id_dapodik' => Str::uuid(),
                'sekolah_id'		    => session('sekolah_id'),
                'jurusan_id'		    => 1,
                'nama_jurusan_sp'		=> ucwords($this->nama),
                'last_sync'			=> now(),
            ]);
        }else {
            Jurusan_sp::create([
                'jurusan_sp_id'         => Str::uuid(),
                'jurusan_sp_id_dapodik' => Str::uuid(),
                'sekolah_id'		    => session('sekolah_id'),
                'jurusan_id'		    => $id_jurusan->jurusan_id + 1,
                'nama_jurusan_sp'		=> ucwords($this->nama),
                'last_sync'			=> now(),
            ]);
        }
        $this->reset(['nama']);
        $this->alert('success', 'Berhasil', [
            'text' => 'Data Jurusan berhasil disimpan!'
        ]);
        $this->emit('close-modal');
    }
    public function hapusJurusan($jurusan_sp_id){
        $this->jurusan_sp_id = $jurusan_sp_id;
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
        $a = Jurusan_sp::destroy($this->jurusan_sp_id);
        if($a){
            $this->alert('success', 'Data Jurusan berhasil dihapus', [
                'position' => 'center'
            ]);
            $this->emit('close-modal');
        } else {
            $this->alert('error', 'Data Jurusan gagal dihapus. Silahkan coba beberapa saat lagi!', [
                'position' => 'center'
            ]);
        }
    }
    
}
