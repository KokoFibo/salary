<div>
    @section('title', 'Payroll')
    {{-- <p>lock_presensi: {{ $lock_presensi }}</p> --}}
    <style>
        td,
        th {
            white-space: nowrap;
        }

        @media (min-width : 600px) {

            table th {
                z-index: 2;
            }

            td:first-child,
            th:first-child {
                position: sticky;
                left: 0;
                z-index: 1;
            }

            td:nth-child(2),
            th:nth-child(2) {
                position: sticky;
                left: 56px;
                z-index: 1;
            }

            td:nth-child(3),
            th:nth-child(3) {
                position: sticky;
                left: 110px;
                z-index: 1;
            }

            td:nth-child(4),
            th:nth-child(4) {

                position: sticky;
                left: 200px;
                z-index: 1;
            }

            th:first-child,
            th:nth-child(2) {
                z-index: 3;
            }
        }
    </style>
    <div class="p-2">
        {{-- <p>selected_company : {{ $selected_company }}</p>
        <p>selected_placement : {{ $selected_placement }}</p>
        <p>selected_departemen : {{ $selected_departemen }}</p> --}}
        {{-- <p>working days = {{ countWorkingDays($month, $year, [0]) }}, Holidays =
            {{ jumlah_libur_nasional($month, $year) }}</p> --}}
        {{-- @if (auth()->user()->role == 8) --}}
        {{-- <p>$data_bulan_ini ; {{ $data_bulan_ini }}</p> --}}
        {{-- <p>Month : {{ $month }}</p>
        <p>Year : {{ $year }}</p> --}}

        @if (check_rebuild_done())
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Congratulation!</strong> Payroll Rebuilt Succesfully.
                <button wire:click='close_succesful_rebuilt' type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
        @endif
        @if (check_rebuilding())
            <div class="alert alert-primary" role="alert">
                <strong>Payroll is rebuilding ...</strong> You may safely leave this page.
            </div>
        @endif
        @if ($fail = check_fail_job())
            <div class="alert alert-danger" role="alert">
                <strong>Errror building payroll</strong>
            </div>
        @endif
        {{-- @endif --}}

        <div class="row mb-2 d-flex flex-column flex-lg-row px-4 p-2">
            <div class="col">
                @if (auth()->user()->role >= 7)
                    <div class="form-check form-switch">
                        <input wire:model.live="lock_slip_gaji" class="form-check-input" type="checkbox" role="switch"
                            id="flexSwitchCheckChecked" value=1 {{ $lock_slip_gaji ? 'checked' : '' }}>
                        <label class="form-check-label" for="flexSwitchCheckChecked">
                            @if ($lock_slip_gaji)
                                {{ __('Slip Gaji is locked') }}
                            @else
                                {{ __('Slip Gaji is unlocked') }}
                            @endif
                        </label>
                    </div>
                @endif

            </div>
            <div class="col">
                <h4 class="text-center text-bold ">{{ __('Yifang Payroll') }}</h4>
            </div>
            <div class="col">
                <div class="d-flex gap-2 flex-column flex-xl-row gap-xl-5 align-items-center justify-content-end">
                    @if (auth()->user()->role > 6)
                        <div class="form-check form-switch">
                            <input wire:model.live="lock_data" class="form-check-input" type="checkbox" role="switch"
                                id="flexSwitchCheckChecked" value=1 {{ $lock_data ? 'checked' : '' }}>
                            <label class="form-check-label" for="flexSwitchCheckChecked">
                                @if ($lock_data)
                                    {{ __('Data is locked') }}
                                @else
                                    {{ __('Data is unlocked') }}
                                @endif
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input wire:model.live="lock_presensi" class="form-check-input" type="checkbox"
                                role="switch" id="flexSwitchCheckChecked" value=1
                                {{ $lock_presensi ? 'checked' : '' }}>
                            <label class="form-check-label" for="flexSwitchCheckChecked">
                                {{-- {{ $lock_presensi ? 'Presensi is locked' : 'Presensi is unlocked' }} --}}
                                @if ($lock_presensi)
                                    {{ __('Presensi is locked') }}
                                @else
                                    {{ __('Presensi is unlocked') }}
                                @endif
                            </label>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @if (!check_rebuilding())
            {{-- =============== --}}
            <div class="px-3 mb-2">

                {{-- BARIS ATAS --}}
                <div class="row g-2 align-items-center mb-3">

                    {{-- Total Gaji --}}
                    <div class="col-12 col-xl-auto">
                        <button class="btn btn-info w-100 nightowl-daylight">
                            {{ __('Total Gaji') }} : Rp. {{ number_format($total) }}
                        </button>
                    </div>

                    {{-- Year --}}
                    <div class="col-6 col-md-3 col-xl-auto">
                        <select class="form-select" wire:model.live="year">
                            @foreach ($select_year as $sy)
                                <option value="{{ $sy }}">{{ $sy }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Month --}}
                    <div class="col-6 col-md-3 col-xl-auto d-flex gap-2">
                        <select class="form-select" wire:model.live="month">
                            @foreach ($select_month as $sm)
                                <option value="{{ $sm }}">{{ monthName($sm) }}</option>
                            @endforeach
                            <option value="1">Januari 2026</option>
                        </select>
                        @if (auth()->user()->role == 8)
                            <button wire:click="clear_lock" class="btn btn-primary w-100 nightowl-daylight">
                                {{ __('Clear Lock') }}
                            </button>
                        @endif
                    </div>
                    {{-- LOADING STATE --}}
                    <div class="col-12">
                        <button wire:loading wire:target='buat_payroll' class="btn btn-primary w-100" disabled>
                            <span class="spinner-border spinner-border-sm"></span>
                            {{ __('Building Data... ±3,5 menit, jangan tekan apapun.') }}
                        </button>

                        <button wire:loading wire:target='export' class="btn btn-primary w-100" disabled>
                            <span class="spinner-border spinner-border-sm"></span>
                            {{ __('Building Excel... Please wait') }}
                        </button>

                        <button wire:loading wire:target='bankexcel' class="btn btn-primary w-100" disabled>
                            <span class="spinner-border spinner-border-sm"></span>
                            {{ __('Building Excel for bank...') }}
                        </button>

                        <button wire:loading wire:target='excelDetailReport2' class="btn btn-primary w-100" disabled>
                            <span class="spinner-border spinner-border-sm"></span>
                            {{ __('Building detail report...') }}
                        </button>
                    </div>

                </div>

                {{-- BARIS TOMBOL AKSI --}}
                <div class="row g-2" wire:loading.class="invisible">

                    @if (auth()->user()->role == 8)
                        <div class="col-12 col-md-6 col-xl-auto">
                            <a href="/cekabsensitanpaid" class="d-grid">
                                <button class="btn btn-primary nightowl-daylight">
                                    {{ __('Cek Absensi Tanpa ID') }}
                                </button>
                            </a>
                        </div>

                        <div class="col-6 col-md-4 col-xl-auto">
                            <button wire:click="buat_payroll('noQueue')"
                                class="btn btn-primary w-100 nightowl-daylight">
                                {{ __('Rebuild tanpa queue') }}
                            </button>
                        </div>

                        <div class="col-6 col-md-4 col-xl-auto">
                            <button wire:click="buat_payroll_baru" class="btn btn-primary w-100 nightowl-daylight">
                                {{ __('Rebuild Baru') }}
                            </button>
                        </div>

                        <div class="col-6 col-md-4 col-xl-auto">
                            <button wire:click="rebuildOptimized" class="btn btn-primary w-100 nightowl-daylight">
                                {{ __('Quick Rebuild') }}
                            </button>
                        </div>
                    @endif

                    <div class="col-6 col-md-4 col-xl-auto">
                        <a href="/ter" class="d-grid">
                            <button class="btn btn-warning nightowl-daylight">
                                {{ __('Table TER PPh21') }}
                            </button>
                        </a>
                    </div>

                    <div class="col-6 col-md-4 col-xl-auto">
                        <button wire:click="bankexcel" class="btn btn-success w-100 nightowl-daylight">
                            {{ __('Report for Bank') }}
                        </button>
                    </div>

                    <div class="col-6 col-md-4 col-xl-auto">
                        <button wire:click="excelDetailReport2" class="btn btn-warning w-100 nightowl-daylight">
                            {{ __('Detail Report') }}
                        </button>
                    </div>

                    <div class="col-6 col-md-4 col-xl-auto">
                        <button wire:click="export" class="btn btn-success w-100 nightowl-daylight">
                            Excel
                        </button>
                    </div>

                    <div class="col-12 col-md-6 col-xl-auto">
                        <a href="/laporan-cost/{{ $year }}" class="d-grid">
                            <button class="btn btn-success nightowl-daylight">
                                Cost Report
                            </button>
                        </a>
                    </div>

                    <div class="col-12 col-md-6 col-xl-auto">
                        <button wire:click="buat_payroll('queue')"
                            {{ is_40_days($month, $year) || isDataUtamaLengkap() > 0 ? 'disabled' : '' }}
                            class="btn btn-primary w-100 nightowl-daylight">
                            {{ __('Rebuild') }}
                        </button>
                    </div>

                </div>
            </div>

            {{-- WARNING DATA --}}
            @if (isDataUtamaLengkap() > 0)
                <div class="d-flex flex-column align-items-center text-center m-3 gap-2">
                    <h5 class="text-danger fw-bold">
                        Ada beberapa data utama karyawan yang belum lengkap!
                    </h5>
                    <a href="/datatidaklengkap">
                        <button class="btn btn-danger">
                            Silakan cek di sini
                        </button>
                    </a>
                </div>
            @endif

            {{-- =============== --}}

        @endif

        <div class="card">
            <div class="card-header">
                <div class="row g-2 align-items-end">

                    {{-- Search --}}
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="input-group">
                            <button class="btn btn-primary" type="button">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <input type="search" wire:model.live="search" class="form-control"
                                placeholder="{{ __('Search') }} ...">
                        </div>
                    </div>

                    {{-- Placement --}}
                    <div class="col-12 col-md-6 col-xl">
                        <select wire:model.live="selected_placement" class="form-select">
                            <option value="0">{{ __('All Directorates') }}</option>
                            @foreach ($placements as $p)
                                <option value="{{ $p->id }}">{{ $p->placement_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Company --}}
                    <div class="col-12 col-md-6 col-xl">
                        <select wire:model.live="selected_company" class="form-select">
                            <option value="0">{{ __('All Companies') }}</option>
                            @foreach ($companies as $c)
                                <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Department --}}
                    <div class="col-12 col-md-6 col-xl">
                        <select wire:model.live="selected_departemen" class="form-select">
                            <option value="0">{{ __('All Department') }}</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_department }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-6 col-md-3 col-xl">
                        <select class="form-select" wire:model.live="status">
                            <option value="0">{{ __('Semua') }}</option>
                            <option value="1">{{ __('Status Aktif') }}</option>
                            <option value="2">{{ __('Status Non Aktif') }}</option>
                        </select>
                    </div>

                    {{-- Per Page --}}
                    <div class="col-6 col-md-3 col-xl">
                        <select class="form-select" wire:model.live="perpage">
                            <option value="10">10 {{ __('rows') }}</option>
                            <option value="15">15 {{ __('rows') }}</option>
                            <option value="20">20 {{ __('rows') }}</option>
                            <option value="25">25 {{ __('rows') }}</option>
                        </select>
                    </div>

                    {{-- Excel Button --}}
                    @if (auth()->user()->role >= 7)
                        <div class="col-12 col-md-6 col-xl-auto">
                            <a href="/kenaikangaji" class="d-grid">
                                <button class="btn btn-success">
                                    Excel by etnis
                                </button>
                            </a>
                        </div>
                    @endif

                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th wire:click="sortColumnName('id_karyawan')">{{ __('ID') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('id_karyawan')">
                                    {{ __('Date') }} <i class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('nama')">{{ __('Nama') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                @if (auth()->user()->role == 8)
                                    <th wire:click="sortColumnName('gaji_libur')">{{ __('test gaji_libur') }} <i
                                            class="fa-solid fa-sort"></i></th>
                                @endif
                                <th wire:click="sortColumnName('status_karyawan')">{{ __('Status') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('jabatan')">{{ __('Jabatan') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('placement')">{{ __('Directorate') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('company')">{{ __('Company') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('departemen')">{{ __('Department') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('metode_penggajian')">{{ __('Metode Penggajian') }}
                                    <i class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('hari_kerja')">{{ __('Hari Kerja') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('jam_kerja')">{{ __('Jam Kerja Bersih') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('jam_lembur')">{{ __('Jam Lembur') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('jumlah_jam_terlambat')">{{ __('Terlambat') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('gaji_pokok')">{{ __('Gaji Pokok') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('gaji_lembur')">{{ __('Gaji Lembur') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('gaji_bpjs')">{{ __('Gaji BPJS') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('subtotal')">{{ __('Sub Gaji') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('subtotal')">{{ __('Gaji Bulan Ini') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('gaji_libur')">{{ __('Gaji Libur') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>

                                {{-- <th wire:click="sortColumnName('libur_nasional')">{{ __('Libur Nasional') }} <i
                                        class="fa-solid fa-sort"></i> --}}
                                </th>
                                <th wire:click="sortColumnName('tambahan_shift_malam')">
                                    {{ __('Tambahan Shift Malam') }} <i class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('bonus1x')">{{ __('Bonus/U.Makan') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('bonus1x')">{{ __('Bonus Karyawan') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('potongan1x')">{{ __('Potongan 1X') }}<i
                                        class="fa-solid fa-sort"></i>
                                </th>

                                <th wire:click="sortColumnName('potongan1x')">{{ __('Potongan Karyawan') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>


                                <th wire:click="sortColumnName('denda_lupa_absen')">{{ __('Lupa Absen') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('denda_resigned')">{{ __('Denda Resigned') }} <i
                                        class="fa-solid fa-sort"></i>
                                </th>

                                <th wire:click="sortColumnName('pajak')">{{ __('Pajak') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('jht')">JHT <i class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('jp')">JP <i class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('jkk')">JKK <i class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('jkm')">JKM <i class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('kesehatan')">Kesehatan <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('tanggungan')">Tanggungan <i
                                        class="fa-solid fa-sort"></i>
                                </th>
                                <th wire:click="sortColumnName('ptkp')">{{ __('PTKP') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th>{{ __('TER') }}</th>

                                <th wire:click="sortColumnName('total_bpjs')">{{ __('Total BPJS') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('bpjs_adjustment')">{{ __('BPJS Adjustment') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('pph21')">{{ __('PPh21') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('prf_salary')">{{ __('PRF Salary') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('other_deduction')">{{ __('Other Deduction') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('bpjs_employee')">{{ __('BPJS Employee') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('prf')">{{ __('PRF') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('core_cash')">{{ __('Core Cash') }} <i
                                        class="fa-solid fa-sort"></i></th>
                                <th wire:click="sortColumnName('total')">{{ __('Total') }} <i
                                        class="fa-solid fa-sort"></i></th>

                            </tr>
                        </thead>
                        <tbody>
                            @if ($payroll->isNotEmpty())

                                @foreach ($payroll as $p)
                                    @if (check_bulan($p->date, $month, $year))
                                        <tr>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-success btn-sm nightowl-daylight"
                                                    wire:click="showDetail({{ $p->id_karyawan }})"
                                                    data-bs-toggle="modal" data-bs-target="#payroll"><i
                                                        class="fa-solid fa-magnifying-glass nightowl-daylight"></i></button>

                                            </td>


                                            <td>{{ $p->id_karyawan }}</td>
                                            {{-- <td>{{ format_tgl($p->date) }}</td> --}}
                                            <td>{{ month_year($p->date) }}</td>
                                            <td>{{ $p->nama }}</td>
                                            @if (auth()->user()->role == 8)
                                                <td>{{ number_format($p->gaji_libur) }}</td>
                                            @endif
                                            <td>{{ $p->status_karyawan }}</td>
                                            <td>{{ nama_jabatan($p->jabatan_id) }}</td>
                                            <td>{{ nama_placement($p->placement_id) }}</td>
                                            <td>{{ nama_company($p->company_id) }}</td>
                                            <td>{{ nama_department($p->department_id) }}</td>
                                            <td>{{ $p->metode_penggajian }}</td>
                                            <td class="text-end">{{ $p->hari_kerja }}</td>
                                            <td class="text-end">{{ number_format($p->jam_kerja, 1) }}</td>
                                            <td class="text-end">{{ $p->jam_lembur }}</td>
                                            <td class="text-end">{{ $p->jumlah_jam_terlambat }}</td>
                                            <td class="text-end">{{ number_format($p->gaji_pokok) }}</td>
                                            <td class="text-end">
                                                {{ $p->gaji_lembur ? number_format($p->gaji_lembur) : '' }}
                                            </td>
                                            <td class="text-end">
                                                {{ $p->gaji_bpjs ? number_format($p->gaji_bpjs) : '' }}
                                            </td>
                                            <td class="text-end">{{ number_format($p->subtotal) }}</td>
                                            <td class="text-end">{{ number_format($p->gaji_bulan_ini) }}</td>
                                            <td class="text-end">{{ number_format($p->gaji_libur) }}</td>
                                            {{-- <td class="text-end">
                                                {{ $p->libur_nasional ? number_format($p->libur_nasional) : '' }}
                                            </td> --}}
                                            <td class="text-end">
                                                {{ $p->tambahan_shift_malam ? number_format($p->tambahan_shift_malam) : '' }}
                                            </td>
                                            <td class="text-end">
                                                {{ $p->bonus1x ? number_format($p->bonus1x) : '' }}
                                            </td>
                                            @php
                                                $total_potongan_dari_karyawan = 0;
                                                $total_bonus_dari_karyawan = 0;
                                                $total_potongan_dari_karyawan = $p->iuran_air + $p->iuran_locker;
                                                $total_bonus_dari_karyawan =
                                                    $p->thr +
                                                    $p->tunjangan_jabatan +
                                                    $p->tunjangan_bahasa +
                                                    $p->tunjangan_skill +
                                                    $p->tunjangan_lembur_sabtu +
                                                    $p->tunjangan_lama_kerja;

                                            @endphp
                                            <td class="text-end">
                                                {{ number_format($total_bonus_dari_karyawan) }}
                                                {{-- {{ $total_bonus_dari_karyawan ? number_format($total_bonus_dari_karyawan) : '' }} --}}
                                            </td>
                                            <td class="text-end">
                                                {{ $p->potongan1x ? number_format($p->potongan1x) : '' }}
                                            </td>

                                            <td class="text-end">
                                                {{ $total_potongan_dari_karyawan ? number_format($total_potongan_dari_karyawan) : '' }}
                                            </td>
                                            <td class="text-end">
                                                {{ $p->denda_lupa_absen ? number_format($p->denda_lupa_absen) : '' }}
                                            </td>
                                            <td class="text-end">
                                                {{ $p->denda_resigned ? number_format($p->denda_resigned) : '' }}

                                            </td>

                                            <td class="text-end">{{ $p->pajak ? number_format($p->pajak) : '' }}
                                            </td>
                                            <td class="text-end">{{ $p->jht ? number_format($p->jht) : '' }}</td>
                                            <td class="text-end">{{ $p->jp ? number_format($p->jp) : '' }}</td>
                                            <td class="text-end">{{ $p->jkk ? 'Yes' : '' }}</td>
                                            <td class="text-end">{{ $p->jkm ? 'Yes' : '' }}</td>
                                            <td class="text-end">
                                                {{ $p->kesehatan ? number_format($p->kesehatan) : '' }}
                                            </td>
                                            <td class="text-end">
                                                {{ $p->tanggungan ? number_format($p->tanggungan) : '' }}
                                            </td>

                                            <td class="text-end">{{ $p->ptkp }}</td>

                                            @if ($p->ptkp != '')
                                                <td class="text-end">{{ get_ter($p->ptkp) }}</td>
                                            @else
                                                <td class="text-end"></td>
                                            @endif
                                            <td class="text-end">{{ number_format($p->total_bpjs) }}</td>
                                            <td class="text-end">{{ number_format($p->bpjs_adjustment) }}</td>
                                            <td class="text-end">{{ number_format($p->pph21) }}</td>
                                            <td class="text-end">{{ number_format($p->prf_salary) }}</td>
                                            <td class="text-end">{{ number_format($p->other_deduction) }}</td>
                                            <td class="text-end">{{ number_format($p->bpjs_employee) }}</td>
                                            <td class="text-end">{{ number_format($p->prf) }}</td>
                                            <td class="text-end">{{ number_format($p->core_cash) }}</td>
                                            <td class="text-end">{{ number_format($p->total) }}</td>

                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <h4>{{ __('No Data Found') }}</h4>
                            @endif
                        </tbody>
                    </table>
                    {{ $payroll->onEachSide(0)->links() }}
                </div>
            </div>
            <p class="px-3">{{ __('Total : ') }} {{ getTotalWorkingDays($year, $month) }} Days.
                ( {{ getTotalWorkingDays($year, $month) - jumlah_libur_nasional($month, $year) }}

                {{ __('working days with') }}
                {{ jumlah_libur_nasional($month, $year) }} {{ __('Holidays') }} )
            </p>

            <p class="px-3 text-success">{{ __('Last update') }}: {{ $last_build }} </p>
        </div>
    </div>
    @if ($data_payroll != null && $data_karyawan != null)
        @include('modals.payroll-modal')
    @endif
</div>
