<div id="tabel-akun" class="mt-4">
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>
                    <input
                        type="checkbox"
                        id="select-all"
                        onclick="toggleSelectAll()"
                    />
                </th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @php
                $usersX = $users->filter(fn($u) => str_starts_with($u->kelas, 'X '));
            @endphp

            @forelse ($usersX as $user)
                <tr class="align-middle border-b hover:bg-gray-50">
                    <td>
                        <input
                            type="checkbox"
                            class="user-checkbox"
                            value="{{ $user->id }}"
                        />
                    </td>
                    <td>{{ $user->nis ?? '-' }}</td>
                    <td>{{ $user->name }}</td>
                    <td>
                        <span
                            class="badge bg-info text-dark"
                            >{{ $user->kelas }}</span
                        >
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->no_hp ?? '-' }}</td>
                    <td>
                        <button
                            type="button"
                            onclick="editPassword({{ $user->id }}, '{{ $user->name }}')"
                            class="btn btn-primary text-white"
                        >
                            Password
                        </button>
                        <form
                            id="delete-form-{{ $user->id }}"
                            action="{{ route('user.destroy', $user->id) }}"
                            method="POST"
                            class="inline-block m-0"
                        >
                            @csrf
                            @method ('DELETE')
                            <button
                                type="button"
                                onclick="confirmDelete({{ $user->id }})"
                                class="btn btn-danger"
                            >
                                Hapus
                            </button>
                        </form>
                        <form
                            id="update-pw-form-{{ $user->id }}"
                            action="{{ route('user.updatePassword', $user->id) }}"
                            method="POST"
                            style="display: none"
                        >
                            @csrf
                            @method ('PUT')
                            <input
                                type="hidden"
                                name="password"
                                id="pw-input-{{ $user->id }}"
                            />
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
                </table>
                </div>
