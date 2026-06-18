<div class="container py-4">

    <div class="card shadow-sm border-0">

        <div class="card-header">
            <h5 class="mb-0">
                Tambah Jam Presensi Massal
            </h5>
        </div>

        <div class="card-body">

            <form wire:submit.prevent="save">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Tanggal
                        </label>

                        <input type="date" class="form-control" wire:model="tanggal">

                        @error('tanggal')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Shift
                        </label>

                        <select class="form-select" wire:model="shift">
                            @foreach ($shifts as $item)
                                <option value="{{ $item }}">
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>

                        @error('shift')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Pilih Kolom
                        </label>

                        <select class="form-select" wire:model="field">
                            @foreach ($fields as $key => $label)
                                <option value="{{ $key }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('field')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Jam
                        </label>

                        <input type="time" class="form-control" wire:model="jam">

                        @error('jam')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                </div>

                <div class="alert alert-warning">
                    Sistem hanya akan mengisi data yang masih kosong (NULL).
                    Data yang sudah memiliki jam tidak akan diubah.
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        Simpan
                    </span>

                    <span wire:loading>
                        Processing...
                    </span>
                </button>

            </form>

        </div>

    </div>

</div>
