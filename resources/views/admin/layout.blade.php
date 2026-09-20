<!DOCTYPE html>
<html lang="id">

<!--
Color Palette yang lebih serasi & enak dilihat:
- Coklat tua utama:    #94451D (primary brown, lebih soft)
- Oranye pastel:       #FFE5CB (sidebar & accent bg)
- Krem:                #FFF8F3 (body bg)
- Hijau sukses:        #4CAF5B
- Merah error:         #F54646
- Abu menu hover:      #FFC49B (hover bg)
- Abu menu selected:   #FFB183 (selected bg)
-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - MySembako</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --primary-brown: #94451D;
            --sidebar-bg: #FFE5CB;
            --sidebar-hover: #FFC49B;
            --sidebar-selected: #FFB183;
            --body-bg: #FFF8F3;
            --success-text: #4CAF5B;
            --error-text: #F54646;
        }
        body {
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            color: var(--primary-brown);
            background: var(--body-bg);
        }
        .text-primary-brown {
            color: var(--primary-brown) !important;
        }
        .bg-primary-brown {
            background-color: var(--primary-brown) !important;
        }
        /* Sidebar color override */
        aside,
        aside nav a,
        aside div,
        aside p,
        aside form button {
            color: var(--primary-brown) !important;
        }
        aside {
            background: var(--sidebar-bg) !important;
        }
        aside svg {
            color: var(--primary-brown);
            stroke: var(--primary-brown);
        }
        /* Active sidebar menu */
        aside nav a.bg-gray-700, aside nav a.active {
            background-color: var(--sidebar-selected) !important;
            color: var(--primary-brown) !important;
        }
        /* Sidebar menu hover */
        aside nav a:hover {
            background-color: var(--sidebar-hover) !important;
            color: var(--primary-brown) !important;
        }
        /* Logout button keeps white text on red */
        aside form button {
            color: #fff !important;
        }
        /* Success and error alert colors yang soft */
        .alert-success {
            color: var(--success-text) !important;
        }
        .alert-error {
            color: var(--error-text) !important;
        }
        /* Panel main content background */
        main {
            background: #FFFDFB !important;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Sidebar -->
    <div class="flex">
        <aside class="w-64 min-h-screen relative" style="background: var(--sidebar-bg); color: var(--primary-brown);">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-8">
                    <img src="/icon/Logo_Mysembako.svg" alt="Logo" class="h-8" onerror="this.style.display='none'">
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="var(--primary-brown)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.users*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="var(--primary-brown)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Kelola User
                    </a>
                    <a href="{{ route('admin.products') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.products*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="var(--primary-brown)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Kelola Produk
                    </a>
                    <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.orders*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="var(--primary-brown)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Kelola Pesanan
                    </a>
                    <a href="{{ route('admin.activity-logs') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.activity-logs*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="var(--primary-brown)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Activity Logs
                    </a>
                </nav>
            </div>
            <div class="absolute bottom-0 w-64 p-6 border-t" style="border-color: var(--sidebar-hover)">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold" style="color: var(--primary-brown);">{{ Auth::user()->name }}</p>
                        <p class="text-xs" style="color: var(--primary-brown);">{{ Auth::user()->role->name ?? 'Admin' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-[#F54646] text-white rounded-lg hover:bg-[#C63C3C] transition-colors text-sm">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8" style="color: var(--primary-brown); background: #FFFDFB;">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg alert-error">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
