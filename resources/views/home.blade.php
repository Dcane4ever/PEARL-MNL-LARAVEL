<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Pearl Manila Hotel - Landing Page Recreation</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/test.css') }}">
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

    <header class="hero reveal-on-scroll">
        <div class="container">
            <div class="hero-content">
                <h1>The Pearl Manila Hotel</h1>
                <p>Manila's Pearl of The Bay. Experience comfort and convenience with plush amenities and stunning city views.</p>
                <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
            </div>
        </div>
    </header>

    <section class="container info-cards-wrapper reveal-on-scroll">
        <div class="info-cards">
            <div class="card-item">
                <h5>01</h5>
                <h3>The Warmest Welcome</h3>
                <p>Experience hospitality like no other as you step into our grand lobby.</p>
            </div>
            <div class="card-item">
                <h5>02</h5>
                <h3>Satisfaction is a Priority</h3>
                <p>We ensure your needs are met with prompt service and care.</p>
            </div>
            <div class="card-item">
                <h5>03</h5>
                <h3>Convenient Location</h3>
                <p>Located in the heart of Manila, close to historical landmarks.</p>
            </div>
            <div class="card-item appointment-card">
                <h3>Make Appointment</h3>
                <p>Easily schedule your stay at The Pearl Manila Hotel by making an appointment today.</p>
                <br>
                <a href="https://l1nk.dev/pearlmanila" class="btn btn-outline" style="border-color: rgba(255,255,255,0.3); font-size: 0.8rem;">Book Now</a>
            </div>
        </div>
    </section>

    <section class="about reveal-on-scroll">
        <div class="container about-grid">
            <div class="about-images">
                <div class="about-overlay">
                    <p>"Experience comfort and convenience at The Pearl Manila Hotel with plush amenities and stunning city views and sea views in every guestroom."</p>
                </div>
            </div>
            <div class="about-content">
                <div class="section-header" style="text-align: left; margin-bottom: 1rem;">
                    <h4>ABOUT COMPANY</h4>
                    <h2>Manila's Pearl of The Bay</h2>
                </div>
                <p>Discover comfort, elegance, and impeccable service at The Pearl Manila Hotel. Located in the heart of Manila, our hotel ensures convenient access and exceptional amenities for an unforgettable stay.</p>
                
                <div class="amenities-list">
                    <div class="amenity"><i class="fas fa-wifi"></i> WiFi</div>
                    <div class="amenity"><i class="fas fa-car"></i> Free Parking</div>
                    <div class="amenity"><i class="fas fa-swimming-pool"></i> Pool</div>
                    <div class="amenity"><i class="fas fa-snowflake"></i> Air Conditioned</div>
                    <div class="amenity"><i class="fas fa-wheelchair"></i> Accessible</div>
                </div>
                <br>
                <a href="https://l1nk.dev/pearlmanila" class="btn btn-primary">Learn More</a>
            </div>
        </div>
    </section>

    <section class="gallery reveal-on-scroll">
    <div class="container">
        <div class="section-header">
            <h4>EXPLORE OUR GALLERY</h4>
            <h2>Gallery</h2>
            <p>Discover the Beauty of The Pearl Manila Hotel</p>
        </div>

        <div class="gallery-tabs">
            <span class="tab-link active" onclick="openTab(event, 'rooms')">ROOMS</span>
            <span class="tab-link" onclick="openTab(event, 'pool')">POOL AREA</span>
            <span class="tab-link" onclick="openTab(event, 'function')">FUNCTION ROOM</span>
        </div>

        <!-- ROOMS CAROUSEL -->
        <div class="carousel active" data-tab="rooms">
            <div class="carousel-window">
                <div class="carousel-track">
                    <div class="carousel-slide">
                        <img src="{{ asset('image/room (6).jpg') }}" alt="Room 1">
                        <img src="{{ asset('image/room (8).jpg') }}" alt="Room 2">
                        <img src="{{ asset('image/room (10).jpg') }}" alt="Room 3">
                        <img src="{{ asset('image/room (9).jpg') }}" alt="Room 4">
                    </div>
                    <div class="carousel-slide">
                        <img src="{{ asset('image/juniorsuite1.jpg') }}" alt="Junior Suite 1">
                        <img src="{{ asset('image/juniorsuite2.jpg') }}" alt="Junior Suite 2">
                        <img src="{{ asset('image/juniorsuite3.jpg') }}" alt="Junior Suite 3">
                        <img src="{{ asset('image/juniorsuite4.jpg') }}" alt="Junior Suite 4">
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

        <!-- POOL CAROUSEL -->
        <div class="carousel" data-tab="pool">
            <div class="carousel-window">
                <div class="carousel-track">
                    <div class="carousel-slide">
                        <img src="{{ asset('image/Amenities (17).jpg') }}" alt="Pool 1">
                        <img src="{{ asset('image/Amenities (23).jpg') }}" alt="Pool 2">
                        <img src="{{ asset('image/Amenities (24).jpg') }}" alt="Pool 3">
                        <img src="{{ asset('image/Amenities (21).jpg') }}" alt="Pool 4">
                    </div>
                    <div class="carousel-slide">
                        <img src="{{ asset('image/pool1.jpg') }}" alt="Pool 5">
                        <img src="{{ asset('image/pool--v13414958.jpg') }}" alt="Pool 6">
                        <img src="{{ asset('image/pool2.jpg') }}" alt="Pool 7">
                        <img src="{{ asset('image/pool3.jpg') }}" alt="Pool 8">
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
                        <img src="{{ asset('image/FUNCTION1.jpg') }}" alt="Function 5">
                        <img src="{{ asset('image/FUNCTION 4.jpg') }}" alt="Function 6">
                        <img src="{{ asset('image/FUNCTION5.jpg') }}" alt="Function 7">
                        <img src="{{ asset('image/FUNCTION3.jpg') }}" alt="Function 8">
                    </div>
                    <div class="carousel-slide">
                        <img src="{{ asset('image/FUNCTION6.jpg') }}" alt="Function 9">
                        <img src="{{ asset('image/FUNCTION7.jpg') }}" alt="Function 10">
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

    <section class="pricing reveal-on-scroll">
        <div class="container">
            <div class="section-header">
                <h4>PRICING PLAN</h4>
                <h2>Choose The Best Pricing</h2>
                <p>Explore Our Pricing Plans and Uncover the Value of The Pearl Manila Hotel</p>
            </div>
            
            <div class="pricing-grid">
                <div class="price-card">
                    <img src="{{ asset('image/room (12).jpg') }}" alt="Superior Room">
                    <div class="price-content">
                        <h3>Superior Room Only</h3>
                        <div class="price-value">P3,645.00</div>
                        <span class="price-sub">Overnight</span>
                        <p class="price-desc">Our classic room with Superior King or Twin beds, perfect for family, friends or colleagues traveling together.</p>
                        <a href="{{ route('register') }}" class="btn btn-primary" style="width:100%; text-align:center;">Get Started</a>
                    </div>
                </div>

                <div class="price-card middle">
                    <img src="{{ asset('image/Amenities (24).jpg') }}" alt="Promo">
                    <div class="price-content">
                        <h3>Greetings!</h3>
                        <p class="price-desc" style="height: auto;">
                            We are pleased to submit our promotional rate that is good for 2 adults and 2 kids below 10 y/o free of charge when sharing room/bed with parents including free parking and use of pool.
                        </p>
                        <a href="{{ route('register') }}" class="btn btn-primary" style="background: #1e3a8a; width:100%; text-align:center;">Get Started</a>
                    </div>
                </div>

                <div class="price-card featured">
                    <img src="{{ asset('image/room (8).jpg') }}" alt="Junior Suite">
                    <div class="price-content">
                        <h3>Junior Suite</h3>
                        <div class="price-value">P5,220.00</div>
                        <span class="price-sub">Overnight</span>
                        <p class="price-desc">The Junior Suite offers a larger, more luxurious option than standard rooms. Includes separate living area.</p>
                        <a href="{{ route('register') }}" class="btn btn-primary" style="background: var(--primary-blue); border:none; width:100%; text-align:center;">Get Started</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials reveal-on-scroll">
        <div class="container">
            <div class="section-header" style="color: white;">
                <h4 style="color: var(--primary-blue);">TESTIMONIALS</h4>
                <h2 style="color: white;">What Guests Say About Us</h2>
            </div>
            <div class="testimonial-grid">
                <div class="testimonial-item">
                    <div class="testi-card">
                        <i class="fas fa-quote-left" style="color: #ddd; font-size: 2rem; margin-bottom: 10px;"></i>
                        <p>"The rooms are nice and clean. The bathroom/toilet however is small. In the morning, they ran out of hot water... Regarding food, fish fillet is always on the menu."</p>
                    </div>
                    <div class="user-icon"><i class="fas fa-user"></i></div>
                    <span class="anonymous-name">Anonymous</span>
                </div>
                <div class="testimonial-item">
                    <div class="testi-card">
                        <i class="fas fa-quote-left" style="color: #ddd; font-size: 2rem; margin-bottom: 10px;"></i>
                        <p>"It was a nice experience to stay in this hotel. I've a great view on the busy city of Manila... The staff are also very helpful and polite. Highly recommend."</p>
                    </div>
                    <div class="user-icon"><i class="fas fa-user"></i></div>
                    <span class="anonymous-name">Anonymous</span>
                </div>
            </div>
        </div>
    </section>

    <section class="location reveal-on-scroll">
        <div class="container location-grid">
            <div class="location-content">
                <div class="section-header" style="text-align: left;">
                    <h4>OUR LOCATION</h4>
                    <h2>Stellar Location</h2>
                </div>
                <p>Pearl Manila Hotel: Your Gateway to Manila's Vibrant Heart. Our hotel in Ermita, Manila is an ideal retreat for students, business executives, and leisure travelers.</p>
                <p><strong>Address:</strong><br>1155 General Luna Street corner Taft and United Nations Avenue, Ermita, 1000, Metro Manila Philippines.</p>
                
                <h5>Nearby Attractions:</h5>
                <ul style="padding-left: 20px; list-style: disc; color: var(--text-gray);">
                    <li>Rizal Park</li>
                    <li>Intramuros</li>
                    <li>National Museum</li>
                </ul>
            </div>
            <div class="map-frame">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.267688229643!2d120.98256331483984!3d14.583852989813136!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397ca264375e381%3A0x6b7280d990426966!2sThe%20Pearl%20Manila%20Hotel!5e0!3m2!1sen!2sph!4v1629876543210!5m2!1sen!2sph" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </section>

    <section class="cta-banner reveal-on-scroll">
        <div class="container cta-content">
            <div class="cta-text">
                <h2>Book Your Stay at The Pearl Manila Hotel</h2>
                <p>Reserve your stay now and experience luxury at The Pearl Manila Hotel.</p>
            </div>
            <a href="https://l1nk.dev/pearlmanila" class="btn" style="background: white; color: var(--primary-blue);">Get Started</a>
        </div>
    </section>

    <footer class="footer reveal-on-scroll">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3>The Pearl Manila Hotel</h3>
                    <p>Manila's Pearl of The Bay. Discover comfort and impeccable service in the heart of Manila.</p>
                    <p>  # eac.edu.ph  </p>
                    <p> #https://manilamed.net.ph/  </p>
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
                        <li><i class="fas fa-phone"></i> (02) 8400 0088 / 0961 789 2662 </li>
                        <li><i class="fas fa-envelope"></i> reservation@pearlmanila.com.ph </li>
                        <li><i class="fas fa-envelope"></i> sales@pearlmanila.com.ph </li>
                        <li><i class="fas fa-map-marker-alt"></i> The Pearl Manila Hotel, 1155 Gen. Luna Street corner Taft and, United Nations Ave, Ermita, Manila, 1000 Metro Manilaa</li>
                    </ul>
                </div>
                <div>
                    <h3>Follow Us</h3>
                    <div style="display: flex; gap: 15px; font-size: 1.2rem;">
                        <a href="https://www.facebook.com/ThePearlManila/"><i class="fab fa-facebook"></i></a>
                        <a href="https://www.instagram.com/thepearlmanilahotel/?hl=en"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 The Pearl Manila Hotel. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    <script src="{{ asset('js/test.js') }}"></script>
</body>
</html>
