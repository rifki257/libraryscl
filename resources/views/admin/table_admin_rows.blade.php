@foreach ($admins as $admin)
    <tr class="align-middle border-b hover:bg-gray-50">
        <td>{{ $admin->name }}</td>
        <td>{{ $admin->email }}</td>
        <td>{{ $admin->no_hp ?? '-' }}</td>
        <td>
            <span
                class="px-2 py-1 text-xs font-bold rounded {{ $admin->role == 'kepper' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}"
            >
                {{ strtoupper($admin->role) }}
            </span>
        </td>
        <td>
            @if ($admin->role === 'petugas')
                <button
                    type="button"
                    onclick="resetPasswordAdmin({{ $admin->id }}, '{{ $admin->name }}')"
                    class="btn btn-primary text-white"
                    title="Reset Password"
                >
                    <i class="fas fa-key"></i>
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
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                <form
                    id="update-pw-form-{{ $admin->id }}"
                    action="{{ route('admin.resetPassword', $admin->id) }}"
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
            @else
                <span class="badge bg-secondary">Utama (No Action)</span>
            @endif
        </td>
    </tr>
@endforeach
