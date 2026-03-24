<head>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Register') }}
        </h2>
    </x-slot>
    @if (session('status'))
    <div class="mb-4 font-medium text-sm text-green-600 p-4 bg-green-100 border border-green-400 rounded-md">
        {{ session('status') }}
    </div>
@endif
    @if ($errors->any())
    <div class="text-red-600">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<x-guest-layout>
    <form method="POST" action="{{ auth()->check() ? route('register.petugas.store') : route('register') }}">
    @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" placeholder="Nama Lengkap Anda" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" placeholder="nama@email.com" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        
        <!-- No hp -->
        <div class="mt-4">
            <x-input-label for="no_hp" :value="__('No hp')" />
            <x-text-input id="no_hp" class="block mt-1 w-full" type="text" name="no_hp" placeholder="08xx xxxx xxxx" :value="old('no_hp')" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        </div>
        
        <!-- Role -->
        <div class="mt-4">
    <x-input-label for="role" :value="__('Daftar Sebagai')" />
    
    <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required onchange="toggleFields()" 
        {{ Auth::user()->role == 'petugas' ? 'readonly' : '' }}>
        
        @if(Auth::user()->role == 'petugas')
            <option value="anggota" selected>Anggota</option>
        @else
            <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
            <option value="kepper" {{ old('role') == 'kepper' ? 'selected' : '' }}>Kepala Sekolah</option>
            <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
            <option value="anggota" {{ old('role') == 'anggota' ? 'selected' : '' }}>Anggota</option>
        @endif
    </select>
    
    <x-input-error :messages="$errors->get('role')" class="mt-2" />
</div>

<div class="mt-4" id="nis_wrapper">
    <x-input-label for="nis" :value="__('Nis')" />
    <x-text-input id="nis" class="block mt-1 w-full" type="text" name="nis" placeholder="10.2345.6789" :value="old('nis')" oninput="formatNIS(this)" maxlength="12" />
    <x-input-error :messages="$errors->get('nis')" class="mt-2" />
</div>

<div class="mt-4" id="kelas_wrapper">
    <x-input-label for="kelas" :value="__('Kelas')" />
    <x-text-input id="kelas" class="block mt-1 w-full" type="text" name="kelas" placeholder="10 rpl 1" :value="old('kelas')" />
    <x-input-error :messages="$errors->get('kelas')" class="mt-2" />
</div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            placeholder="Kata Rifki Harus Lebih Dari 8"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi Kata Rifki"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('dashboard') }}">
                {{ __('Kembali') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
    
</x-guest-layout>
<script>
function toggleFields() {
    const role = document.getElementById('role').value;
    const nisWrapper = document.getElementById('nis_wrapper');
    const kelasWrapper = document.getElementById('kelas_wrapper');
    const nisInput = document.getElementById('nis');
    const kelasInput = document.getElementById('kelas');

    if (role === 'anggota') {
        nisWrapper.classList.remove('hidden');
        kelasWrapper.classList.remove('hidden');
        nisInput.setAttribute('required', 'required');
        kelasInput.setAttribute('required', 'required');
    } else {
        nisWrapper.classList.add('hidden');
        kelasWrapper.classList.add('hidden');
        
        nisInput.removeAttribute('required');
        kelasInput.removeAttribute('required');
        
        nisInput.value = '';
        kelasInput.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});

function formatNIS(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 10) value = value.slice(0, 10);
    let formatted = "";
    if (value.length > 0) {
        formatted += value.substring(0, 2);
        if (value.length > 2) {
            formatted += "." + value.substring(2, 6);
            if (value.length > 6) {
                formatted += "." + value.substring(6, 10);
            }
        }
    }
    input.value = formatted;
}
document.addEventListener('DOMContentLoaded', function() {
    const statusAlert = document.querySelector('.text-green-600');
    if (statusAlert) {
        setTimeout(() => {
            statusAlert.style.transition = "opacity 0.5s ease";
            statusAlert.style.opacity = "0";
            setTimeout(() => statusAlert.remove(), 500);
        }, 3000);
    }
});
</script>
</x-app-layout>