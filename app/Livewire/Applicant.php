<?php

namespace App\Livewire;

use Livewire\Component;
use App\Rules\FileSizeLimit;
use Illuminate\Http\Request;
use App\Models\Applicantdata;
use App\Models\Applicantfile;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Rules\AllowedFileExtension;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class Applicant extends Component
{
    use WithFileUploads;
    // #[Validate('image|max:1024')]
    public $files = [];
    public $filenames = [];

    public $is_registered, $show, $showMenu, $showSubmit, $registeredEmail, $registeredPassword, $is_update;
    public $nama, $email, $password, $confirm_password, $hp, $telp, $tempat_lahir, $tgl_lahir, $gender;
    public $status_pernikahan, $golongan_darah, $agama, $etnis, $nama_contact_darurat;
    public $contact_darurat_1, $contact_darurat_2, $jenis_identitas, $no_identitas;
    public $alamat_identitas, $alamat_tinggal_sekarang;
    public $applicant_id, $originalName, $filename;
    public $toggle_eye_password;
    public $id;


    public function toggleEyePassword()
    {
        $this->toggle_eye_password = !$this->toggle_eye_password;
    }


    public function deleteFile($id)
    {
        // $data = Applicantfile::where('filename', $filename)->first();
        $data = Applicantfile::find($id);
        if ($data != null) {


            try {

                // $result = Storage::disk('google')->delete($data->filename);
                $result = Storage::disk('public')->delete($data->filename);
                if ($result) {
                    // File was deleted successfully
                    $data->delete();
                    // $this->dispatch('success', message: 'File telah di delete');
                    $this->dispatch(
                        'message',
                        type: 'success',
                        title: 'File telah di delete',
                    );

                    return 'File deleted successfully.';
                } else {
                    // File could not be deleted
                    // return 'Failed to delete file.';


                    // $this->dispatch('error', message: 'File GAGAL di delete');
                    $this->dispatch(
                        'message',
                        type: 'error',
                        title: 'File GAGAL di delete',
                    );
                }
            } catch (\Exception $e) {
                // An error occurred while deleting the file
                return 'An error occurred: ' . $e->getMessage();
            }
        } else {
            // $this->dispatch('error', message: 'File tidak ketemu');
            $this->dispatch(
                'message',
                type: 'error',
                title: 'File tidak ketemu',
            );
        }
    }

    public function submit()
    {

        $this->validate([
            'registeredEmail' => 'required|email',
            'registeredPassword' => 'required|min:6',
        ], [
            'registeredEmail.required' => 'Email wajib diisi',
            'registeredEmail.email' => 'Format email harus benar',
            'registeredPassword.required' => 'Password wajib diisi',
            'registeredPassword.min' => 'Password minimal 6 karakter',
        ]);
        $data = Applicantdata::where('email', $this->registeredEmail)->where('password', $this->registeredPassword)->first();
        if ($data != null) {
            $file_data = Applicantfile::where('id_karyawan', $data->applicant_id)->get();
            // dd($file_data);
            $this->filenames = $file_data;
            $this->showMenu = false;
            $this->show = true;
            //    ==============================
            $this->id = $data->id;
            $this->applicant_id = $data->applicant_id;
            $this->nama = $data->nama;
            $this->email = $data->email;
            $this->password = $data->password;
            $this->confirm_password = $data->password;
            $this->hp = $data->hp;
            $this->telp = $data->telp;
            $this->tempat_lahir = $data->tempat_lahir;
            $this->tgl_lahir = $data->tgl_lahir;
            $this->gender = $data->gender;
            $this->status_pernikahan = $data->status_pernikahan;
            $this->golongan_darah = $data->golongan_darah;
            $this->agama = $data->agama;
            $this->etnis = $data->etnis;
            $this->nama_contact_darurat = $data->nama_contact_darurat;
            $this->contact_darurat_1 = $data->contact_darurat_1;
            $this->contact_darurat_2 = $data->contact_darurat_2;
            $this->jenis_identitas = $data->jenis_identitas;
            $this->no_identitas = $data->no_identitas;
            $this->alamat_identitas = $data->alamat_identitas;
            $this->alamat_tinggal_sekarang = $data->alamat_tinggal_sekarang;

            //    ==============================
            $this->showSubmit = false;
        } else {
            // $this->dispatch('error', message: 'Email atau password salah');
            $this->dispatch(
                'message',
                type: 'error',
                title: 'Email atau password salah',
            );
            $this->showSubmit = true;
        }
        $this->is_update = true;
    }



    public function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'confirm_password.required' => 'Confirm Password wajib diisi.',
            'hp.required' => 'Handphone wajib diisi.',
            'telp.required' => 'Telepon wajib diisi.',
            'tempat_lahir.required' => 'Kota Kelahiran wajib diisi.',
            'tgl_lahir.required' => 'Tanggal Lahir wajib diisi.',
            'gender.required' => 'Gender wajib diisi.',
            'status_pernikahan.required' => 'Status Pernikahan wajib diisi.',
            'golongan_darah.required' => 'Golongan Darah wajib diisi.',
            'agama.required' => 'Agama wajib diisi.',
            'etnis.required' => 'Etnis wajib diisi.',
            'nama_contact_darurat.required' => 'Nama Konta Darurat wajib diisi.',
            'contact_darurat_1.required' => 'Kontak Darurat 1 wajib diisi.',
            'jenis_identitas.required' => 'Jenis Identitas wajib diisi.',
            'no_identitas.required' => 'No Identitas wajib diisi.',
            'alamat_identitas.required' => 'Alamat Identitas wajib diisi.',
            'alamat_tinggal_sekarang.required' => 'Alamat tinggal tekarang wajib diisi.',
            'files.*.mimes' => 'Hanya menerima file png, jpg dan jpeg',
            'files.*.max' => 'Max file size 1Mb',

            'nama.min' => 'Nama minimal 5 karakter.',
            'password.min' => 'Password minimal 6 karakter.',
            'hp.min' => 'Handphone minimal 10 karakter.',
            'telp.min' => 'Telepon minimal 9 karakter.',
            'contact_darurat_1.min' => 'Kontak Darurat 1 minimal 10 karakter.',
            'contact_darurat_2.min' => 'Kontak Darurat 2 minimal 10 karakter.',
            'confirm_password.min' => 'Konfirmasi Password minimal 6 karakter.',
            'confirm_password.same' => 'Konfirmasi Password Berbeda',
            'email.unique' => 'Email ini sudah terdaftar dalam database',
            'tgl_lahir.date' => 'Harus berupa format tanggal yang bear.',
            'tgl_lahir.before' => 'Tanggal Lahir anda salah.',

        ];
    }

    public function rules()
    {
        return [
            'nama' => 'required|min:2',
            'email' =>
            'required|unique:App\Models\Applicantdata,email,' . $this->id,
            'password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:password',
            'hp' => 'required|min:10',
            'telp' => 'required|min:9',
            'tempat_lahir' => 'required',
            'tgl_lahir' => 'date|before:today|required',
            'gender' => 'required',
            'status_pernikahan' => 'required',
            'golongan_darah' => 'required',
            'agama' => 'required',
            'etnis' => 'required',
            'nama_contact_darurat' => 'required',
            'contact_darurat_1' => 'required|min:10',
            'contact_darurat_2' => 'nullable|min:10',
            'jenis_identitas' => 'required',
            'no_identitas' => 'required',
            'alamat_identitas' => 'required',
            'alamat_tinggal_sekarang' => 'required',
            // 'files.*' =>  ['nullable',  new AllowedFileExtension, new FileSizeLimit(1024)]
            // 'files.*' =>  ['nullable',  new AllowedFileExtension]
            'files.*' => ['nullable', 'mimes:png,jpg,jpeg', new AllowedFileExtension],

        ];
    }

    public function updatedFiles()
    {
        $this->validate([
            // 'files.*' => ['nullable', 'mimes:png,jpg,jpeg,pdf', new FileSizeLimit(1024)],
            'files.*' => ['nullable', 'mimes:png,jpg,jpeg', new AllowedFileExtension],
        ]);
    }

    public function save()
    {
        $validated = $this->validate();
        $this->is_update = true;
        $this->applicant_id = makeApplicationId($this->nama, $this->tgl_lahir);
        // if ($this->files != null) {
        if ($this->files) {
            foreach ($this->files as $file) {
                $folder = 'Applicants/' . $this->applicant_id;
                $fileExension = $file->getClientOriginalExtension();

                if ($fileExension != 'pdf') {
                    $folder = 'Applicants/' . $this->applicant_id . '/' . random_int(100000, 900000) . '.' . $fileExension;
                    $manager = ImageManager::gd();

                    // resize gif image
                    $image = $manager
                        ->read($file)
                        ->scale(width: 800);

                    // $imagedata = (string) $image->toJpeg();
                    $imagedata = (string) $image->toWebp(60);

                    // Storage::disk('google')->put($folder, $imagedata);
                    Storage::disk('public')->put($folder, $imagedata);
                    $this->path = $folder;
                } else {
                    // $this->path = Storage::disk('google')->put($folder, $file);
                    $this->path = Storage::disk('public')->put($folder, $file);
                }

                $this->originalFilename = $file->getClientOriginalName();
                Applicantfile::create([
                    'id_karyawan' => $this->applicant_id,
                    // 'originalName' => $this->originalFilename,
                    'originalName' => clear_dot($this->originalFilename, $fileExension),
                    'filename' => $this->path,
                ]);
            }
            $this->files = '';
            // return response()->json(['success' => true]);
        }
        Applicantdata::create([
            'applicant_id' => $this->applicant_id,
            'nama' => titleCase(trim($this->nama)),
            'email' => $this->email,
            'password' => $this->password,
            'hp' => $this->hp,
            'telp' => $this->telp,
            'tempat_lahir' => titleCase($this->tempat_lahir),
            'tgl_lahir' => $this->tgl_lahir,
            'gender' => $this->gender,
            'status_pernikahan' => $this->status_pernikahan,
            'golongan_darah' => $this->golongan_darah,
            'agama' => $this->agama,
            'etnis' => $this->etnis,
            'nama_contact_darurat' => titleCase($this->nama_contact_darurat),
            'contact_darurat_1' => $this->contact_darurat_1,
            'contact_darurat_2' => $this->contact_darurat_2,
            'jenis_identitas' => $this->jenis_identitas,
            'no_identitas' => $this->no_identitas,
            'alamat_identitas' => titleCase($this->alamat_identitas),
            'alamat_tinggal_sekarang' => titleCase($this->alamat_tinggal_sekarang),
            'status' => 1
        ]);

        $currentApplicantdata = Applicantdata::where('applicant_id', $this->applicant_id)->first();
        $this->id = $currentApplicantdata->id;


        // $this->dispatch('success', message: 'Data Anda sudah berhasil di submit');
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Data Anda sudah berhasil di submit',
        );

        $this->files = [];
    }
    public function update()
    {
        $validated = $this->validate();

        // update data

        $data = Applicantdata::where('applicant_id', $this->applicant_id)->first();
        if ($this->files) {
            foreach ($this->files as $file) {
                $folder = 'Applicants/' . $data->applicant_id;
                $fileExension = $file->getClientOriginalExtension();
                // $folder = 'Applicants/' . $this->applicant_id;

                if ($fileExension != 'pdf') {
                    // $folder = 'Applicants/' . $this->applicant_id . '/' . time() . '.' . $fileExension;
                    // $folder = 'Applicants/' . $data->applicant_id . '/' . time() . '.' . $fileExension;
                    $folder = 'Applicants/' . $data->applicant_id . '/' . random_int(1000, 9000) . '.' . $fileExension;

                    $manager = ImageManager::gd();

                    // resize gif image
                    $image = $manager
                        ->read($file)
                        ->scale(width: 800);
                    // $imagedata = (string) $image->toJpeg();
                    $imagedata = (string) $image->toWebp(60);

                    // Storage::disk('google')->put($folder, $imagedata);
                    Storage::disk('public')->put($folder, $imagedata);
                    $this->path = $folder;
                } else {
                    // $this->path = Storage::disk('google')->put($folder, $file);
                    $this->path = Storage::disk('public')->put($folder, $file);
                }

                $this->originalFilename = $file->getClientOriginalName();
                Applicantfile::create([
                    'id_karyawan' => $this->applicant_id,
                    'originalName' => clear_dot($this->originalFilename, $fileExension),
                    'filename' => $this->path,
                ]);
            }
        }


        $data->applicant_id = $this->applicant_id;
        $data->nama = titleCase($this->nama);
        $data->email = $this->email;
        $data->password = $this->password;
        $data->hp = $this->hp;
        $data->telp = $this->telp;
        $data->tempat_lahir = titleCase($this->tempat_lahir);
        $data->tgl_lahir = $this->tgl_lahir;
        $data->gender = $this->gender;
        $data->status_pernikahan = $this->status_pernikahan;
        $data->golongan_darah = $this->golongan_darah;
        $data->agama = $this->agama;
        $data->etnis = $this->etnis;
        $data->nama_contact_darurat = titleCase($this->nama_contact_darurat);
        $data->contact_darurat_1 = $this->contact_darurat_1;
        $data->contact_darurat_2 = $this->contact_darurat_2;
        $data->jenis_identitas = $this->jenis_identitas;
        $data->no_identitas = $this->no_identitas;
        $data->alamat_identitas = titleCase($this->alamat_identitas);
        $data->alamat_tinggal_sekarang = titleCase($this->alamat_tinggal_sekarang);
        $data->save();

        // $this->dispatch('success', message: 'Data Anda sudah berhasil di update');
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Data Anda sudah berhasil di update',
        );
        $this->files = [];
    }



    public function mount()
    {
        $this->is_registered = false;
        $this->show = false;
        $this->showMenu = true;
        $this->is_update = false;
        $this->showSubmit = false;
        $this->toggle_eye_password = false;
    }

    public function alreadyRegistered()
    {

        $this->is_registered = true;
        $this->showMenu = false;
        $this->showSubmit = true;
    }
    public function register()
    {
        $this->is_registered = false;
        $this->show = true;
        $this->showMenu = false;
    }

    public function keluar()
    {
        $this->reset();
        $this->is_registered = false;
        $this->show = false;
        $this->showMenu = true;
    }



    public function updatedIsRegistered()
    {
    }
    public function render()
    {
        $file_data = Applicantfile::where('id_karyawan', $this->applicant_id)->get();
        $this->filenames = $file_data;
        return view('livewire.applicant')->layout('layouts.newpolos');
    }
}
