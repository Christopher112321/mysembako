<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - MySembako</title>
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
                    <span class="text-xl font-bold text-orange-500 tracking-wide">MySembako Admin</span>
                </div>
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-full font-semibold text-sm hover:bg-gray-700 transition-colors">
                    Kembali
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Tambah Produk Baru</h1>

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

            <!-- Form -->
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Nama Produk -->
                <div>
                    <label for="nama_produk" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama_produk" 
                        name="nama_produk" 
                        value="{{ old('nama_produk') }}" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors"
                        placeholder="Masukkan nama produk"
                    >
                </div>

                <!-- Kategori Produk -->
                <div>
                    <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2" id="kategori-wrapper" data-categories='@json($categories->pluck("name"))'>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="kategori" 
                                name="kategori" 
                                value="{{ old('kategori') }}" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors"
                                placeholder="Pilih atau ketik kategori (bisa kategori baru)"
                                autocomplete="off"
                            >
                            <div id="kategori-suggestions" class="absolute z-20 mt-1 w-full bg-white border border-orange-100 rounded-xl shadow-sm hidden max-h-56 overflow-auto"></div>
                        </div>
                        <div class="flex flex-wrap gap-2" id="kategori-chips"></div>
                        <small class="text-gray-500 block">Klik chip di atas atau ketik untuk mencari/menambah kategori baru.</small>
                    </div>
                </div>
                {{-- 
                    Catatan: 
                    - Input kategori di sini hanya berupa form input biasa.
                    - Tombol "+" sebelumnya hanya menampilkan alert, tidak ada proses penambahan kategori sungguhan.
                    - Jika ingin fitur tambah kategori dinamis, harus ada mekanisme backend atau frontend (misal: select2 + ajax ke database kategori).
                    - Jika hanya pakai input teks, tidak perlu tombol "+".
                --}}

                <!-- Harga -->
                <div>
                    <label for="harga" class="block text-sm font-semibold text-gray-700 mb-2">
                        Harga <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="harga" 
                        name="harga" 
                        value="{{ old('harga') }}" 
                        min="0" 
                        step="1" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors"
                        placeholder="Masukkan harga"
                    >
                    <p class="mt-2 text-sm text-gray-500">Masukkan harga dalam rupiah (tanpa titik atau koma)</p>
                </div>

                <!-- Stok -->
                <div>
                    <label for="stok" class="block text-sm font-semibold text-gray-700 mb-2">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="stok" 
                        name="stok" 
                        value="{{ old('stok', 0) }}" 
                        min="0" 
                        step="1" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors"
                        placeholder="Masukkan jumlah stok"
                    >
                    <p class="mt-2 text-sm text-gray-500">Masukkan jumlah stok produk yang tersedia</p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea 
                        id="deskripsi" 
                        name="deskripsi" 
                        rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-2xl focus:outline-none focus:border-orange-500 transition-colors resize-y"
                        placeholder="Deskripsi produk..."
                    >{{ old('deskripsi') }}</textarea>
                </div>

                <!-- Foto -->
                <div>
                    <label for="foto" class="block text-sm font-semibold text-gray-700 mb-2">
                        Foto Produk
                    </label>
                    <input 
                        type="file" 
                        id="foto" 
                        name="foto" 
                        accept="image/*" 
                        onchange="previewImage(this)"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer"
                    >
                    <p class="mt-2 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                    <div id="preview" class="mt-4 hidden">
                        <img id="preview-img" src="" alt="Preview" class="max-w-xs rounded-lg border-2 border-gray-200">
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-4">
                    <a href="{{ route('products.index') }}" class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-full font-semibold text-center hover:bg-gray-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 px-6 py-3 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition-colors">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Utility sederhana untuk UI kategori yang lebih ramah
        (function initKategoriUI() {
            const wrapper = document.getElementById('kategori-wrapper');
            if (!wrapper) return;

            const input = document.getElementById('kategori');
            const suggestionsBox = document.getElementById('kategori-suggestions');
            const chipsContainer = document.getElementById('kategori-chips');
            const categories = JSON.parse(wrapper.dataset.categories || '[]');

            const renderChips = () => {
                chipsContainer.innerHTML = '';
                categories.slice(0, 20).forEach((cat) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'px-3 py-1.5 rounded-full text-sm border border-orange-200 text-gray-800 bg-orange-50 hover:border-orange-400 transition';
                    btn.textContent = cat;
                    btn.onclick = () => {
                        input.value = cat;
                        hideSuggestions();
                    };
                    chipsContainer.appendChild(btn);
                });
            };

            const showSuggestions = (items) => {
                if (!items.length) {
                    hideSuggestions();
                    return;
                }
                suggestionsBox.innerHTML = items
                    .map((item) => `<button type="button" class="block w-full text-left px-4 py-2 hover:bg-orange-50">${item}</button>`)
                    .join('');
                Array.from(suggestionsBox.children).forEach((btn) => {
                    btn.onclick = () => {
                        input.value = btn.textContent.trim();
                        hideSuggestions();
                    };
                });
                suggestionsBox.classList.remove('hidden');
            };

            const hideSuggestions = () => {
                suggestionsBox.classList.add('hidden');
            };

            input.addEventListener('input', (e) => {
                const q = e.target.value.toLowerCase().trim();
                if (!q) {
                    hideSuggestions();
                    return;
                }
                const filtered = categories.filter((cat) => cat.toLowerCase().includes(q)).slice(0, 8);
                showSuggestions(filtered);
            });

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) hideSuggestions();
            });

            renderChips();
        })();

        function previewImage(input) {
            const preview = document.getElementById('preview');
            const previewImg = document.getElementById('preview-img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
