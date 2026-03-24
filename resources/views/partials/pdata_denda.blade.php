<div
    class="modal fade"
    id="modalDenda{{ $data->id_pinjam }}"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Rincian Keterlambatan</h5>
                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label class="fw-bold">Peminjam:</label>
                    <p>{{ $data->user->name }}</p>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="fw-bold text-danger"
                            >Total Terlambat:</label
                        >
                        <p class="fs-5">{{ $jumlahHariTelat }} Hari</p>
                    </div>
                    <div class="col-6">
                        <label class="fw-bold text-danger">Total Denda:</label>
                        <p class="fs-5 fw-bold text-primary">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="alert alert-info py-2">
                    <small
                        >* Perhitungan: {{ $jumlahHariTelat }} hari x Rp
                        150.000</small
                    >
                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
