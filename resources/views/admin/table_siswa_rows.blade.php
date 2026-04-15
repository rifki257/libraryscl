@forelse ($users as $user)
    <tr class="align-middle text-center">
        <td>
            <input
                type="checkbox"
                class="siswa-checkbox"
                value="{{ $user->id }}"
                data-status="{{ $user->status }}"
                data-kelas="{{ $user->kelas }}"
                {{-- Tambahkan data-kelas --}}
                onclick="updateBulkEditButton()"
            />
        </td>
        <td>{{ $user->nis ?? '-' }}</td>
        <td>{{ $user->name }}</td>
        <td>
            @if ($user->status === 'alumni')
                <span class="badge bg-dark text-white">
                    <i class="fas fa-graduation-cap me-1"></i> Alumni
                </span>
            @else
                <span class="badge bg-info text-dark">{{ $user->kelas }}</span>
            @endif
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
