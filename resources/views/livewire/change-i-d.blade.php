<div>
    <div class="col-5 m-5">
        <div class="card">
            <div class="card-header">
                <h3>Ganti ID</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="ID" class="form-label">ID yang akan diganti -> {{ $namaIdLama }}</label>
                    <input type="text" class="form-control" id="ID" wire:model.live='idLama'>
                    <p class='text-red'>{{ $idLamaError }}</p>
                </div>
                <div class="mb-3">
                    <label for="ID2" class="form-label">ID yang diinginkan</label>
                    <input type="text" class="form-control" id="ID2" wire:model.live='idBaru'>
                    @if ($idBaru != '')
                        @if ($namaIdBaru != '')
                            <p class='text-red'> {{ $namaIdBaru }} sudah ada dalam database</p>
                        @else
                            @if ($idBaruError)
                                <p class='text-red'>{{ $idBaruError }}</p>
                            @else
                                <p class='text-blue'>{{ $idBarumessage }}</p>
                            @endif
                        @endif
                    @endif
                </div>
                <button class="btn btn-success" wire:click='rubah' wire:confirm="Yakin mau di rubah?"
                    {{ $isupdateable ? '' : 'disabled' }}>Rubah</button>
                <button class="btn btn-dark" wire:click='cancel'>Cancel</button>
            </div>
        </div>
    </div>
</div>
