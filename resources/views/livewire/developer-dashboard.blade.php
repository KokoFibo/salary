<div>
    <div class="d-flex mt-5 justify-content-evenly">
        <a href="/deletenoscan"><button class="btn btn-primary">Delete No Scan History</button></a>
        <a href="/yfdeletetanggalpresensiwr"><button class="btn btn-primary">Delete Tanggal Presensi</button></a>
        <a href="/movepresensidata"><button class="btn btn-primary">Move Presensi Data (backup)</button></a>
        <a href="/moveback"><button class="btn btn-primary">Move Back Data</button></a>
        <a href="/absensikosong"><button class="btn btn-primary col-12">Data Absensi Kosong</button></a>
        <a href="/usernotfound"><button class="btn btn-primary col-12">User Not Found</button></a>



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
</div>
