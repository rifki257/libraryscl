<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Kelola Akun Admin') }}
            </h2>

            <div class="position-relative" shadow-sm style="width: 300px">
                <span
                    class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
                    style="z-index: 5"
                >
                    <i class="bi bi-search"></i>
                </span>
                <input
                    type="text"
                    id="liveSearch"
                    class="form-control ps-5 pe-5 shadow-sm"
                    placeholder="Cari admin atau email..."
                    autocomplete="off"
                    style="border-radius: 8px; border: 1px solid #ddd"
                />
                <button
                    id="clearSearch"
                    class="btn position-absolute top-50 end-0 translate-middle-y me-2"
                    style="
                        display: none;
                        border: none;
                        background: transparent;
                        z-index: 10;
                    "
                >
                    <i class="bi bi-x-circle-fill text-muted"></i>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Jabatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody" class="text-center">
                    @include ('admin.table_admin_rows')
                </tbody>
            </table>
            <div class="mt-4">{{ $admins->links() }}</div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // Fungsi Fetch Data
            function fetchAdmins(query) {
                $.ajax({
                    url: '{{ route('akun_admin') }}', // Ganti dengan nama route index Anda
                    type: 'GET',
                    data: { search: query },
                    success: function (data) {
                        $('#adminTableBody').html(data);
                    },
                });
            }

            // Input Event (Ketuk Keyboard)
            $('#liveSearch').on('keyup', function () {
                let val = $(this).val();
                // Tampilkan/Sembunyikan tombol reset (X)
                val.length > 0
                    ? $('#clearSearch').fadeIn(100)
                    : $('#clearSearch').fadeOut(100);
                fetchAdmins(val);
            });

            // Klik Tombol Reset (X)
            $('#clearSearch').on('click', function () {
                $('#liveSearch').val('');
                $(this).hide();
                fetchAdmins('');
                $('#liveSearch').focus();
            });
        });
        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000,
        });
        @endif
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Akun?',
                text: 'Data ini tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        function editPassword(id, name) {
            Swal.fire({
                title: 'Ganti Password',
                text: 'Masukkan password baru untuk ' + name,
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off',
                },
                showCancelButton: true,
                confirmButtonText: 'Update Password',
                confirmButtonColor: '#3b82f6',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true, // Menampilkan loading saat proses
                inputValidator: (value) => {
                    if (!value) return 'Password tidak boleh kosong!';
                    if (value.length < 8) return 'Password minimal 8 karakter!';
                },
                preConfirm: (newPassword) => {
                    // Gunakan AJAX Fetch di sini daripada form.submit()
                    return fetch(`/akun-admin/update-password/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({ password: newPassword }),
                    })
                        .then((response) => {
                            if (!response.ok)
                                throw new Error('Gagal memperbarui password');
                            return response.json();
                        })
                        .catch((error) => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading(),
            }).then((result) => {
                if (result.isConfirmed && result.value.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.value.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            });
        }
    </script>
</x-app-layout>
