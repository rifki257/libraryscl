<div class="me-1 d-flex align-items-center">
    <div class="dropdown me-3">
        <a
            class="nav-link position-relative p-0"
            href="#"
            id="notifDummy"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            <i class="bi bi-bell fs-4 text-secondary"></i>
            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light"
                style="
                    font-size: 0.6rem;
                    transform: translate(-10%, -20%) !important;
                "
            >
                3
            </span>
        </a>

        <ul
            class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2"
            aria-labelledby="notifDummy"
            style="width: 320px; border-radius: 15px; overflow: hidden"
        >
            <li
                class="px-3 py-2 bg-light border-bottom d-flex justify-content-between align-items-center"
            >
                <span class="fw-bold small">Notifikasi</span>
                <a
                    href="#"
                    class="text-primary x-small"
                    style="font-size: 0.75rem; text-decoration: none"
                    >Tandai Dibaca</a
                >
            </li>

            <div style="max-height: 350px; overflow-y: auto">
                <li>
                    <a
                        class="dropdown-item py-3 border-bottom d-flex align-items-start gap-3"
                        href="#"
                    >
                        <div
                            class="bg-success bg-opacity-10 p-2 rounded-circle text-success"
                        >
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div style="white-space: normal">
                            <p class="mb-0 small">Pinjaman buku <strong>"admin"</strong> telah disetujui petugas!</p>
                            <small class="text-muted">2 menit yang lalu</small>
                        </div>
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item py-3 border-bottom d-flex align-items-start gap-3 bg-light"
                        href="#"
                    >
                        <div
                            class="bg-danger bg-opacity-10 p-2 rounded-circle text-danger"
                        >
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div style="white-space: normal">
                            <p class="mb-0 small text-danger fw-bold">Peringatan: Buku Anda sudah jatuh tempo!</p>
                            <small class="text-muted">1 jam yang lalu</small>
                        </div>
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item py-3 border-bottom d-flex align-items-start gap-3"
                        href="#"
                    >
                        <div
                            class="bg-info bg-opacity-10 p-2 rounded-circle text-info"
                        >
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <div style="white-space: normal">
                            <p class="mb-0 small">Ada buku baru yang mungkin Anda suka di katalog.</p>
                            <small class="text-muted">Kemarin</small>
                        </div>
                    </a>
                </li>
            </div>

            <li>
                <a
                    class="dropdown-item text-center small text-primary fw-bold py-2"
                    href="#"
                >
                    Lihat Semua Notifikasi
                </a>
            </li>
        </ul>
    </div>
</div>
<style>
    .dropdown-item {
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f1f5f9 !important;
    }

    .dropdown-menu {
        display: block;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    div::-webkit-scrollbar {
        width: 4px;
    }
    div::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
</style>
