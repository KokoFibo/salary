<div>
    <div class="d-flex mt-5 justify-content-evenly">
        <a href="/deletenoscan"><button class="btn btn-primary">Delete No Scan History</button></a>
        <a href="/yfdeletetanggalpresensiwr"><button class="btn btn-primary">Delete Tanggal Presensi</button></a>
        <a href="/movepresensidata"><button class="btn btn-primary">Move Presensi Data (backup)</button></a>
        <a href="/moveback"><button class="btn btn-primary">Move Back Data</button></a>
        <a href="/absensikosong"><button class="btn btn-primary col-12">Data Absensi Kosong</button></a>
        <a href="/usernotfound"><button class="btn btn-primary col-12">User Not Found</button></a>
        <a href="/rubahid"><button class="btn btn-primary">Rubah ID</button></a>




    </div>
    <div class="d-flex mt-5 justify-content-evenly">
        <a href="/jabatan"><button class="btn btn-primary">Add Jabatan</button></a>
        <a href="/addcompany"><button class="btn btn-primary">Add Company</button></a>
        <a href="/addplacement"><button class="btn btn-primary">Add Placement</button></a>
        <a href="/department"><button class="btn btn-primary">Add Departement</button></a>
        <button wire:click='clear_build' class="btn btn-primary">Clear Build</button>
        <button wire:click='clear_payroll_rebuild' class="btn btn-primary">Clear Payroll Rebuild</button>
        <button wire:click='delete_failed_jobs' class="btn btn-primary">Delete Failed Job</button>
    </div>
    <div class="d-flex mt-5 justify-content-evenly">
        <button class='btn btn-danger' wire:confirm='Are you sure?' wire:click='delete_diatas_4jt'>Delete Karyawan Gaji
            diatas
            atau sama dengan 4
            juta</button>

        <button class='btn btn-danger' wire:confirm='Are you sure?' wire:click='delete_dibawah_4jt'>Delete Karyawan
            Gaji
            dibawah 4 juta</button>
    </div>
    <div class="d-flex mt-5 justify-content-evenly">

        <button class='btn btn-danger' wire:confirm='Are you sure?' wire:click='delete_dibawah_4jt_keep_company'>Delete
            Karyawan
            Gaji
            dibawah 4 juta keep Company ['YAM', 'YIG', 'YCME', 'YSM','YEV']</button>
        <button class='btn btn-danger' wire:confirm='Are you sure?' wire:click='delete_karyawan_company'>Delete
            karyawan
            Company ['YAM', 'YIG', 'YCME', 'YSM', 'YEV']</button>
    </div>
</div>
