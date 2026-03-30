<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('akun admin') }}
        </h2>
        <div class="flex justify-end">
            <div class="input-group" style="max-width: 350px">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input
                    type="text"
                    id="search-input"
                    name="search"
                    class="form-control border-start-0 border-end-0 ps-0 shadow-none"
                    placeholder="Cari nama, email, no hp..."
                    onkeyup="searchTable()"
                    autocomplete="off"
                />
                <button
                    class="btn bg-white border border-start-0 d-none align-items-center gap-1"
                    type="button"
                    id="reset-search"
                    onclick="resetTable()"
                    style="z-index: 5"
                >
                    <i class="bi bi-x-circle-fill text-danger"></i>
                    <span style="font-size: 0.8rem" class="text-muted fw-bold"
                        >Reset</span
                    >
                </button>
            </div>
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
                            <div id="tabel-akun" class="mt-4">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>No HP</th>
                                            <th>Jabatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach ($admins as $admin)
                                            <tr
                                                class="align-middle border-b hover:bg-gray-50"
                                            >
                                                <td>{{ $admin->name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>
                                                    {{ $admin->no_hp ?? '-' }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="px-2 py-1 text-xs font-bold rounded {{ $admin->role == 'kepper' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}"
                                                    >
                                                        {{ strtoupper($admin->role) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        onclick="editPassword({{ $admin->id }}, '{{ $admin->name }}')"
                                                        class="btn btn-primary text-white"
                                                    >
                                                        Password
                                                    </button>
                                                    <form
                                                        id="delete-form-{{ $admin->id }}"
                                                        action="{{ route('admin.destroy', $admin->id) }}"
                                                        method="POST"
                                                        class="inline-block m-0"
                                                    >
                                                        @csrf
                                                        @method ('DELETE')
                                                        <button
                                                            type="button"
                                                            onclick="confirmDelete({{ $admin->id }})"
                                                            class="btn btn-danger"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </form>
                                                    <form
                                                        id="update-pw-form-{{ $admin->id }}"
                                                        action="{{ route('admin.updatePassword', $admin->id) }}"
                                                        method="POST"
                                                        style="display: none"
                                                    >
                                                        @csrf
                                                        @method ('PUT')
                                                        <input
                                                            type="hidden"
                                                            name="password"
                                                            id="pw-input-{{ $admin->id }}"
                                                        />
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                inputValidator: (value) => {
                    if (!value) {
                        return 'Password tidak boleh kosong!';
                    }
                    if (value.length < 8) {
                        return 'Password minimal 8 karakter!';
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('pw-input-' + id).value = result.value;
                    document.getElementById('update-pw-form-' + id).submit();
                }
            });
        }
        function searchTable() {
            let input = document.getElementById('search-input');
            let filter = input.value.toLowerCase();
            let table = document.querySelector('table');
            let tr = table.getElementsByTagName('tr');
            let btnReset = document.getElementById('reset-search');

            if (filter.length > 0) {
                btnReset.classList.remove('d-none');
                btnReset.classList.add('d-flex');
            } else {
                btnReset.classList.add('d-none');
                btnReset.classList.remove('d-flex');
            }

            for (let i = 1; i < tr.length; i++) {
                let tdNama = tr[i].getElementsByTagName('td')[0];
                let tdEmail = tr[i].getElementsByTagName('td')[1];
                let tdNoHp = tr[i].getElementsByTagName('td')[2];

                if (tdNama || tdEmail || tdNoHp) {
                    let textNama = tdNama.textContent || tdNama.innerText;
                    let textEmail = tdEmail.textContent || tdEmail.innerText;
                    let textNoHp = tdNoHp.textContent || tdNoHp.innerText;

                    let combinedText = textNama + ' ' + textEmail + ' ' + textNoHp;

                    if (combinedText.toLowerCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        function resetTable() {
            let input = document.getElementById('search-input');
            input.value = '';
            searchTable();
            input.focus();
        }
    </script>
</x-app-layout>
