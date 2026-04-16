<x-dropdown align="right" width="64">
    <x-slot name="trigger">
        <button
            onclick="markAllAsReadSilently()"
            class="relative inline-flex items-center p-2 rounded-full text-gray-600 hover:text-black hover:bg-gray-100 transition-all focus:outline-none"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>

            @auth
                @if (auth()->user()->unreadNotifications->count() > 0)
                    <span id="notification-badge" class="absolute top-1.5 right-1.5 flex h-4 w-4">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] text-white items-center justify-center font-bold"
                        >
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    </span>
                @endif
            @endauth
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="w-80 sm:w-96">
            <div
                class="block px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100"
            >
                Pemberitahuan
            </div>

            <div class="max-h-80 overflow-y-auto">
                @auth
                    @forelse (auth()->user()->notifications as $notification)
                        <div
                            class="px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0 {{ $notification->read_at ? 'opacity-60' : '' }}"
                        >
                            <div class="flex flex-col gap-1">
                                <div class="flex justify-between items-start">
                                    <span
                                        class="text-[10px] font-bold text-red-600 uppercase"
                                        >Peminjaman Ditolak</span
                                    >
                                    <span
                                        class="text-[9px] text-gray-400"
                                        >{{ $notification->created_at->diffForHumans() }}</span
                                    >
                                </div>
                                <p class="text-xs text-gray-700 leading-snug">
                                    {{ $notification->data['pesan'] }}
                                </p>
                                @if (isset($notification->data['alasan']))
                                    <p class="text-[11px] text-gray-500 italic bg-gray-100 p-1.5 rounded mt-1">"{{ $notification->data['alasan'] }}"</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-gray-500">
                            <p class="text-xs italic">Tidak ada notifikasi baru</p>
                        </div>
                    @endforelse
                @endauth
            </div>

            @auth
                @if (auth()->user()->notifications->count() > 0)
                    <a
                        href="{{ route('markNotificationsRead') }}"
                        class="block w-full text-center py-2 text-[11px] font-bold text-indigo-600 hover:bg-indigo-50 border-t border-gray-100"
                    >
                        Tandai Semua Dibaca
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>
</x-dropdown>
<script>
    function markAllAsReadSilently() {
        // 1. Cari elemen badge berdasarkan ID
        const badge = document.getElementById('notification-badge');
        
        // 2. Jika ketemu, langsung hapus dari layar (Hapus, bukan cuma sembunyi)
        if (badge) {
            badge.remove(); 
            console.log('Badge dihapus secara instan');
        }

        // 3. Panggil server di latar belakang untuk update database
        fetch("{{ route('markNotificationsRead') }}", {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server merespon:', data.message);
        })
        .catch(error => {
            console.error('Waduh, ada error pas update notif:', error);
        });
    }
</script>   
