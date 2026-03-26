<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MediTrack - Healthcare Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --light-bg: #f8fafc;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: #333;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: white;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 80px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: #555;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: var(--light-bg);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 600;
        }

        .sidebar-menu i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 70px;
            padding: 2rem;
            min-height: 100vh;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 1.5rem;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #556cd6 0%, #6b3a8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .badge-status {
            padding: 0.5rem 0.875rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-scheduled { background-color: #dbeafe; color: #1e40af; }
        .badge-confirmed { background-color: #dcfce7; color: #166534; }
        .badge-completed { background-color: #e0e7ff; color: #4f46e5; }
        .badge-cancelled { background-color: #fee2e2; color: #991b1b; }

        .stat-card {
            text-align: center;
            padding: 2rem;
            border-radius: 0.75rem;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #666;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .table th {
            background-color: var(--light-bg);
            font-weight: 600;
            color: #333;
            border: none;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
            border-color: #e5e7eb;
        }

        .form-control, .form-select {
            border: 1px solid #e5e7eb;
            padding: 0.6rem 0.875rem;
            border-radius: 0.5rem;
            transition: border-color 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .alert {
            border: none;
            border-radius: 0.75rem;
        }

        .modal-content {
            border-radius: 0.75rem;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
        }

        footer {
            background: #1f2937;
            color: white;
            padding: 2rem;
            text-align: center;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @yield('extra_css')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-hospital"></i> MediTrack
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-white">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name ?? 'User' }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid" style="margin-top: 70px;">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar">
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) active @endif">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    @if(Auth::user()->role === 'admin')
                        <li><hr class="my-2"></li>
                        <li><a href="#" class="text-muted disabled" style="cursor: default;"><strong class="ps-3">Admin Panel</strong></a></li>
                        <li>
                            <a href="{{ route('patients.index') }}">
                                <i class="bi bi-people"></i> Kelola Pasien
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('doctors.index') }}">
                                <i class="bi bi-person-badge"></i> Kelola Dokter
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('appointments.index') }}">
                                <i class="bi bi-calendar-check"></i> Semua Appointment
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->role === 'patient')
                        <li><hr class="my-2"></li>
                        <li><a href="#" class="text-muted disabled" style="cursor: default;"><strong class="ps-3">Pasien</strong></a></li>
                        <li>
                            <a href="{{ route('patient.appointments') }}">
                                <i class="bi bi-calendar-event"></i> Appointment Saya
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('patient.prescriptions') }}">
                                <i class="bi bi-capsule"></i> Resep Saya
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('patient.health-record') }}">
                                <i class="bi bi-file-medical"></i> Riwayat Kesehatan
                            </a>
                        </li>
                    @endif

                   

                    @if(Auth::user()->role === 'pharmacist')
                        <li><hr class="my-2"></li>
                        <li><a href="#" class="text-muted disabled" style="cursor: default;"><strong class="ps-3">Farmasi</strong></a></li>
                        <li>
                            <a href="{{ route('pharmacy.inventory') }}">
                                <i class="bi bi-box-seam"></i> Inventory
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pharmacy.orders') }}">
                                <i class="bi bi-bag-check"></i> Pesanan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pharmacy.low-stock') }}">
                                <i class="bi bi-exclamation-triangle"></i> Stok Rendah
                            </a>
                        </li>
                    @endif

                    <li><hr class="my-2"></li>
                    <li>
                        <a href="#">
                            <i class="bi bi-question-circle"></i> Bantuan
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> Terjadi kesalahan:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 MediTrack Healthcare Management System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('extra_js')
</body>
</html>
