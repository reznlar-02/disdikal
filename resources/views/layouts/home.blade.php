<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Umum */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: url('{{ asset('assets/images/kapal.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: rgba(5, 75, 150, 0.9);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
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
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
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

        /* Layout Section */
        section {
            margin-top: 80px;
            padding: 40px 0;
        }

        .main-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
            background: rgba(10, 9, 9, 0.318);
            border-radius: 4px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .main-content h1 {
            font-size: 2.3rem;
            color: #ffffff;
            margin-bottom: 30px;
        }

        .menu-links a {
            display: inline-block;
            margin: 15px;
            padding: 15px 25px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .menu-links a:hover {
            background-color: #2980b9;
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        /* Footer */
        footer {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
            margin: auto;
            width: 80%;
        }

        .footer-box {
            background-color: rgba(10, 9, 9, 0.419);
            color: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 600px;
        }

        .footer-box p {
            margin: 0;
            font-size: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                padding: 30px;
            }

            .menu-links a {
                font-size: 1rem;
                padding: 12px 22px;
            }

            .login-btn {
                top: 50%;
                right: 15px;
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid d-flex justify-content-center">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('assets/images/Group 1.png') }}" alt="" width="60" height="60">
                E-Questioner DISDIKAL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNavAltMarkup">
                <div class="navbar-nav text-center">
                    <!-- Menu Navbar tidak perlu di sini -->
                </div>
            </div>
        </div>
        <!-- Login Button in Navbar -->
        <a href="{{ url('login') }}" class="login-btn">Login</a>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="footer-box">
            <p>Alamat :</p>
            <p>Gedung B3 Sutedi Sena Putra, Lantai 8-9</p>
            <p>Mabes TNI AL, Cilangkap Jakarta Timur</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @include('partials.flash-toast')

    @yield('script')
</body>

</html>
