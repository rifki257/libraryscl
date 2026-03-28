<div class="flex-1 flex justify-center px-4">
    <form action="{{ route('katalog') }}" method="GET" class="w-full max-w-lg">
        <div class="relative">
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}"
                placeholder="Cari judul, penulis, atau penerbit..." 
                class="w-full bg-gray-100 border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500 rounded-lg py-2 pl-4 pr-10 text-sm transition-all"
            >
            <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-blue-500">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>
    </form>
</div>
<script>
    // Tambahkan script ini di halaman katalog atau layout utama
const searchInput = document.querySelector('input[name="q"]');
const container = document.getElementById('katalog-container'); // Pastikan ID ini ada di pembungkus @include

if (searchInput && window.location.pathname.includes('/katalog')) {
    searchInput.addEventListener('input', _.debounce(function(e) {
        const query = e.target.value;
        
        fetch(`/katalog?q=${query}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        });
    }, 500)); // Menunggu 500ms agar tidak terlalu berat ke server
}
</script>