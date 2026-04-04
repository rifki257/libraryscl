<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-4 flex-grow-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Peminjaman') }}
            </h2>

            <ul
                class="nav nav-tabs border-bottom-0"
                id="returnTab"
                role="tablist"
            >
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link active fw-bold text-gray-600"
                        id="confirmation-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#confirmation-pane"
                        type="button"
                        role="tab"
                    >
                        <i class="bi bi-check2-circle me-1"></i> Konfirmasi
                        Pinjam
                        <span class="badge bg-danger ms-1" id="pending-count">
                            {{ $semuaPeminjaman->count() }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link fw-bold text-gray-600"
                        id="all-data-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#all-data-pane"
                        type="button"
                        role="tab"
                    >
                        <i class="bi bi-collection-play me-1"></i> Semua Data

                        <span class="badge bg-primary ms-1">
                            {{ (isset($semuaPeminjam)) ? $semuaPeminjam->count() : 0 }}
                        </span>
                    </button>
                </li>
            </ul>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="overflow-hidden shadow-sm sm:rounded-lg"
                style="background-color: rgb(235, 235, 235)"
            >
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <div class="card-body">
                            <div id="tabel-peminjaman" class="mt-4">
                                <div class="tab-content" id="returnTabContent">
                                    <div
                                        class="tab-pane fade show active"
                                        id="confirmation-pane"
                                        role="tabpanel"
                                        aria-labelledby="confirmation-tab"
                                    >
                                        @include ('partials.konfir_pinjam')
                                    </div>

                                    <div
                                        class="tab-pane fade"
                                        id="all-data-pane"
                                        role="tabpanel"
                                        aria-labelledby="all-data-tab"
                                    >
                                        @include ('partials.pinjam_data')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
