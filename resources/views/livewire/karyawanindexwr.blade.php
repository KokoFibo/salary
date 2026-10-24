<div>
    @section('title', 'Karyawan')

    @if (auth()->user()->role == 5 || auth()->user()->role == 6)
        <div x-data="{
            search: $persist(@entangle('search').live),
            columnName: $persist(@entangle('columnName').live),
            direction: $persist(@entangle('direction').live),
            selectStatus: $persist(@entangle('selectStatus').live),
            perpage: $persist(@entangle('perpage').live),
            page: $persist(@entangle('paginators.page').live),
        }">
        </div>
    @endif

    <style>
        /* Ukuran font khusus isi tabel */
        .custom-table th,
        .custom-table td {
            white-space: nowrap;
            vertical-align: middle;
            font-size: 0.8125rem;
            /* ~13px */
            padding: 0.4rem 0.5rem;
            line-height: 1.2;
        }

        /* Form filter & dropdown select kecil dengan padding kanan cukup */
        .custom-table .form-control-sm,
        .custom-table .form-select-sm {
            font-size: 0.775rem;
            padding: 0.25rem 1.5rem 0.25rem 0.4rem;
        }

        .sortable-header {
            cursor: pointer;
            user-select: none;
            transition: background-color 0.2s;
        }

        .sortable-header:hover {
            background-color: rgba(0, 0, 0, 0.05) !important;
        }

        /* Container & Sticky Pagination */
        .table-responsive-container {
            max-height: calc(100vh - 280px);
            overflow-y: auto;
            position: relative;
        }

        .sticky-pagination-footer {
            position: sticky;
            bottom: 0;
            z-index: 20;
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Styling jarak antara Teks Total Records dan Button Pagination */
        .sticky-pagination-footer .pagination-wrapper {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            gap: 2rem !important;
            /* Jarak renggang tegas antara teks dan angka */
        }

        /* Memaksa elemen internal nav Laravel/Livewire agar terpisah jauh */
        .sticky-pagination-footer nav {
            margin-left: auto !important;
            display: flex !important;
            align-items: center !important;
        }

        .sticky-pagination-footer .pagination {
            margin-bottom: 0 !important;
            gap: 4px !important;
        }

        .sticky-pagination-footer .page-link {
            padding: 0.25rem 0.55rem !important;
            font-size: 0.775rem !important;
            border-radius: 0.25rem !important;
            color: #0d6efd !important;
        }

        .sticky-pagination-footer .page-item.active .page-link {
            background-color: #00bfff !important;
            /* Warna cyan menyala */
            border-color: #00bfff !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        .sticky-pagination-footer small,
        .sticky-pagination-footer p,
        .sticky-pagination-footer span {
            font-size: 0.8rem !important;
            margin-bottom: 0 !important;
        }

        .btn-action-xs {
            padding: 0.15rem 0.35rem;
            font-size: 0.75rem;
            line-height: 1;
            border-radius: 0.2rem;
        }

        /* --- STICKY COLUMNS SETUP --- */
        .sticky-col-1 {
            position: sticky;
            left: 0;
            width: 100px;
            min-width: 100px;
            max-width: 100px;
            z-index: 10;
            background-color: #ffffff !important;
        }

        .sticky-col-2 {
            position: sticky;
            left: 100px;
            width: 110px;
            min-width: 110px;
            z-index: 10;
            background-color: #ffffff !important;
        }

        .sticky-col-3 {
            position: sticky;
            left: 210px;
            width: 180px;
            min-width: 180px;
            z-index: 10;
            background-color: #ffffff !important;
            box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.12);
        }

        /* Z-Index khusus untuk Header Sticky */
        thead tr:first-child th.sticky-col-1,
        thead tr:first-child th.sticky-col-2,
        thead tr:first-child th.sticky-col-3 {
            z-index: 16 !important;
            background-color: #f8f9fa !important;
        }

        thead tr:last-child th.sticky-col-1,
        thead tr:last-child th.sticky-col-2,
        thead tr:last-child th.sticky-col-3 {
            z-index: 15 !important;
            background-color: #f8f9fa !important;
        }

        /* Zebra striping khusus sticky columns */
        .table-striped>tbody>tr:nth-of-type(odd)>.sticky-col-1,
        .table-striped>tbody>tr:nth-of-type(odd)>.sticky-col-2,
        .table-striped>tbody>tr:nth-of-type(odd)>.sticky-col-3 {
            background-color: #f9f9f9 !important;
        }

        /* Hover efek pada sticky column */
        .table-hover>tbody>tr:hover>.sticky-col-1,
        .table-hover>tbody>tr:hover>.sticky-col-2,
        .table-hover>tbody>tr:hover>.sticky-col-3 {
            background-color: #ecefe5 !important;
        }
    </style>

    <div class="px-4 mt-3">
        <div class="card shadow-sm border-0 rounded-3">
            {{-- Header Panel --}}
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="fw-bold fs-4 mb-0 text-dark">{{ __('Data Karyawan') }}</h3>
                        @if (isDataUtamaLengkap() > 0)
                            <a href="/datatidaklengkap" class="btn btn-outline-danger btn-sm rounded-2 ms-2">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Data Utama Tidak Lengkap
                            </a>
                        @endif
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                        <div style="min-width: 140px;">
                            <select class="form-select form-select-sm" wire:model.live="perpage">
                                <option value="10">10 {{ __('rows perpage') }}</option>
                                <option value="15">15 {{ __('rows perpage') }}</option>
                                <option value="20">20 {{ __('rows perpage') }}</option>
                                <option value="25">25 {{ __('rows perpage') }}</option>
                            </select>
                        </div>

                        <div style="min-width: 150px;">
                            <select wire:model.live="selectStatus" class="form-select form-select-sm">
                                <option value="0">{{ __('All Status') }}</option>
                                <option value="1">{{ __('Aktif') }}</option>
                                <option value="4">{{ __('Dirumahkan') }}</option>
                                <option value="2">{{ __('Resigned') }}</option>
                                <option value="3">{{ __('Blacklist') }}</option>
                            </select>
                        </div>

                        <button wire:click="reset_filter" class="btn btn-light btn-sm border text-secondary">
                            <i class="fa-solid fa-rotate me-1"></i> {{ __('Refresh') }}
                        </button>

                        <a href="/karyawancreate" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus me-1"></i> {{ __('Karyawan Baru') }}
                        </a>
                    </div>
                </div>

                {{-- Action Bar Extra --}}
                <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                    @if (auth()->user()->role >= 6)
                        <button wire:loading.remove wire:click="excel" class="btn btn-outline-success btn-sm">
                            <i class="fa-solid fa-file-excel me-1"></i> Excel
                        </button>
                        <a href="/template-gaji-form" class="btn btn-outline-success btn-sm">
                            <i class="fa-solid fa-download me-1"></i> Excel Template Department
                        </a>
                        <a href="/template-gaji-form-placement" class="btn btn-outline-success btn-sm">
                            <i class="fa-solid fa-download me-1"></i> Excel Template Directorate
                        </a>
                    @endif

                    @if (auth()->user()->role >= 6 || auth()->user()->username == 1146)
                        <a href="/bulk-upload-salary-adjust" class="btn btn-outline-primary btn-sm nightowl-daylight">
                            <i class="fa-solid fa-upload me-1"></i> {{ __('Bulk Update Salary Adjust') }}
                        </a>
                    @endif

                    @if (auth()->user()->role == 8)
                        <button wire:loading.remove wire:click="excelForm" class="btn btn-outline-success btn-sm">
                            <i class="fa-solid fa-file-excel me-1"></i> Excel Form
                        </button>
                        <a href="/usernotfound" class="btn btn-outline-secondary btn-sm">User Not Found</a>
                    @endif

                    @if (auth()->user()->role >= 8)
                        <a href="/movedata" class="btn btn-outline-warning btn-sm">Move Data From Other Database</a>
                    @endif

                    <a href="/infokaryawan" class="btn btn-outline-info btn-sm">
                        <i class="fa-solid fa-circle-info me-1"></i> Informasi
                    </a>

                    @if (auth()->user()->role >= 6)
                        <a href="/changeid" class="btn btn-outline-warning btn-sm">Ganti ID</a>
                        @if (auth()->user()->role == 8)
                            <button wire:loading.remove wire:click="excelKhusus" class="btn btn-outline-success btn-sm">
                                Excel Khusus Lama
                            </button>
                        @endif
                    @endif

                    @if (auth()->user()->role >= 7)
                        <a href="/cek-kenaikan-gaji" class="btn btn-outline-primary btn-sm">
                            Cek Perkembangan Gaji
                        </a>
                    @endif

                    @if ($is_tanggal_gajian || auth()->user()->role == 5)
                        <a href="/iuranlocker" class="{{ is_data_locked() ? 'd-none' : '' }}">
                            <button class="btn btn-outline-danger btn-sm">Hapus Iuran Locker</button>
                        </a>
                    @endif

                    @if (auth()->user()->role >= 5)
                        <a href="/excelkhusus">
                            <button type="button" class="btn btn-outline-success btn-sm">Excel Khusus</button>
                        </a>
                    @endif

                    <div wire:loading wire:target="excel"
                        class="spinner-border spinner-border-sm text-primary align-self-center ms-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive table-responsive-container">
                    <table class="table table-hover table-striped align-middle custom-table mb-0">
                        <thead class="table-light sticky-top shadow-sm">
                            {{-- Row Filter Inputs --}}
                            <tr class="bg-light">
                                <th class="sticky-col-1"></th>
                                <th class="sticky-col-2">
                                    <input wire:model.live="search_id_karyawan" type="text"
                                        class="form-control form-control-sm" placeholder="{{ __('ID Karyawan') }}">
                                </th>
                                <th class="sticky-col-3">
                                    <input wire:model.live="search_nama" type="text"
                                        class="form-control form-control-sm" placeholder="{{ __('Nama Karyawan') }}">
                                </th>
                                <th></th>
                                <th style="width: 150px; min-width: 150px;">
                                    <select wire:model.live="search_company" class="form-select form-select-sm">
                                        <option value="">{{ __('Company') }}</option>
                                        @foreach ($companies as $j)
                                            <option value="{{ $j }}">{{ nama_company($j) }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th style="width: 150px; min-width: 150px;">
                                    <select wire:model.live="search_placement" class="form-select form-select-sm">
                                        <option value="">{{ __('Directorate') }}</option>
                                        @foreach ($placements as $j)
                                            <option value="{{ $j }}">{{ nama_placement($j) }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th style="width: 150px; min-width: 150px;">
                                    <select wire:model.live="search_department" class="form-select form-select-sm">
                                        <option value="">{{ __('Department') }}</option>
                                        @foreach ($departments as $j)
                                            <option value="{{ $j }}">{{ nama_department($j) }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th style="width: 150px; min-width: 150px;">
                                    <select wire:model.live="search_jabatan" class="form-select form-select-sm">
                                        <option value="">{{ __('Jabatan') }}</option>
                                        @foreach ($jabatans as $j)
                                            <option value="{{ $j }}">{{ nama_jabatan($j) }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                @if (auth()->user()->role >= 7)
                                    <th style="width: 140px; min-width: 140px;">
                                        <select wire:model.live="search_etnis" class="form-select form-select-sm">
                                            <option value="">{{ __('Etnis') }}</option>
                                            @foreach ($etnises as $j)
                                                @if ($j == '')
                                                    <option value="kosong">{{ __('Masih Kosong') }}</option>
                                                @else
                                                    <option value="{{ $j }}">{{ $j }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </th>
                                @endif
                                <th colspan="20"></th>
                            </tr>

                            {{-- Row Sorting Headers --}}
                            <tr>
                                <th class="text-center sticky-col-1">#</th>
                                <th class="sortable-header sticky-col-2" wire:click="sortColumnName('id_karyawan')">
                                    {{ __('ID Karyawan') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="sortable-header sticky-col-3" wire:click="sortColumnName('nama')">
                                    {{ __('Nama') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="sortable-header" wire:click="sortColumnName('level_jabatan')">
                                    {{ __('Job Grade') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('company_id')">
                                    {{ __('Company') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('placement_id')">
                                    {{ __('Directorate') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('department_id')">
                                    {{ __('Department') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('jabatan_id')">
                                    {{ __('Jabatan') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                @if (Auth::user()->role > 6)
                                    <th class="text-center sortable-header" wire:click="sortColumnName('etnis')">
                                        {{ __('Etnis') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                    </th>
                                @endif
                                <th class="text-center sortable-header"
                                    wire:click="sortColumnName('status_karyawan')">
                                    {{ __('Status') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                @if (Auth::user()->role > 5)
                                    <th class="text-center sortable-header"
                                        wire:click="sortColumnName('tanggal_bergabung')">
                                        {{ __('Lama Bekerja') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                    </th>
                                @endif
                                <th class="text-center sortable-header"
                                    wire:click="sortColumnName('metode_penggajian')">
                                    {{ __('Metode Penggajian') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('gaji_pokok')">
                                    {{ __('Gaji (G+TB+TH+TJ)') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('gaji_tetap')">
                                    {{ __('Gaji') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header"
                                    wire:click="sortColumnName('tunjangan_bahasa')">
                                    {{ __('Tunjangan Bahasa') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header"
                                    wire:click="sortColumnName('tunjangan_housing')">
                                    {{ __('Tunjangan Housing') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header"
                                    wire:click="sortColumnName('tunjangan_jabatan')">
                                    {{ __('Tunjangan Jabatan') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('gaji_bpjs')">
                                    {{ __('Gaji BPJS') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('gaji_overtime')">
                                    {{ __('Overtime') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('tanggal_update')">
                                    {{ __('Last Adjust') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('iuran_air')">
                                    {{ __('Iuran Air') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                <th class="text-center sortable-header" wire:click="sortColumnName('iuran_locker')">
                                    {{ __('Iuran Locker') }} <i class="fa-solid fa-sort text-muted ms-1"></i>
                                </th>
                                @if ($selectStatus == 2 && auth()->user()->role > 6)
                                    <th class="text-center">
                                        {{ __('Lama bekerja') }}
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $data)
                                <tr>
                                    <td class="sticky-col-1">
                                        <div class="d-flex align-items-center gap-1">
                                            <a href="/karyawanupdate/{{ $data->id }}"
                                                class="btn btn-success btn-action-xs {{ is_data_locked() ? 'd-none' : '' }}"
                                                title="Edit">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>

                                            @if ($selectStatus == 2 || $selectStatus == 3)
                                                <a href="/karyawanreinstate/{{ $data->id }}"
                                                    class="btn btn-warning btn-action-xs {{ is_data_locked() ? 'd-none' : '' }}"
                                                    title="Reinstate">
                                                    R
                                                </a>
                                            @endif

                                            @if (Auth::user()->role > 7)
                                                <button wire:click="delete(`{{ $data->id }}`)"
                                                    wire:confirm.prompt="Yakin mau di delete?\n\nKetik DELETE untuk konfirmasi|DELETE"
                                                    class="btn btn-danger btn-action-xs {{ is_data_locked() ? 'd-none' : '' }}"
                                                    title="Hapus">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                                <button wire:click="export(`{{ $data->id }}`)"
                                                    wire:confirm.prompt="Yakin mau di export?\n\nKetik EXPORT untuk konfirmasi|EXPORT"
                                                    class="btn btn-warning btn-action-xs {{ is_data_locked() ? 'd-none' : '' }}"
                                                    title="Export">
                                                    <i class="fa-solid fa-file-export"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-dark sticky-col-2">{{ $data->id_karyawan }}</td>
                                    <td class="sticky-col-3">{{ $data->nama }}</td>
                                    <td>{{ getGrade($data->level_jabatan) }}</td>
                                    <td class="text-center">{{ $data->company?->company_name ?? '-' }}</td>
                                    <td class="text-center">{{ $data->placement?->placement_name ?? '-' }}</td>
                                    <td class="text-center">{{ $data->department?->nama_department ?? '-' }}</td>
                                    <td class="text-center">{{ $data->jabatan?->nama_jabatan ?? '-' }}</td>

                                    @if (Auth::user()->role > 6)
                                        <td class="text-center">{{ $data->etnis }}</td>
                                    @endif

                                    <td class="text-center">
                                        <span
                                            class="badge bg-light text-dark border fw-normal py-1 px-2">{{ $data->status_karyawan }}</span>
                                    </td>

                                    @if (
                                        (auth()->user()->username == 1146 && $data->gaji_pokok <= 8000000) ||
                                            (auth()->user()->role == 5 && $data->gaji_pokok <= 4500000) ||
                                            auth()->user()->role >= 6)
                                        @if (Auth::user()->role > 5)
                                            @if ($data->status_karyawan == 'Resigned')
                                                <td class="text-center">
                                                    {{ lamaBekerjaBlacklistResigned($data->tanggal_bergabung, $data->tanggal_resigned) }}
                                                </td>
                                            @elseif($data->status_karyawan == 'Blacklist')
                                                <td class="text-center">
                                                    {{ lamaBekerjaBlacklistResigned($data->tanggal_bergabung, $data->tanggal_blacklist) }}
                                                </td>
                                            @else
                                                <td class="text-center">{{ lamaBekerja($data->tanggal_bergabung) }}
                                                </td>
                                            @endif
                                        @endif
                                        <td class="text-center">{{ $data->metode_penggajian }}</td>
                                        <td class="text-center fw-semibold">{{ number_format($data->gaji_pokok) }}
                                        </td>
                                        <td class="text-center">{{ number_format($data->gaji_tetap) }}</td>
                                        <td class="text-center">{{ number_format($data->tunjangan_bahasa) }}</td>
                                        <td class="text-center">{{ number_format($data->tunjangan_housing) }}</td>
                                        <td class="text-center">{{ number_format($data->tunjangan_jabatan) }}</td>
                                        <td class="text-center">{{ number_format($data->gaji_bpjs) }}</td>
                                        <td class="text-center">{{ number_format($data->gaji_overtime) }}</td>
                                        <td class="text-center">{{ format_tgl($data->tanggal_update) }}</td>
                                        <td class="text-center">{{ number_format($data->iuran_air) }}</td>
                                        <td class="text-center">{{ number_format($data->iuran_locker) }}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif

                                    @if ($selectStatus == 5 && auth()->user()->role > 6)
                                        <td class="text-center">
                                            {{ lama_resign($data->tanggal_bergabung, $data->tanggal_resigned, $data->tanggal_blacklist) }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Sticky Pagination Footer --}}
            <div class="card-footer sticky-pagination-footer py-2 px-3">
                <div class="pagination-wrapper d-flex justify-content-between align-items-center w-100">
                    <div class="text-muted pe-3">
                        Total records found : <strong>{{ $datas->total() }}</strong> data
                    </div>
                    <div class="ms-auto">
                        {{ $datas->onEachSide(0)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            document.addEventListener("reinstate", (event) => {
                const data = event.detail;
                Swal.fire({
                    position: "center",
                    icon: data.type,
                    title: data.title,
                    showConfirmButton: false,
                    timer: 1500,
                });
            });
        </script>
    @endscript
</div>
