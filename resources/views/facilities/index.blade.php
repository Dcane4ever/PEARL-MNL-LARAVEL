<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities - The Pearl Manila Hotel</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/facilities.css') }}">
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

    <header class="hero facilities-hero reveal-on-scroll">
        <div class="container">
            <div class="hero-content">
                <h1>Facilities</h1>
                <p>Relax by the rooftop pool, host memorable events in our function rooms, and enjoy thoughtful amenities designed for both leisure and business stays.</p>
            </div>
        </div>
    </header>

    <section class="about reveal-on-scroll">
        <div class="container about-grid">
            <div class="about-images">
                <div class="about-overlay">
                    <p>"From sunrise swims to elegant banquets, The Pearl Manila Hotel offers versatile spaces with stunning city and bay views."</p>
                </div>
            </div>
            <div class="about-content">
                <div class="section-header" style="text-align: left; margin-bottom: 1rem;">
                    <h4>OUR FACILITIES</h4>
                    <h2>Relax, Meet, and Celebrate</h2>
                </div>
                <p>Whether you're unwinding after a busy day or hosting an important event, our facilities are crafted to meet your needs—from the refreshing rooftop pool to flexible function rooms and convenient support services.</p>
                
                <div class="amenities-list">
                    <div class="amenity"><i class="fas fa-water"></i> Rooftop pool with city views</div>
                    <div class="amenity"><i class="fas fa-users"></i> Multiple function &amp; meeting rooms</div>
                    <div class="amenity"><i class="fas fa-parking"></i> Multilevel parking</div>
                    <div class="amenity"><i class="fas fa-wifi"></i> Complimentary Wi‑Fi in public areas</div>
                    <div class="amenity"><i class="fas fa-mug-hot"></i> Food &amp; beverage service for events</div>
                    <div class="amenity"><i class="fas fa-briefcase"></i> Business support on request</div>
                </div>
            </div>
        </div>
    </section>

    <section class="facilities-section reveal-on-scroll">
        <div class="container">
            <div class="section-header">
                <h4>EVENT &amp; MEETING SPACES</h4>
                <h2>Function Rooms &amp; Venues</h2>
                <p>Adaptable venues for seminars, parties, and corporate functions.</p>
            </div>

            <div class="pricing-grid">
                <div class="price-card">
                    <img src="{{ asset('image/Amenities (23).jpg') }}" alt="Pool Deck">
                    <div class="price-content">
                        <h3>Pool Deck</h3>
                        <p>Open‑air venue ideal for socials, cocktails, and intimate celebrations with a panoramic view of Manila.</p>
                        <ul>
                            <li>Perfect for receptions &amp; private parties</li>
                            <li>Scenic skyline and bay views</li>
                        </ul>
                    </div>
                </div>

                <div class="price-card">
                    <img src="{{ asset('image/Amenities (18).jpg') }}" alt="Function Rooms">
                    <div class="price-content">
                        <h3>Function Rooms</h3>
                        <p>Configurable rooms that can be arranged for classroom, theater, or banquet‑style events.</p>
                        <ul>
                            <li>Ideal for meetings, conferences, and trainings</li>
                            <li>Audio‑visual support available on request</li>
                        </ul>
                    </div>
                </div>

                <div class="price-card">
                    <img src="{{ asset('image/Amenities (15).jpg') }}" alt="Banquet Setups">
                    <div class="price-content">
                        <h3>Banquet Setups</h3>
                        <p>Elegant table arrangements and décor options to match weddings, debuts, and special occasions.</p>
                        <ul>
                            <li>Customizable themes &amp; menus</li>
                            <li>Dedicated events team support</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="gallery reveal-on-scroll">
        <div class="container">
            <div class="section-header">
                <h4>FACILITIES GALLERY</h4>
                <h2>Scenes Around The Pearl</h2>
                <p>A glimpse of our pool deck and function spaces.</p>
            </div>

            <div class="gallery-tabs">
                <span class="tab-link active" onclick="openTab(event, 'pool')">POOL AREA</span>
                <span class="tab-link" onclick="openTab(event, 'function')">FUNCTION ROOMS</span>
            </div>

            <!-- POOL CAROUSEL -->
            <div class="carousel active" data-tab="pool">
                <div class="carousel-window">
                    <div class="carousel-track">
                        <div class="carousel-slide">
                            <img src="{{ asset('image/Amenities (17).jpg') }}" alt="Pool 1">
                            <img src="{{ asset('image/Amenities (21).jpg') }}" alt="Pool 2">
                            <img src="{{ asset('image/Amenities (24).jpg') }}" alt="Pool 3">
                            <img src="{{ asset('image/Amenities (23).jpg') }}" alt="Pool 4">
                        </div>
                        <div class="carousel-slide">
                            <img src="{{ asset('image/pool--v13414958.jpg') }}" alt="Pool 5">
                            <img src="{{ asset('image/pool1.jpg') }}" alt="Pool 6">
                            <img src="{{ asset('image/pool2.jpg') }}" alt="Pool 7">
                            <img src="{{ asset('image/pool3.jpg') }}" alt="Pool 8">
                        </div>
                        <div class="carousel-slide">
                            <img src="{{ asset('image/pool4.jpg') }}" alt="Pool 9">
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

            <!-- FUNCTION ROOM CAROUSEL -->
            <div class="carousel" data-tab="function">
                <div class="carousel-window">
                    <div class="carousel-track">
                        <div class="carousel-slide">
                            <img src="{{ asset('image/Amenities (20).jpg') }}" alt="Function 1">
                            <img src="{{ asset('image/Amenities (18).jpg') }}" alt="Function 2">
                            <img src="{{ asset('image/Amenities (15).jpg') }}" alt="Function 3">
                            <img src="{{ asset('image/Amenities (2).jpg') }}" alt="Function 4">
                        </div>
                        <div class="carousel-slide">
                            <img src="{{ asset('image/FUNCTION 4.jpg') }}" alt="Function Lounge">
                            <img src="{{ asset('image/FUNCTION1.jpg') }}" alt="Function Banquet">
                            <img src="{{ asset('image/FUNCTION2.jpg') }}" alt="Function Dining">
                            <img src="{{ asset('image/FUNCTION3.jpg') }}" alt="Function Gym">
                        </div>  
                        <div class="carousel-slide">
                            <img src="{{ asset('image/FUNCTION5.jpg') }}" alt="Function Ballroom">
                            <img src="{{ asset('image/FUNCTION6.jpg') }}" alt="Function Fitness">
                             <img src="{{ asset('image/FUNCTION7.jpg') }}" alt="Function Fitness">
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
                <h2>Plan Your Next Event at The Pearl Manila Hotel</h2>
                <p>Coordinate with our team to reserve the perfect venue for your occasion.</p>
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

<script src="{{ asset('js/facilities.js') }}"></script>
</body>
</html>
