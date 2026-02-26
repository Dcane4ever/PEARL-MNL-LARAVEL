<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms &amp; Suites - The Pearl Manila Hotel</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/rooms.css') }}">
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

    <header class="hero rooms-hero reveal-on-scroll">
        <div class="container">
            <div class="hero-content">
                <h1>Rooms &amp; Suites</h1>
                <p>Comfortable, well-appointed rooms with city and bay views, designed for business travelers, students, and families.</p>
            </div>
        </div>
    </header>

    <section class="about reveal-on-scroll">
        <div class="container about-grid">
            <div class="about-images">
                <div class="about-overlay">
                    <p>"From smart Deluxe rooms to spacious Suites, every stay includes warm service and essential comforts."</p>
                </div>
            </div>
            <div class="about-content">
                <div class="section-header" style="text-align: left; margin-bottom: 1rem;">
                    <h4>OUR ACCOMMODATIONS</h4>
                    <h2>Stay in Comfort</h2>
                </div>
                <p>Choose between our Superior King room or spacious Junior Suite—both featuring luxury amenities, premium service, and the finest comforts for an unforgettable stay in Manila.</p>

                <div class="amenities-list">
                    <div class="amenity"><i class="fas fa-bed"></i> Plush beds with fresh linens</div>
                    <div class="amenity"><i class="fas fa-wifi"></i> Free Wi‑Fi</div>
                    <div class="amenity"><i class="fas fa-tv"></i> Cable TV &amp; mini‑refrigerator</div>
                    <div class="amenity"><i class="fas fa-shower"></i> Private bathroom with hot &amp; cold shower</div>
                    <div class="amenity"><i class="fas fa-city"></i> City and bay view options</div>
                    <div class="amenity"><i class="fas fa-lock"></i> In‑room safety features</div>
                </div>
            </div>
        </div>
    </section>

    <section class="rooms-section reveal-on-scroll">
        <div class="container">
            <div class="section-header">
                <h4>ROOM TYPES</h4>
                <h2>Find the Right Room</h2>
                <p>Two premium categories featuring our finest accommodations.</p>
            </div>

            <div class="rooms-grid">
                <div class="room-card">
                    <img src="{{ asset('image/superior-king1.jpg') }}" alt="Superior King">
                    <div class="room-content">
                        <h3>Superior King</h3>
                        <div class="room-rate">From P3,645.00</div>
                        <span class="room-sub">Per night, room only</span>
                        <p class="room-desc">Elegant king-size room with premium linens, modern amenities, and stunning bay views. Perfect for guests seeking luxury and comfort.</p>
                    </div>
                </div>

                <div class="room-card">
                    <img src="{{ asset('image/juniorsuite1.jpg') }}" alt="Junior Suite">
                    <div class="room-content">
                        <h3>Junior Suite</h3>
                        <div class="room-rate">From P5,220.00</div>
                        <span class="room-sub">Per night, room only</span>
                        <p class="room-desc">Spacious suite with separate living area, premium furnishings, and exclusive amenities. Ideal for extended stays and special occasions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="gallery reveal-on-scroll">
        <div class="container">
            <div class="section-header">
                <h4>ROOM GALLERY</h4>
                <h2>A Look Inside</h2>
                <p>Preview a selection of our rooms and suites.</p>
            </div>

            <div class="gallery-tabs">
                <span class="tab-link active" onclick="openTab(event, 'rooms-standard')">ROOMS</span>
                <span class="tab-link" onclick="openTab(event, 'rooms-suite')">SUITES</span>
            </div>

            <!-- ROOMS CAROUSEL -->
            <div class="carousel active" data-tab="rooms-standard">
                <div class="carousel-window">
                    <div class="carousel-track">
                        <div class="carousel-slide">
                            <img src="{{ asset('image/room (6).jpg') }}" alt="Room 1">
                            <img src="{{ asset('image/room (9).jpg') }}" alt="Room 2">
                            <img src="{{ asset('image/room (7).jpg') }}" alt="Room 3">
                            <img src="{{ asset('image/room (8).jpg') }}" alt="Room 4">
                        </div>
                        <div class="carousel-slide">
                            <img src="{{ asset('image/room (11).jpg') }}" alt="Room 5">
                            <img src="{{ asset('image/room (6).jpg') }}" alt="Room 6">
                            <img src="{{ asset('image/room (9).jpg') }}" alt="Room 7">
                            <img src="{{ asset('image/room (7).jpg') }}" alt="Room 8">
                        </div>
                    </div>
                </div>
                <button class="carousel-arrow prev" type="button" aria-label="Previous slide">&#10094;</button>
                <button class="carousel-arrow next" type="button" aria-label="Next slide">&#10095;</button>
                <div class="carousel-dots" aria-hidden="true">
                    <button class="carousel-dot is-active" type="button"></button>
                    <button class="carousel-dot" type="button"></button>
                </div>
            </div>

            <!-- SUITES CAROUSEL -->
            <div class="carousel" data-tab="rooms-suite">
                <div class="carousel-window">
                    <div class="carousel-track">
                        <div class="carousel-slide">
                            <img src="{{ asset('image/room (10).jpg') }}" alt="Suite 1">
                            <img src="{{ asset('image/room (12).jpg') }}" alt="Suite 2">
                            <img src="{{ asset('image/room (8).jpg') }}" alt="Suite 3">
                            <img src="{{ asset('image/room (9).jpg') }}" alt="Suite 4">
                        </div>
                        <div class="carousel-slide">
                            <img src="{{ asset('image/juniorsuite1.jpg') }}" alt="Suite Bedroom">
                            <img src="{{ asset('image/juniorsuite2.jpg') }}" alt="Suite Living">
                            <img src="{{ asset('image/juniorsuite3.jpg') }}" alt="Suite Bathroom">
                            <img src="{{ asset('image/juniorsuite4.jpg') }}" alt="Suite View">
                        </div>
                        <div class="carousel-slide">
                            <img src="{{ asset('image/superior-king1.jpg') }}" alt="Suite Detail 1">
                            <img src="{{ asset('image/superior-king2.jpg') }}" alt="Suite Detail 2">
                             <img src="{{ asset('image/superior-king3jpg.jpg') }}" alt="Suite Detail 1">
                            <img src="{{ asset('image/superior-king4.jpg') }}" alt="Suite Detail 2">
                        </div>
                    </div>
                </div>
                <button class="carousel-arrow prev" type="button" aria-label="Previous slide">&#10094;</button>
                <button class="carousel-arrow next" type="button" aria-label="Next slide">&#10095;</button>
                <div class="carousel-dots" aria-hidden="true">
                    <button class="carousel-dot is-active" type="button"></button>
                    <button class="carousel-dot" type="button"></button>
                    <button class="carousel-dot" type="button"></button>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-banner reveal-on-scroll">
        <div class="container cta-content">
            <div class="cta-text">
                <h2>Book Your Stay at The Pearl Manila Hotel</h2>
                <p>Reserve your preferred room type today and experience Manila’s Pearl of the Bay.</p>
            </div>
            <a href="https://www.booking.com/hotel/ph/pearl-manila.en-gb.html" target="_blank" rel="noreferrer noopener" class="btn" style="background: white; color: var(--primary-blue);">Get Started</a>
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

<script src="{{ asset('js/rooms.js') }}"></script>
</body>
</html>
