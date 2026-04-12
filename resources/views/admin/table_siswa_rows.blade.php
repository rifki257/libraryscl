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
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">Data Kelas XI tidak ditemukan.</td>
    </tr>
@endforelse
