<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pearl-user-id" content="{{ auth()->id() }}">
    <meta name="pearl-user-admin" content="{{ auth()->check() && auth()->user()->is_admin ? '1' : '0' }}">
    <title>Check In - The Pearl Manila Hotel</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/checkin.css') }}">
    @vite(['resources/js/app.js'])
   
</head>
<body>

    <nav class="navbar reveal-on-scroll">
        <div class="container nav-container">
            <div class="logo">
                <img src="{{ asset('image/PearlMNL_LOGO.png') }}" alt="Pearl Manila">
                The Pearl Manila
            </div>
            <ul class="nav-menu">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/rooms') }}">Rooms</a></li>
                <li><a href="{{ url('/facilities') }}">Facilities</a></li>
                <li><a href="{{ auth()->check() ? route('rooms.booking') : url('/checkin') }}">Check In</a></li>
                @auth
                    <li><a href="{{ route('rooms.history') }}">Booking History</a></li>
                @endauth
                
                <li>
                    <button class="theme-toggle" type="button" aria-label="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                        <span class="theme-toggle-label">Dark</span>
                    </button>
                </li>
                @auth
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-primary" type="submit">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-login"><a href="{{ route('login') }}">Login</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <header class="hero checkin-hero reveal-on-scroll">
        <div class="container">
            <div class="hero-content">
                <h1>Check In</h1>
                <p>Plan your arrival with ease. Book online or contact our reservations team. Standard check-in is 2:00 PM; check-out is 12:00 PM.</p>
                <a href="{{ route('login') }}" class="btn btn-primary">Log in to Book</a>
            </div>
        </div>
    </header>

    <section class="about reveal-on-scroll">
        <div class="container about-grid">
            <div>
                <div class="section-header" style="text-align:left; margin-bottom:1rem;">
                    <h4>PLAN YOUR ARRIVAL</h4>
                    <h2>Before You Check In</h2>
                </div>
                <p>Whether you are a business traveler, student, or family on vacation, we want your arrival to be smooth and stress-free. Prepare your details in advance and our front desk team will take care of the rest.</p>

                <div class="info-list">
                    <p><strong>Standard Check-In:</strong> 2:00 PM</p>
                    <p><strong>Standard Check-Out:</strong> 12:00 PM</p>
                    <p><strong>Early Check-In / Late Check-Out:</strong> Subject to availability and applicable fees.</p>
                </div>
            </div>
            <div class="about-highlight">
                <h3>What to Bring</h3>
                <ul>
                    <li>Valid government-issued ID</li>
                    <li>Booking confirmation (printed or on your phone)</li>
                    <li>Payment method for incidentals</li>
                    <li>Special requests (early check-in, extra bed, etc.) to be noted in advance</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="steps-section reveal-on-scroll">
        <div class="container">
            <div class="section-header">
                <h4>HOW IT WORKS</h4>
                <h2>Check-In in Four Simple Steps</h2>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">STEP 01</div>
                    <h3>Reserve</h3>
                    <p>Log in to your account to secure your booking, or email <strong>reservation@pearlmanila.com.ph</strong> for assistance.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">STEP 02</div>
                    <h3>Confirm</h3>
                    <p>Receive your booking confirmation with room inclusions, dates, and payment details.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">STEP 03</div>
                    <h3>Arrive</h3>
                    <p>Present your valid ID and confirmation at the front desk. Early check-in is subject to room availability.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">STEP 04</div>
                    <h3>Enjoy Your Stay</h3>
                    <p>Settle into your room, visit the pool, dine nearby, and take in Manila Bay sunsets.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-banner reveal-on-scroll">
        <div class="container cta-content">
            <div class="cta-text">
                <h2>Ready to Check In?</h2>
                <p>Reserve your stay now and experience Manila's Pearl of the Bay.</p>
            </div>
            <a href="{{ route('login') }}" class="btn" style="background:white; color:var(--primary-blue);">Get Started</a>
        </div>
    </section>

    <footer class="footer reveal-on-scroll">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3>The Pearl Manila Hotel</h3>
                    <p>Manila's Pearl of The Bay. Discover comfort and impeccable service in the heart of Manila.</p>
                </div>
                <div>
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ url('/rooms') }}">Rooms</a></li>
                        <li><a href="{{ url('/facilities') }}">Facilities</a></li>
                        <li><a href="{{ auth()->check() ? route('rooms.booking') : url('/checkin') }}">Check In</a></li>
                    </ul>
                </div>
                <div>
                    <h3>Contact</h3>
                    <ul>
                        <li><i class="fas fa-phone"></i> (02) 8400 0088 / 0961 789 2662</li>
                        <li><i class="fas fa-envelope"></i> reservation@pearlmanila.com.ph</li>
                        <li><i class="fas fa-envelope"></i> sales@pearlmanila.com.ph</li>
                        <li><i class="fas fa-map-marker-alt"></i> 1155 Gen. Luna St. corner Taft &amp; UN Ave., Ermita, Manila</li>
                    </ul>
                </div>
                <div>
                    <h3>Follow Us</h3>
                    <div style="display: flex; gap: 15px; font-size: 1.2rem;">
                        <a href="https://www.facebook.com/ThePearlManila/" target="_blank" rel="noreferrer noopener"><i class="fab fa-facebook"></i></a>
                        <a href="https://www.instagram.com/thepearlmanilahotel/?hl=en" target="_blank" rel="noreferrer noopener"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 The Pearl Manila Hotel. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

<script src="{{ asset('js/checkin.js') }}"></script>
<script>
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
