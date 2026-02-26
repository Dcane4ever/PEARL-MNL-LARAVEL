// Carousel functionality
(function () {
    function initCarousel(root) {
        var track = root.querySelector('.carousel-track');
        var slides = root.querySelectorAll('.carousel-slide');
        var dots = root.querySelectorAll('.carousel-dot');
        var prev = root.querySelector('.carousel-arrow.prev');
        var next = root.querySelector('.carousel-arrow.next');
        if (!track || !slides.length) return;

        var index = 0;

        function update() {
            track.style.transform = 'translateX(' + (-index * 100) + '%)';
            if (dots && dots.length) {
                for (var i = 0; i < dots.length; i++) {
                    dots[i].classList.toggle('is-active', i === index);
                }
            }
        }

        if (prev) {
            prev.addEventListener('click', function () {
                index = (index - 1 + slides.length) % slides.length;
                update();
            });
        }
        if (next) {
            next.addEventListener('click', function () {
                index = (index + 1) % slides.length;
                update();
            });
        }
        if (dots && dots.length) {
            for (var i = 0; i < dots.length; i++) {
                (function (dotIndex) {
                    dots[dotIndex].addEventListener('click', function () {
                        index = dotIndex;
                        update();
                    });
                })(i);
            }
        }

        update();
    }

    var carousels = document.querySelectorAll('.carousel');
    for (var i = 0; i < carousels.length; i++) {
        initCarousel(carousels[i]);
    }
})();

// Reveal-on-scroll animation
(function () {
    var revealElements = document.querySelectorAll('.reveal-on-scroll');

    if (!('IntersectionObserver' in window) || !revealElements.length) {
        revealElements.forEach(function (el) {
            el.classList.add('in-view');
        });
        return;
    }

    var observer = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealElements.forEach(function (el) {
        observer.observe(el);
    });
})();

// Theme toggle
(function () {
    var STORAGE_KEY = 'theme';
    var root = document.documentElement;

    function currentTheme() {
        return root.classList.contains('dark-theme') ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        if (theme === 'dark') {
            root.classList.add('dark-theme');
        } else {
            root.classList.remove('dark-theme');
        }
        var btn = document.querySelector('.theme-toggle');
        if (!btn) return;
        var label = btn.querySelector('.theme-toggle-label');
        var icon = btn.querySelector('i');
        if (theme === 'dark') {
            if (label) label.textContent = 'Light';
            if (icon) { icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); }
        } else {
            if (label) label.textContent = 'Dark';
            if (icon) { icon.classList.remove('fa-sun'); icon.classList.add('fa-moon'); }
        }
    }

    try {
        var stored = localStorage.getItem(STORAGE_KEY);
        if (stored === 'dark' || stored === 'light') {
            applyTheme(stored);
        } else {
            applyTheme(currentTheme());
        }
    } catch (e) {
        applyTheme(currentTheme());
    }

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('.theme-toggle');
        if (!btn) return;
        event.preventDefault();
        var next = currentTheme() === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        try {
            localStorage.setItem(STORAGE_KEY, next);
        } catch (e) {}
    });
})();

// Gallery tab functionality
function openTab(evt, categoryName) {
    var i, tablinks, carousels;
    
    // Deactivate all tabs
    tablinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    
    // Hide all carousels
    carousels = document.querySelectorAll('.carousel');
    for (i = 0; i < carousels.length; i++) {
        carousels[i].classList.remove("active");
    }
    
    // Activate current tab
    evt.currentTarget.classList.add("active");
    
    // Show corresponding carousel
    var activeCarousel = document.querySelector('.carousel[data-tab="' + categoryName + '"]');
    if (activeCarousel) {
        activeCarousel.classList.add('active');
    }
}

// Smooth page transitions
(function () {
    var TRANSITION_MS = 320;

    function shouldHandleLink(anchor, event) {
        if (!anchor) return false;
        if (anchor.target === '_blank' || anchor.hasAttribute('download')) return false;
        if (event.defaultPrevented || event.button !== 0) return false;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;

        var href = anchor.getAttribute('href');
        if (!href || href.charAt(0) === '#') return false;
        if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0 || href.indexOf('javascript:') === 0) return false;

        var targetUrl;
        try {
            targetUrl = new URL(anchor.href, window.location.href);
        } catch (error) {
            return false;
        }

        if (targetUrl.origin !== window.location.origin) return false;
        if (targetUrl.pathname === window.location.pathname && targetUrl.search === window.location.search) return false;

        return true;
    }

    document.addEventListener('click', function (event) {
        var anchor = event.target.closest('a[href]');
        if (!shouldHandleLink(anchor, event)) return;

        event.preventDefault();
        document.documentElement.classList.add('is-transitioning');

        window.setTimeout(function () {
            window.location.href = anchor.href;
        }, TRANSITION_MS);
    });

    window.addEventListener('pageshow', function () {
        document.documentElement.classList.remove('is-transitioning');
    });
})();
