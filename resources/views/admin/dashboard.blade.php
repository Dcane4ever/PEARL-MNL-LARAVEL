<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pearl-user-id" content="{{ auth()->id() }}">
    <meta name="pearl-user-admin" content="{{ auth()->check() && auth()->user()->is_admin ? '1' : '0' }}">
    <title>Admin Dashboard - The Pearl Manila Hotel</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/test.css') }}">
    @vite(['resources/js/app.js'])
</head>
<body>
    <nav class="admin-navbar">
        <div class="container admin-nav-container">
            <div class="admin-logo">
                <img src="{{ asset('image/PearlMNL_LOGO.png') }}" alt="Pearl Manila">
                Admin Console
            </div>
            <ul class="admin-nav-menu">
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
            <div class="admin-card admin-welcome">
                <div class="admin-welcome-content">
                    <div>
                        <p class="admin-kicker">The Pearl Manila Hotel</p>
                        <h2>Welcome back, Admin</h2>
                        <p class="admin-lead">Oversee reservations, coordinate room availability, and keep every stay on schedule. Use the console to manage daily operations and admin access.</p>
                        <div class="admin-welcome-actions">
                            <button class="btn btn-primary" type="button" data-modal-open="admin-start">Get Started</button>
                            <a class="btn btn-outline" href="{{ route('admin.operations') }}">Go to Operations</a>
                        </div>
                    </div>
                    <div class="admin-welcome-panel">
                        <div>
                            <h3>Today at a glance</h3>
                            <p>Review requests, confirm stays, and adjust inventory in minutes.</p>
                        </div>
                        <div class="admin-welcome-highlights">
                            <div>
                                <span class="highlight-label">Bookings</span>
                                <span class="highlight-value">Calendar View</span>
                            </div>
                            <div>
                                <span class="highlight-label">Inventory</span>
                                <span class="highlight-value">15th & 16th floor</span>
                            </div>
                            <div>
                                <span class="highlight-label">Admin Access</span>
                                <span class="highlight-value">Managed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="admin-modal" id="admin-start" aria-hidden="true">
        <div class="admin-modal-backdrop" data-modal-close="admin-start"></div>
        <div class="admin-modal-card" role="dialog" aria-modal="true" aria-labelledby="admin-start-title">
            <div class="admin-modal-header">
                <div>
                    <p class="admin-kicker">Get Started</p>
                    <h3 id="admin-start-title">Choose your admin workspace</h3>
                </div>
                <button class="admin-modal-close" type="button" data-modal-close="admin-start">&times;</button>
            </div>
            <div class="admin-modal-options">
                <a class="admin-modal-option" href="{{ route('admin.operations') }}">
                    <h4>Booking &amp; Inventory</h4>
                    <p>Approve requests, cancel bookings, and set floor availability.</p>
                </a>
                <a class="admin-modal-option" href="{{ route('admin.users') }}">
                    <h4>Admin Management</h4>
                    <p>Add new admins and keep access centralized.</p>
                </a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/test.js') }}"></script>
    <script>
        const openButtons = document.querySelectorAll('[data-modal-open]');
        const closeButtons = document.querySelectorAll('[data-modal-close]');

        openButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-modal-open');
                const modal = document.getElementById(target);
                if (modal) {
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                }
            });
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-modal-close');
                const modal = document.getElementById(target);
                if (modal) {
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                }
            });
        });

        // Clear booking confirmation localStorage on logout
        const logoutForms = document.querySelectorAll('form[action*="logout"]');
        logoutForms.forEach(form => {
            form.addEventListener('submit', () => {
                localStorage.removeItem('acknowledgedBookings');
            });
        });

        window.PearlLiveRefresh = (payload = {}) => {
            if (payload?.scope === 'poll') {
                return;
            }
            window.location.reload();
        };

        window.dispatchEvent(new Event('pearl:live-ready'));
    </script>
</body>
</html>
