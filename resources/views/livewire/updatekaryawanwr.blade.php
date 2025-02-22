<div>
    @section('title', 'Update Karyawan')
    <div class="container ">

        <div class="card mt-3 ">
            <div class="card-header bg-secondary">
                <h5 class="text-light py-2">{{ __('Update Data Karyawan') }}</h5>
            </div>

            <div class="card-body">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab">
                        <button class="nav-link active" id="nav-pribadi-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-pribadi" type="button" role="tab" aria-controls="nav-pribadi"
                            aria-selected="false"><span class="fs-5">{{ __('Data Pribadi') }}</span></button>
                        <button class="nav-link" id="nav-identitas-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-identitas" type="button" role="tab" aria-controls="nav-identitas"
                            aria-selected="false"><span class="fs-5">{{ __('Identitas') }}</span></button>
                        <button class="nav-link " id="nav-kepegawaian-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-kepegawaian" type="button" role="tab"
                            aria-controls="nav-kepegawaian" aria-selected="true"><span
                                class="fs-5">{{ __('Data Kepegawaian') }}</span></button>


                        {{-- baris dibawah ini jangan dihapus --}}
                        <button class="nav-link " id="nav-payroll-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-payroll" type="button" role="tab" aria-controls="nav-payroll"
                            aria-selected="false"><span class="fs-5">{{ __('Payroll') }}</span></button>
                        {{-- @endif --}}

                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">

                    <div class="tab-pane fade show active p-3" id="nav-pribadi" role="tabpanel"
                        aria-labelledby="nav-pribadi-tab">
                        @include('pribadi')
                    </div>

                    <div class="tab-pane fade p-3" id="nav-identitas" role="tabpanel"
                        aria-labelledby="nav-identitas-tab">
                        @include('identitas')
                    </div>

                    <div class="tab-pane fade p-3" id="nav-kepegawaian" role="tabpanel"
                        aria-labelledby="nav-kepegawaian-tab">
                        @include('kepegawaian')
                    </div>



                    {{-- baris dibawah ini jangan dihapus --}}
                    <div class="tab-pane fade p-3" id="nav-payroll" role="tabpanel" aria-labelledby="nav-payroll-tab">
                        @include('payroll')
                    </div>

                    {{-- @endif --}}


                </div>


                <div class="card m-3">
                    <div class="card-header">

                        <h4 class='pt-3'>Upload Dokumen</h4>
                        <p>Hanya menerima file png, jpg dan jpeg saja</p>
                    </div>
                    <div class="card-body">



                        {{-- upload files  --}}
                        <div class="p-3 row row-cols-2 row-cols-md-4 g-4 mb-4">
                            <div class="col">
                                <label class="form-label" for="upload_ktp">
                                    <p>KTP <span class="text-danger">*</span></p>
                                </label>
                                <input wire:model='ktp' multiple class="form-control" id="upload_ktp" type="file">
                                @error('ktp.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_kk">
                                    <p>Kartu Keluarga <span class="text-danger">*</span></p>
                                </label>
                                <input wire:model='kk' multiple class="form-control" id="upload_kk" type="file">
                                @error('kk.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_ijazah">
                                    <p>Ijazah <span class="text-danger">*</span></p>
                                </label>
                                <input wire:model='ijazah' multiple class="form-control" id="upload_ijazah"
                                    type="file">
                                @error('ijazah.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_nilai">
                                    <p>Transkip Nilai/SKHUN <span class="text-danger">*</span></p>
                                </label>
                                <input wire:model='nilai' multiple class="form-control" id="upload_nilai"
                                    type="file">
                                @error('nilai.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_cv">
                                    <p>CV <span class="text-danger">*</span></p>
                                </label>
                                <input wire:model='cv' multiple class="form-control" id="upload_cv" type="file">
                                @error('cv.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_pasfoto">
                                    <p>Pass Foto <span class="text-danger">*</span></p>
                                </label>
                                <input wire:model='pasfoto' multiple class="form-control" id="upload_pasfoto"
                                    type="file">
                                @error('pasfoto.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_npwp">
                                    <p>NPWP</p>
                                </label>
                                <input wire:model='npwp' multiple class="form-control" id="upload_npwp"
                                    type="file">
                                @error('npwp.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_paklaring">
                                    <p>Paklaring</p>
                                </label>
                                <input wire:model='paklaring' multiple class="form-control" id="upload_paklaring"
                                    type="file">
                                @error('paklaring.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_bpjs">
                                    <p>Kartu BPJS Ketenagakerjaan</p>
                                </label>
                                <input wire:model='bpjs' multiple class="form-control" id="upload_bpjs"
                                    type="file">
                                @error('bpjs.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_skck">
                                    <p>SKCK</p>
                                </label>
                                <input wire:model='skck' multiple class="form-control" id="upload_skck"
                                    type="file">
                                @error('skck.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_sertifikat">
                                    <p>Sertifikat</p>
                                </label>
                                <input wire:model='sertifikat' multiple class="form-control" id="upload_sertifikat"
                                    type="file">
                                @error('sertifikat.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label" for="upload_bri">
                                    <p>Buku Tabungan Bank BRI</p>
                                </label>
                                <input wire:model='bri' multiple class="form-control" id="upload_bri"
                                    type="file">
                                @error('bri.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>




                {{-- Show Errors --}}
                <ul>
                    @foreach ($errors->all() as $error)
                        <li><span class='text-danger'>{{ $error }}</span></li>
                    @endforeach
                </ul>


                <div wire:loading wire:target='update1'>
                    <div class="text-center">
                        <h5>Mohon tunggu sampai proses update selesai</h5>
                        <div class="spinner-border text-dark mt-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 pb-3 px-3" wire:loading.remove>
                    @if ($status_karyawan != 'Resigned' && $status_karyawan != 'Blacklist')
                        <button wire:click="update1" class="btn btn-primary mx-3">{{ __('Update') }}</button>
                    @endif

                    <button wire:click="exit" class="btn btn-dark mx-3">{{ __('Exit') }}</button>
                    @if (!$show_arsip)
                        @if (!$is_folder_kosong)
                            <button wire:click="arsip"
                                class="btn btn-success mx-3">{{ __('Lihat File Dokumen') }}</button>
                        @else
                            <button class="btn btn-success mx-3" disabled>Belum ada file dokumen</button>
                        @endif
                    @else
                        @if (!$is_folder_kosong)
                            <button class="btn btn-success" wire:click='tutup_arsip'>Tutup File Dokumen</button>
                        @endif
                    @endif
                    @if (!$is_folder_kosong)
                        ;
                        <a href="{{ route('download.zip', ['folder' => $folder_name]) }}"
                            class="btn btn-primary">Download
                            All</a>
                        <a href="{{ route('download.merged.pdf', ['folder' => $folder_name]) }}"
                            class="btn btn-danger">Download PDF</a>
                    @endif

                </div>
            </div>


            @if ($show_arsip)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3>File Arsip {{ $nama }}</h3>
                    </div>
                    <div class="card-body">
                        @if ($personal_files->isNotEmpty())


                            @foreach ($personal_files as $fn)
                                @if (strtolower(getFilenameExtension($fn->originalName)) == 'pdf')
                                    {{-- <li class="list-group-item "> --}}
                                    <div class="d-flex flex-row justify-content-between align-items-center">
                                        {{-- <h4> {{ $fn->originalName }}</h4> --}}
                                        <h4> {{ get_filename($fn->filename) }}</h4>
                                        <button class="btn btn-danger" wire:confirm='Yakin mau di delete?'
                                            wire:click="deleteFile('{{ $fn->id }}')">Remove</button>
                                    </div>
                                    <div>
                                        <iframe class="mt-1 mb-3 rounded-4" src="{{ getUrl($fn->filename) }}"
                                            width="100%" height="600px"></iframe>
                                    </div>

                                    {{-- </li> --}}
                                @endif
                            @endforeach
                            @foreach ($personal_files as $key => $fn)
                                @if (strtolower(getFilenameExtension($fn->originalName)) != 'pdf')
                                    {{-- <li class="list-group-item"> --}}
                                    <div class="d-flex flex-column-reverse flex-lg-row   ">
                                        <div class="flex flex-col">
                                            <div class="responsive-container">

                                                <div
                                                    class="d-flex flex-row justify-content-between align-items-center mt-3 px-2">
                                                    {{-- <h4> {{ $fn->originalName }}</h4> --}}
                                                    <h4> {{ get_filename($fn->filename) }} </h4>
                                                    <div>
                                                        <button class="btn btn-danger"
                                                            wire:confirm='Yakin mau di delete?'
                                                            wire:click="deleteFile('{{ $fn->id }}')">Remove</button>

                                                    </div>


                                                </div>

                                                <img class="mt-1 mb-3 rounded-4" src="{{ getUrl($fn->filename) }}"
                                                    alt="">
                                            </div>
                                        </div>

                                    </div>
                                    {{-- </li> --}}
                                @endif
                            @endforeach
                            <button class="btn btn-success" wire:click='tutup_arsip'>Tutup File Dokumen</button>
                        @else
                            <h3>File tidak ditemukan</h3>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    <style>
        /* Container for the responsive image */
        .responsive-container {
            width: 100%;
            max-width: 800px;
            /* Optional: Set a max-width for the container */
            margin: 0 auto;
            /* Center the container */
        }

        /* Make the image responsive */
        .responsive-container img {
            width: 100%;
            height: auto;
            display: block;
            /* Remove any extra space below the image */
        }
    </style>
    @script
        <script>
            window.addEventListener("show-delete-confirmation", (event) => {
                Swal.fire({
                    title: "Yakin mau delete data?",
                    // text: "You won't be able to revert this!",
                    text: event.detail.text,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, delete",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.dispatch("delete-confirmed");
                    }
                });
            });
        </script>
    @endscript
</div>
