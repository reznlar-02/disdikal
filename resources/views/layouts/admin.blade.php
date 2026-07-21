<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS DataTable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <!-- CSS untuk integrasi Bootstrap dengan DataTable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- CSS tambahan untuk meningkatkan tampilan -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <style>
        /* Umum */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            padding-top: 80px; /* Tambah padding agar konten tidak tertutup navbar */
            background: url('{{ asset("assets/images/kapal.jpg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: rgba(5, 75, 150, 0.9);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: fixed; /* Pastikan navbar tetap fixed */
            top: 0;
            width: 100%;
            z-index: 1000; /* Pastikan navbar selalu di atas konten lain */
        }

        .navbar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: bold;
            padding: 10px 20px;
            text-transform: uppercase;
        }

        .navbar-nav .nav-link:hover {
            color: #d1e7ff !important;
        }

        .navbar-toggler {
            background-color: white;
            border-radius: 5px;
        }

        .navbar-toggler-icon {
            filter: invert(1);
        }

        /* Login Button at Navbar */
        .login-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            font-size: 1.1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background-color: #2980b9;
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('admin') }}">
                <img src="{{ asset('assets/images/Group 1.png') }}" alt="" width="60" height="60">
                E-Questioner DISDIKAL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin/strata') }}">Strata</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin/pendidikan') }}">Pendidikan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin/pertanyaansurvei') }}">Pertanyaan
                            Survei</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin/hasilsurvei') }}">Hasil Survei</a></li>
                </ul>
                <a href="{{ url('logout') }}" class="login-btn">Logout</a>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer dihapus -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <!-- JS DataTable -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        // Inisialisasi DataTable
        $(document).ready(function() {
            $('#datatable').DataTable({
                responsive: true, // Aktifkan mode responsif
                paging: true, // Aktifkan paginasi
                searching: true, // Aktifkan fitur pencarian
                ordering: true, // Aktifkan pengurutan
                info: true, // Menampilkan info total baris
                lengthChange: false, // Menampilkan opsi jumlah baris per halaman
                language: {
                    search: "Search:", // Label pencarian
                    lengthMenu: "Show _MENU_ entries", // Opsi jumlah entri
                    info: "Showing _START_ to _END_ of _TOTAL_ entries", // Info tampilan entri
                    paginate: {
                        previous: "Previous",
                        next: "Next"
                    }
                }
            });
        });
    </script>

    @include('partials.flash-toast')
    @include('partials.confirm-delete-modal')

    @yield('script')
</body>

</html>
