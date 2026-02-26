<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management - The Pearl Manila Hotel</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/test.css') }}">
</head>
<body>
    <nav class="admin-navbar">
        <div class="container admin-nav-container">
            <div class="admin-logo">
                <img src="{{ asset('image/PearlMNL_LOGO.png') }}" alt="Pearl Manila">
                Admin Console
            </div>
            <ul class="admin-nav-menu">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.operations') }}">Booking &amp; Inventory</a></li>
                <li><a href="{{ route('admin.users') }}">Admin Management</a></li>
                <li>
                    <button class="theme-toggle" type="button" aria-label="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                        <span class="theme-toggle-label">Dark</span>
                    </button>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <section class="auth-page admin-page">
        <div class="container admin-container">
            <div class="admin-card">
                <div class="admin-hero">
                    <div>
                        <h2>Admin Management</h2>
                        <p>Create and onboard additional administrators.</p>
                    </div>
                </div>

                @if (session('admin_created'))
                    <div class="alert alert-success">{{ session('admin_created') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="admin-layout single-panel">
                    <div class="admin-main">
                        <div class="admin-panel">
                            <h3>Create Another Admin</h3>
                            <p>Add a new admin user for the dashboard.</p>
                            <form class="auth-form" method="POST" action="{{ route('admin.users.store') }}" onsubmit="return confirm('Are you sure you want to create this admin account?');">
                                @csrf
                                <div>
                                    <label for="admin_name">Name</label>
                                    <input id="admin_name" name="name" type="text" value="{{ old('name') }}" required>
                                </div>

                                <div>
                                    <label for="admin_email">Email</label>
                                    <input id="admin_email" name="email" type="email" value="{{ old('email') }}" required>
                                </div>

                                <div>
                                    <label for="admin_password">Password</label>
                                    <input id="admin_password" name="password" type="password" required>
                                </div>

                                <div>
                                    <label for="admin_password_confirmation">Confirm Password</label>
                                    <input id="admin_password_confirmation" name="password_confirmation" type="password" required>
                                </div>

                                 <div class="auth-actions">
                                     <span></span>
                                     <button class="btn btn-primary" type="submit">Create Admin</button>
                                 </div>
                             </form>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </section>

    <script src="{{ asset('js/test.js') }}"></script>
    <script>
        // Clear booking confirmation localStorage on logout
        const logoutForms = document.querySelectorAll('form[action*="logout"]');
        logoutForms.forEach(form => {
            form.addEventListener('submit', () => {
                localStorage.removeItem('acknowledgedBookings');
            });
        });
    </script>
</body>
</html>
