@forelse ($users as $user)
    <tr class="align-middle text-center">
        <td>
            <input
                type="checkbox"
                class="user-checkbox"
                value="{{ $user->id }}"
                data-kelas="{{ $user->kelas }}"
            />
        </td>
        <td>{{ $user->nis ?? '-' }}</td>
        <td>{{ $user->name }}</td>
        <td>
            <span class="badge bg-info text-dark">{{ $user->kelas }}</span>
        </td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->no_hp ?? '-' }}</td>
        <td>
            <button
                class="btn btn-sm btn-info btn-reset-pw"
                data-id="{{ $user->id }}"
                title="Reset Password"
            >
                <i class="fas fa-key"></i>
            </button>

            <button
                class="btn btn-sm btn-danger btn-delete-siswa"
                data-id="{{ $user->id }}"
                title="Hapus"
            >
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">Data siswa tidak ditemukan.</td>
    </tr>
@endforelse
