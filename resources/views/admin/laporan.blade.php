<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Perpustakaan</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <label class="fw-bold mb-2">Shortcut Periode:</label>
                    <div class="d-flex gap-2 mb-4">
                        <a href="?tgl_mulai={{ now()->startOfWeek()->format('Y-m-d') }}&tgl_selesai={{ now()->endOfWeek()->format('Y-m-d') }}" class="btn btn-outline-primary btn-sm">Minggu Ini</a>
                        <a href="?tgl_mulai={{ now()->subWeek()->startOfWeek()->format('Y-m-d') }}&tgl_selesai={{ now()->subWeek()->endOfWeek()->format('Y-m-d') }}" class="btn btn-outline-primary btn-sm">Minggu Lalu</a>
                        <a href="?tgl_mulai={{ now()->startOfMonth()->format('Y-m-d') }}&tgl_selesai={{ now()->endOfMonth()->format('Y-m-d') }}" class="btn btn-outline-primary btn-sm">Bulan Ini</a>
                        <a href="?tgl_mulai={{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}&tgl_selesai={{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}" class="btn btn-outline-primary btn-sm">Bulan Lalu</a>
                    </div>

                    <form action="{{ route('admin.laporan.export') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" class="form-control" value="{{ $tgl_mulai ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai" class="form-control" value="{{ $tgl_selesai ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Jenis Laporan</label>
                            <select name="jenis" class="form-select">
                                <option value="peminjaman" {{ ($jenis ?? '') == 'peminjaman' ? 'selected' : '' }}>Data Peminjaman</option>
                                <option value="pengembalian" {{ ($jenis ?? '') == 'pengembalian' ? 'selected' : '' }}>Data Pengembalian</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" formmethod="GET" formaction="{{ route('admin.laporan.index') }}" class="btn btn-secondary w-100">Filter</button>
                            <button type="submit" class="btn btn-success w-100">Ke Word</button>
                        </div>
                    </form>
                </div>
            </div>

            
        </div>
    </div>
</x-app-layout>