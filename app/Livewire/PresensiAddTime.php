<?php

namespace App\Livewire;

use App\Models\Karyawan;
use App\Models\Yfrekappresensi;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PresensiAddTime extends Component
{
    public $tanggal;

    public $field = 'first_in';

    public $jam;

    public $shift = 'Pagi';

    public $fields = [
        'first_in' => 'First In',
        'first_out' => 'First Out',
        'second_in' => 'Second In',
        'second_out' => 'Second Out',
        'overtime_in' => 'Overtime In',
        'overtime_out' => 'Overtime Out',
    ];

    public $shifts = [
        'Pagi',
        'Malam',
    ];

    protected $rules = [
        'tanggal' => 'required|date',
        'field' => 'required|string',
        'jam' => 'required|date_format:H:i',
        'shift' => 'required|string',
    ];

    public function save()
    {
        $this->validate();
        $is_sunday = is_sunday($this->tanggal);
        $is_libur_nasional =  is_libur_nasional($this->tanggal);
        $is_saturday = is_saturday($this->tanggal);

        if (! array_key_exists($this->field, $this->fields)) {

            $this->dispatch(
                'message',
                type: 'error',
                title: 'Field tidak valid',
            );

            return;
        }

        DB::beginTransaction();

        try {

            $data = Yfrekappresensi::whereDate('date', $this->tanggal)
                ->where('shift', $this->shift)
                ->whereNull($this->field)
                ->get();

            $total = 0;

            foreach ($data as $item) {

                // isi field baru
                $item->{$this->field} = $this->jam;

                // ambil value terbaru
                $first_in = $item->first_in;
                $first_out = $item->first_out;
                $second_in = $item->second_in;
                $second_out = $item->second_out;
                $overtime_in = $item->overtime_in;
                $overtime_out = $item->overtime_out;

                // generate no scan
                $no_scan = noScan(
                    $first_in,
                    $first_out,
                    $second_in,
                    $second_out,
                    $overtime_in,
                    $overtime_out
                );

                $item->no_scan = $no_scan;
                $item->no_scan_history = $no_scan;
                // --------------------------------------------------------------


                $dataKaryawan = Karyawan::where('id_karyawan', $item->user_id)->first();
                $hasil = saveDetail($item->user_id, $item->first_in, $item->first_out, $item->second_in, $item->second_out, $item->late, $item->shift, $item->date, $dataKaryawan->jabatan_id, $item->no_scan, $dataKaryawan->placement_id, $item->overtime_in, $item->overtime_out);
                // dd($hasil['jam_kerja']);
                // if ($data->user_id == 2152) dd($data->user_id, $hasil['jam_kerja']);
                $item->total_hari_kerja = 0;
                $item->total_jam_kerja = 0;
                $item->total_jam_lembur = 0;
                $item->total_hari_kerja_libur = 0;
                $item->total_jam_kerja_libur = 0;
                $item->total_jam_lembur_libur = 0;

                if (isset($hasil['jam_kerja']) && $hasil['jam_kerja'] >= 1) {
                    $item->total_hari_kerja = 1;
                }

                if (isset($hasil['jam_kerja'])) {
                    $item->total_jam_kerja = $hasil['jam_kerja'];
                }
                if (isset($hasil['jam_lembur'])) {
                    $item->total_jam_lembur = $hasil['jam_lembur'];
                }

                // hitung tambahan shift malam

                if ($item->shift == 'Malam') {
                    if ($is_saturday) {
                        if ($item->total_jam_kerja >= 6) {
                            // $jam_lembur = $jam_lembur + 1;
                            $item->shift_malam = 1;
                        }
                    } else if ($is_sunday || $is_libur_nasional) {
                        if ($item->total_jam_kerja >= 8) {
                            // $jam_lembur = $jam_lembur + 2;
                            $item->shift_malam = 1;
                        }
                    } else {
                        if ($item->total_jam_kerja >= 8) {
                            // $jam_lembur = $jam_lembur + 1;
                            $item->shift_malam = 1;
                        } else {
                            $item->shift_malam = 0;
                        }
                    }
                } else {
                    $item->shift_malam = 0;
                }


                // khusus untuk security kode jabatan 17
                if ($dataKaryawan->jabatan_id == 17 && $item->shift == 'Malam') {
                    if ($is_sunday || $is_libur_nasional) {
                        $item->total_jam_kerja = min($item->total_jam_kerja, 16);
                    } elseif ($is_saturday) {
                        $item->total_jam_kerja = min($item->total_jam_kerja, 6);
                    } else {
                        $item->total_jam_kerja = min($item->total_jam_kerja, 8);
                    }
                }

                // $this->checkData();
                // $berhasil = 1;
                // if ($this->is_delete) {
                //     $berhasil = 3;
                //     if ($item->no_scan == "") {
                //         $item->no_scan_history = null;
                //         $berhasil = 2;
                //     }
                // }



                if ($is_libur_nasional || $is_sunday) {
                    $item->total_hari_kerja_libur = 0;
                    $item->total_hari_kerja = 0;
                    $item->total_jam_kerja_libur = $item->total_jam_kerja * 2;
                    $item->total_jam_lembur_libur = $item->total_jam_lembur * 2;
                    $item->total_jam_kerja = 0;
                    $item->total_jam_lembur = 0;
                }



                // $item->save();

                //---------------------------------------------------------------

                $item->save();

                $total++;
            }

            DB::commit();

            $this->dispatch(
                'message',
                type: 'success',
                title: "{$total} data berhasil ditambahkan",
            );

            $this->reset('jam');
        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch(
                'message',
                type: 'error',
                title: $e->getMessage(),
            );
        }
    }

    public function render()
    {
        return view('livewire.presensi-add-time');
    }
}
