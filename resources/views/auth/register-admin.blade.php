<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Toko - MySembako</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center gap-3">
                    <img src="/icon/Logo_Mysembako.svg" alt="Logo" class="h-9" onerror="this.style.display='none'">
                    <span class="text-xl font-bold text-orange-500 tracking-wide">MySembako</span>
                </div>
                <a href="{{ route('login') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 font-semibold text-sm transition-colors">
                    Sudah punya akun? Login
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Buka Toko Baru</h1>
                <p class="text-gray-600">Daftarkan toko Anda dan mulai jual produk di MySembako</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info Box -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p class="font-semibold mb-1">Sebagai Admin Toko, Anda akan dapat:</p>
                        <ul class="list-disc list-inside space-y-1 ml-2">
                            <li>Menambahkan dan mengelola produk</li>
                            <li>Mengatur stok dan harga</li>
                            <li>Melihat laporan penjualan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register.admin.submit') }}" class="space-y-6">
                @csrf

                <!-- Nama Pemilik Toko -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Toko <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors"
                        placeholder="Masukkan nama Anda"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors"
                        placeholder="contoh@email.com"
                    >
                    <p class="mt-2 text-sm text-gray-500">Email ini akan digunakan untuk login</p>
                </div>

                <!-- No Telepon -->
                <div>
                    <label for="no_telp" class="block text-sm font-semibold text-gray-700 mb-2">
                        No. Telepon
                    </label>
                    <input 
                        type="text" 
                        id="no_telp" 
                        name="no_telp" 
                        value="{{ old('no_telp') }}" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors"
                        placeholder="081234567890"
                    >
                    <p class="mt-2 text-sm text-gray-500">Nomor telepon untuk kontak (opsional)</p>
                </div>

                <!-- Lokasi Kota -->
                <div>
                    <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                        Lokasi Kota
                    </label>
                    <input 
                        type="text" 
                        id="location" 
                        name="location" 
                        value="{{ old('location') }}" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors"
                        placeholder="Contoh: Jakarta, Bandung, Surabaya"
                    >
                    <p class="mt-2 text-sm text-gray-500">Kota lokasi toko Anda (opsional)</p>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            minlength="6"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors pr-12"
                            placeholder="Minimal 6 karakter"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password')" 
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <svg id="icon-password-show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="icon-password-hide" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Minimal 6 karakter</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            minlength="6"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors pr-12"
                            placeholder="Ulangi password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password_confirmation')" 
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <svg id="icon-password_confirmation-show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="icon-password_confirmation-hide" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full px-6 py-3 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition-colors">
                        Daftar & Buka Toko
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="text-orange-500 font-semibold hover:text-orange-600">
                            Login di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </main>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const showIcon = document.getElementById(`icon-${fieldId}-show`);
            const hideIcon = document.getElementById(`icon-${fieldId}-hide`);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                showIcon.classList.remove('hidden');
                hideIcon.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                showIcon.classList.add('hidden');
                hideIcon.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>

