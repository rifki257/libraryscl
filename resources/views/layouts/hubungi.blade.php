<nav
    x-data="{
        open: false,
        userDropdown: false,
        showModal: false,
        contactModal: false,
    }"
    ...
>
    <a
        href="javascript:void(0)"
        @click="contactModal = true"
        class="hover:text-indigo-600 transition-colors"
    >
        Hubungi Pustakawan
    </a>

    <div
        x-show="contactModal"
        x-cloak
        class="fixed inset-0 z-[100] overflow-y-auto"
    >
        <div
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
            @click="contactModal = false"
        ></div>

        <div
            class="flex min-h-full items-center justify-center p-4 text-center"
        >
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-sm"
            >
                <div class="bg-white px-6 py-8">
                    <div class="text-center">
                        <div
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 mb-4"
                        >
                            <i
                                class="bi bi-chat-dots text-indigo-600 text-2xl"
                            ></i>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900">
                            Butuh Bantuan?
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">Pustakawan kami siap membantu Anda pada jam kerja (06.30 - 15.00).</p>
                    </div>

                    <div class="mt-8 space-y-3">
                        <a
                            href="https://wa.me/6285225934243"
                            target="_blank"
                            class="flex items-center p-4 rounded-xl border border-gray-100 hover:border-green-500 hover:bg-green-50 transition-all group"
                        >
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-500 text-white"
                            >
                                <i class="bi bi-whatsapp text-xl"></i>
                            </div>
                            <div class="ml-4 text-left">
                                <p class="text-sm font-bold text-gray-900">WhatsApp Chat</p>
                                <p class="text-xs text-gray-500">Tanya stok & perpanjangan</p>
                            </div>
                        </a>

                        <a
                            href="mailto:pustakawan@sekolah.sch.id"
                            class="flex items-center p-4 rounded-xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50 transition-all group"
                        >
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-500 text-white"
                            >
                                <i class="bi bi-envelope text-xl"></i>
                            </div>
                            <div class="ml-4 text-left">
                                <p class="text-sm font-bold text-gray-900">Email Resmi</p>
                                <p class="text-xs text-gray-500">Laporan denda & kendala akun</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-center">
                    <button
                        type="button"
                        @click="contactModal = false"
                        class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        Tutup Jendela
                    </button>
                </div>
            </div>
        </div>
    </div>
