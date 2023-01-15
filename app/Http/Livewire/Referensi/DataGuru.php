<?php

namespace App\Http\Livewire\Referensi;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Guru;
use App\Models\Gelar;
use App\Models\Gelar_ptk;
use App\Models\Agama;
use App\Models\Jenis_ptk;
use App\Models\Status_kepegawaian;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class DataGuru extends Component
{
    use WithPagination, LivewireAlert;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function loadPerPage(){
        $this->resetPage();
    }
    public $sortby = 'nama';
    public $sortbydesc = 'ASC';
    public $per_page = 10;
    public $data = 'Guru';
    public $hapus = FALSE;
    public $update = TRUE;
    public $guru_id;
    public $readonly;
    public $disabled;
    public $nama;
    public $gelar_depan = [];
    public $gelar_belakang = [];
    public $ref_gelar_depan = [];
    public $ref_gelar_belakang = [];
    public $ref_agama = [];
    public $ref_jenis_ptk = [];
    public $ref_status_kepegawaian = [];
    public $nuptk;
    public $nip;
    public $nik;
    public $jenis_kelamin;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $agama_id;
    public $alamat;
    public $rt, $rw;
    public $desa_kelurahan;
    public $kecamatan;
    public $kode_pos;
    public $no_hp;
    public $email;
    public $jenis_ptk_id;
    public $status_kepegawaian_id;
    public $dudi_id;
    public $opsi_dudi = FALSE;
    public $tanggal_lahir_str;

    protected $listeners = ['confirmed', 'setTglLahir'];

    public function render()
    {
        return view('livewire.referensi.data-guru', [
            'data_ptk' => Guru::where(function($query){
                $query->whereIn('jenis_ptk_id', jenis_gtk('guru'));
                $query->where('sekolah_id', session('sekolah_id'));
            })->with(['sekolah' => function($query){
                $query->select('sekolah_id', 'nama');
            }])->orderBy($this->sortby, $this->sortbydesc)
                ->when($this->search, function($ptk) {
                    $ptk->where('nama', 'ILIKE', '%' . $this->search . '%')
                    ->orWhere('nuptk', 'ILIKE', '%' . $this->search . '%');
            })->paginate($this->per_page),
            'breadcrumbs' => [
                ['link' => "/", 'name' => "Beranda"], ['link' => '#', 'name' => 'Referensi'], ['name' => "Data Guru"]
            ]
        ]);
    }
    private function loggedUser(){
        return auth()->user();
    }
    public function detil($id){
        $this->reset(['guru_id', 'gelar_depan', 'gelar_belakang', 'nuptk', 'nip', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama_id', 'rt', 'rw', 'desa_kelurahan', 'kecamatan', 'kode_pos', 'no_hp', 'email', 'jenis_ptk_id', 'status_kepegawaian_id']);
        if($this->loggedUser()->hasRole('tu', session('semester_id'))){
            $this->update = FALSE;
            $this->emit('disabled');
        }
        $this->guru_id = $id;
        $this->guru = Guru::with(['gelar_depan', 'gelar_belakang'])->find($id);
        foreach($this->guru->gelar_depan->unique() as $gelar_depan){
            $this->gelar_depan[] = $gelar_depan->gelar_akademik_id;
        }
        //->unique()->implode('display_name', ', ')
        foreach($this->guru->gelar_belakang->unique() as $gelar_belakang){
            $this->gelar_belakang[] = $gelar_belakang->gelar_akademik_id;
        }
        $this->nama = $this->guru->nama;
        $this->nuptk = $this->guru->nuptk;
        $this->nip = $this->guru->nip;
        $this->nik = $this->guru->nik;
        $this->jenis_kelamin = $this->guru->jenis_kelamin;
        $this->tempat_lahir = $this->guru->tempat_lahir;
        $this->tanggal_lahir = $this->guru->tanggal_lahir_indo;
        $this->agama_id = $this->guru->agama_id;
        $this->alamat = $this->guru->alamat;
        $this->rt = $this->guru->rt;
        $this->rw = $this->guru->rw;
        $this->desa_kelurahan = $this->guru->desa_kelurahan;
        $this->kecamatan = $this->guru->kecamatan;
        $this->kode_pos = $this->guru->kode_pos;
        $this->no_hp = $this->guru->no_hp;
        $this->email = $this->guru->email;
        $this->jenis_ptk_id = $this->guru->jenis_ptk_id;
        $this->status_kepegawaian_id = $this->guru->status_kepegawaian_id;
        $this->ref_gelar_depan = Gelar::where('posisi_gelar', 1)->get();
		$this->ref_gelar_belakang = Gelar::where('posisi_gelar', 2)->get();
        $this->ref_agama = Agama::get();
        $this->ref_jenis_ptk = Jenis_ptk::get();
        $this->ref_status_kepegawaian = Status_kepegawaian::get();
        $this->dispatchBrowserEvent('ref_gelar_depan', ['ref_gelar_depan' => $this->ref_gelar_depan]);
        $this->dispatchBrowserEvent('ref_gelar_belakang', ['ref_gelar_belakang' => $this->ref_gelar_belakang]);
        $this->dispatchBrowserEvent('gelar_depan', ['gelar_depan' => $this->gelar_depan]);
        $this->dispatchBrowserEvent('gelar_belakang', ['gelar_belakang' => $this->gelar_belakang]);
        $this->dispatchBrowserEvent('pharaonic.select2.init');
        $this->emit('detilGuru');
    }
    private function updateGelar($data){
        $find = Gelar_ptk::where(function($query) use ($data){
            $query->where('sekolah_id', session('sekolah_id'));
            $query->where('guru_id', $this->guru_id);
            $query->where('gelar_akademik_id', $data);
        })->first();
        if(!$find){
            Gelar_ptk::create(
                [
                    'gelar_ptk_id' => Str::uuid(),
                    'sekolah_id' => session('sekolah_id'),
                    'guru_id' => $this->guru_id,
                    'gelar_akademik_id' => $data,
                    'ptk_id' => $this->guru_id,
                    'last_sync' => now(),
                ]
            );
        }
    }
    public function perbaharui(){
        $data = Guru::with(['pengguna'])->find($this->guru_id);
        $validation = ($data->pengguna) ? ['required', 'email', 'max:255', Rule::unique('users')->ignore($data->pengguna->user_id, 'user_id')] : ['required', 'email', 'max:255', Rule::unique('users')];
        $this->validate(
            [
                'email' => $validation,
                'tanggal_lahir' => ['required', 'date'],
                'nuptk' => ['nullable', 'digits:16', 'numeric', Rule::unique('guru')->ignore($this->guru_id, 'guru_id')],
                'nik' => ['required', 'digits:16', 'numeric', Rule::unique('guru')->ignore($this->guru_id, 'guru_id')]
            ],
            [
                'email.required' => 'Email tidak boleh kosong!',
                'email.email' => 'Email tidak valid!',
                'email.unique' => 'Email sudah terdaftar di Database!',
                'tanggal_lahir.required' => 'Tanggal Lahir tidak boleh kosong!',
                'tanggal_lahir.date' => 'Format Tanggal Lahir salah!',
                'nik.required' => 'NIK tidak boleh kosong!',
                'nik.digits' => 'NIK harus 16 digit!',
                'nik.unique' => 'NIK sudah terdaftar!',
                'nik.numeric' => 'NIK harus berupa angka!',
                'nuptk.digits' => 'NUPTK harus 16 digit!',
                'nuptk.unique' => 'NIK sudah terdaftar!',
                'nuptk.numeric' => 'NIK harus berupa angka!',
            ]
        );
        Gelar_ptk::where(function($query){
            $query->has('gelar_depan');
            $query->where('guru_id', $this->guru_id);
            $query->whereNotIn('gelar_akademik_id', $this->gelar_depan);
        })->delete();
        Gelar_ptk::where(function($query){
            $query->has('gelar_belakang');
            $query->where('guru_id', $this->guru_id);
            $query->whereNotIn('gelar_akademik_id', $this->gelar_belakang);
        })->delete();
        if($this->gelar_depan){
            foreach($this->gelar_depan as $depan){
                $this->updateGelar($depan);
            }
        }
        if($this->gelar_belakang){
            foreach($this->gelar_belakang as $belakang){
                $this->updateGelar($belakang);
            }
        }
        $data->nama = $this->nama;
        $data->nuptk = $this->nuptk;
        $data->nip = $this->nip;
        $data->nik = $this->nik;
        $data->jenis_kelamin = $this->jenis_kelamin;
        $data->tempat_lahir = $this->tempat_lahir;
        $data->tanggal_lahir = $this->tanggal_lahir;
        $data->agama_id = $this->agama_id;
        $data->alamat = $this->alamat;
        $data->rt = $this->rt;
        $data->rw = $this->rw;
        $data->desa_kelurahan = $this->desa_kelurahan;
        $data->kecamatan = $this->kecamatan;
        $data->kode_pos = $this->kode_pos;
        $data->no_hp = $this->no_hp;
        $data->email = $this->email;
        if($data->save()){
            $user = User::where('email', $this->email)->first();
            $role = Role::where('name', 'guru')->first();
            if($user){
                $user->email = $this->email;
                $user->save();
            } else {
                $new_password = strtolower(Str::random(8));
                $user = User::create([
                    'name' => $data->nama,
                    'email' => $this->email,
                    'nuptk'	=> $this->nuptk,
                    'password' => bcrypt($new_password),
                    'last_sync'	=> now(),
                    'sekolah_id'	=> session('sekolah_id'),
                    'password_dapo'	=> md5($new_password),
                    'guru_id'	=> $this->guru_id,
                    'default_password' => $new_password,
                ]);
            }
            if(!$user->hasRole($role, session('semester_id'))){
                $user->attachRole($role, session('semester_id'));
            }
        }

        $this->reset(['guru_id', 'gelar_depan', 'gelar_belakang', 'nuptk', 'nip', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama_id', 'rt', 'rw', 'desa_kelurahan', 'kecamatan', 'kode_pos', 'no_hp', 'email', 'jenis_ptk_id', 'status_kepegawaian_id']);
        $this->emit('close-modal');
        $this->alert('success', 'Data '.$this->data.' berhasil diperbaharui', [
            'position' => 'center'
        ]);
    }

    public function setTglLahir($value){
        $this->tanggal_lahir = Carbon::createFromTimeStamp(strtotime($value))->format('Y-m-d');
        $this->tanggal_lahir_str = Carbon::createFromTimeStamp(strtotime($value))->translatedFormat('j F Y');
    }
}
