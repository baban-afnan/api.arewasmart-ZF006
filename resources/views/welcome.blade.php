<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Arewa Smart - {{ $title ?? 'Welcome to Arewa Smart Idea' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/logo/logo.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/icons/feather/feather.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

        <!-- Open Graph / WhatsApp Meta Tags -->
        <meta property="og:title" content="Arewa Smart Idea - Innovative Digital Solutions">
        <meta property="og:description" content="Empowering northern Nigeria through innovative digital solutions and smart technology services.">
        <meta property="og:image" content="{{ asset('assets/img/logo/logo.png') }}">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:type" content="website">

        <style>
            /* Premium Design Enhancements - Gold Theme */
            :root {
                --primary-color: #D4AF37; /* Metallic Gold */
                --primary-dark: #B8860B; /* Dark Goldenrod */
                --accent-color: #FFDF00; /* Bright Gold */
                --text-light: #f5f5f5;
            }

            body {
                font-family: 'Figtree', sans-serif;
            }

            /* Top Bar */
            .top-bar {
                background: linear-gradient(90deg, #1a1a1a, #000);
                color: #fff;
                padding: 10px 0;
                font-size: 0.9rem;
                border-bottom: 1px solid rgba(255,215,0,0.2);
            }
            .top-bar i {
                color: var(--accent-color);
                margin-right: 5px;
            }

            /* Hero Glassmorphism */
            .hero-content {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                padding: 3rem;
                border-radius: 20px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 8px 32px 0 rgba(212, 175, 55, 0.2);
            }

            /* Buttons */
            .btn-primary {
                background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
                border: none;
                transition: all 0.3s ease;
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
                background: linear-gradient(45deg, var(--primary-dark), var(--primary-color));
            }
            
            .btn-secondary-outline {
                background: transparent;
                border: 2px solid var(--primary-color);
                color: #fff;
                 transition: all 0.3s ease;
            }
            .btn-secondary-outline:hover {
                background: var(--primary-color);
                color: #fff;
                transform: translateY(-2px);
            }


            /* WhatsApp Float */
            .whatsapp-float {
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: #25d366;
                color: white;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
                box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
                z-index: 9999;
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                text-decoration: none;
            }
            .whatsapp-float:hover {
                transform: scale(1.1) rotate(10deg);
                background: #128C7E;
                color: white;
            }

            /* Animations */
            @keyframes pulse-gold {
                0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); }
                70% { box-shadow: 0 0 0 15px rgba(212, 175, 55, 0); }
                100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
            }
            
            /* Footer Banner Styles */
            .privacy-banner {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background: #ffffff;
                padding: 1.5rem;
                box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
                z-index: 10000;
                border-top: 1px solid #eee;
                display: none; /* Hidden by default, shown via JS */
                animation: slideUp 0.5s ease-out;
            }

            @keyframes slideUp {
                from { transform: translateY(100%); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }

            .privacy-banner .banner-content {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                max-width: 1200px;
                margin: 0 auto;
            }

            .privacy-icon {
                color: var(--accent-color);
                font-size: 1.5rem;
                margin-right: 15px;
            }
            
            .privacy-text h5 {
                font-weight: 700;
                margin-bottom: 5px;
                font-size: 1.1rem;
                color: #000;
            }
            
            .privacy-text p {
                font-size: 0.9rem;
                color: #555;
                margin-bottom: 0;
                max-width: 700px;
            }

            .banner-actions {
                display: flex;
                align-items: center;
                margin-top: 10px;
            }

            .btn-outline-secondary {
                border-color: #ccc;
                color: #333;
                background: transparent;
                margin-right: 10px;
            }
            .btn-outline-secondary:hover {
                background: #eee;
                color: #000;
            }

            .link-primary {
                color: var(--primary-color) !important;
                text-decoration: underline;
                margin-right: 20px;
                font-weight: 600;
                cursor: pointer;
            }

            @media (max-width: 768px) {
                .banner-actions {
                    margin-top: 20px;
                    width: 100%;
                    justify-content: flex-end;
                }
                .link-primary {
                    display: block;
                    margin-bottom: 10px;
                    margin-right: 0;
                }
            }
            
            /* Modal Styles */
            .data-protection-modal .modal-content {
                border-radius: 15px;
                border: none;
                background: #fff;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            }
            .data-protection-modal .modal-header {
                background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
                color: white;
                border-top-left-radius: 15px;
                border-top-right-radius: 15px;
                border-bottom: none;
            }
            .data-protection-modal .modal-body {
                max-height: 60vh;
                overflow-y: auto;
                padding: 2rem;
                font-size: 0.95rem;
                line-height: 1.6;
                color: #333;
            }
            .data-protection-modal .modal-footer {
                border-top: 1px solid #eee;
                background: #f9f9f9;
                border-bottom-left-radius: 15px;
                border-bottom-right-radius: 15px;
            }
            .policy-section {
                margin-bottom: 20px;
            }
            .policy-section h5 {
                color: var(--primary-color);
                font-weight: 600;
                margin-bottom: 10px;
                border-left: 3px solid var(--accent-color);
                padding-left: 10px;
            }

        </style>
    </head>

    <body class="bg-white">
        <div id="global-loader" style="display: none;">
            <div class="page-loader"></div>
        </div>

    <!-- Top Bar -->
    <div class="top-bar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="contact-info">
                <span class="me-4"><i class="fas fa-map-marker-alt"></i> NO983 Babantude Adelke Street Apapa Lagos</span>
                <span><i class="fas fa-phone-alt"></i> 09112345678</span>
            </div>
            <div class="social-icons">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header>
        <div class="container header-container">
            <a href="#" class="logo">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Arewa Smart Idea Logo" style="height: 40px; margin-right: 10px;">
            </a>
            <div class="mobile-menu">
            <i class="fas fa-bars"></i>
            </div>
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#partners">Partners</a></li>
                    <li><a href="#support">Support</a></li>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="{{route ('login')}}" class="btn btn-primary text-white">Get Started</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(50, 40, 0, 0.6)), 
         url('{{ asset('assets/img/landing/api.png') }}') no-repeat center center/cover; min-height: 100vh; display: flex; align-items: center;">
        <div class="container hero-content text-center">
            <h1 class="text-white mb-4" data-aos="fade-down" data-aos-duration="1000" style="font-size: 3.5rem; font-weight: 700;">
                Welcome to Arewa Smart API
            </h1>
            <p class="text-white mb-5" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000" style="font-size: 1.25rem; max-width: 800px; margin: 0 auto;">
                Empowering northern Nigeria through innovative digital solutions and smart technology services. Join us in building a smarter, more connected future.
            </p>
            <div class="hero-btns" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                <a href="{{route ('register')}}" class="btn btn-primary btn-lg me-3" style="padding: 12px 30px; border-radius: 30px;">
                    Get Started
                </a>
                <a href="{{route ('login')}}" class="btn btn-secondary-outline btn-lg me-3" style="padding: 12px 30px; border-radius: 30px;">
                    Login Now
                </a>
            </div>
        </div>
    </section>

   

   <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section" style="padding: 100px 0; background: linear-gradient(135deg, #1a1a1a 0%, #000 100%); position: relative; overflow: hidden;">
        <!-- Background Patterns -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; background-image: radial-gradient(#ffd700 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="container" style="position: relative; z-index: 2;">
            <div class="section-title text-center mb-5" data-aos="fade-up">
                <h4 style="color: #ffd700; font-weight: 600; letter-spacing: 2px; text-transform: uppercase;">Testimonials</h4>
                <h2 style="color: #fff; font-weight: 800; font-size: 2.5rem;">Trusted by Leaders</h2>
                <hr style="width: 60px; height: 3px; background: #ffd700; margin: 15px auto; border: none;">
                <p class="text-white-50" style="max-width: 600px; margin: 0 auto; font-size: 1.1rem;">
                    See what our partners and clients have to say about their experience working with Arewa Smart Idea.
                </p>
            </div>

            <div class="row g-4">
                <!-- Testimonial 1 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card-premium">
                        <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                        <p class="review-text">“Arewa Smart transformed our operations with cutting-edge solutions. Their support team is always responsive and professional! Truly a game changer for our business.”</p>
                        <div class="reviewer-info">
                            <img src="{{ asset('assets/img/users/user-08.jpg') }}" alt="Abdulrahman Musa">
                            <div>
                                <h4>Abdulrahman Musa</h4>
                                <span>CEO, NorthernTech</span>
                                <div class="stars">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card-premium">
                        <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                        <p class="review-text">“Working with Arewa Smart has been a seamless experience. Their expertise and attention to detail are unmatched. They delivered exactly what we needed, on time.”</p>
                        <div class="reviewer-info">
                            <img src="{{ asset('assets/img/users/user-34.jpg') }}" alt="Fatima Bello">
                            <div>
                                <h4>Fatima Bello</h4>
                                <span>Manager, Arewa Logistics</span>
                                <div class="stars">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card-premium">
                        <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                        <p class="review-text">“The quality of service and support we’ve received from Arewa Smart is outstanding. Highly recommended for any business looking to scale digitally.”</p>
                        <div class="reviewer-info">
                            <img src="{{ asset('assets/img/users/user-01.jpg') }}" alt="Emeka Johnson">
                            <div>
                                <h4>Emeka Johnson</h4>
                                <span>IT Director, SmartLink Ltd</span>
                                <div class="stars">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .testimonial-card-premium {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                padding: 40px 30px;
                border-radius: 20px;
                color: white;
                height: 100%;
                transition: all 0.3s ease;
                position: relative;
            }
            .testimonial-card-premium:hover {
                background: rgba(255, 255, 255, 0.1);
                transform: translateY(-5px);
            }
            .quote-icon {
                font-size: 2rem;
                color: #ffd700;
                margin-bottom: 20px;
                opacity: 0.5;
            }
            .review-text {
                font-size: 1.05rem;
                line-height: 1.8;
                margin-bottom: 30px;
                font-style: italic;
                opacity: 0.9;
            }
            .reviewer-info {
                display: flex;
                align-items: center;
            }
            .reviewer-info img {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                object-fit: cover;
                margin-right: 15px;
                border: 2px solid #ffd700;
            }
            .reviewer-info h4 {
                font-size: 1.1rem;
                font-weight: 700;
                margin-bottom: 2px;
                color: #fff;
            }
            .reviewer-info span {
                font-size: 0.85rem;
                color: rgba(255, 255, 255, 0.7);
                display: block;
                margin-bottom: 5px;
            }
            .stars {
                color: #ffd700;
                font-size: 0.8rem;
            }
        </style>
    </section>



    <!-- Partners Section -->
    <section class="partners" id="partners" style="padding: 80px 0; background-color: #fff;">
        <div class="container">
            <div class="section-title text-center mb-5" data-aos="fade-up">
                <h4 style="color: #ffd700; font-weight: 600; letter-spacing: 2px; text-transform: uppercase;">Our Partners</h4>
                <h2 style="color: #B8860B; font-weight: 800; font-size: 2.5rem;">Trusted Collaborations</h2>
                <hr style="width: 60px; height: 3px; background: #ffd700; margin: 15px auto; border: none;">
                <p class="text-muted" style="max-width: 600px; margin: 0 auto; font-size: 1.1rem;">
                    We proudly collaborate with leading organizations to deliver excellence and innovation.
                </p>
            </div>

            <div class="row align-items-center justify-content-center g-5">
                <div class="col-6 col-md-4 col-lg-2 text-center" data-aos="fade-up" data-aos-delay="100">
                    <img src="{{ asset('assets/img/partner/zepa.png') }}" alt="Zepa" class="partner-logo-premium">
                </div>
                <div class="col-6 col-md-4 col-lg-2 text-center" data-aos="fade-up" data-aos-delay="200">
                    <img src="{{ asset('assets/img/partner/biyanow.png') }}" alt="BiyaNow" class="partner-logo-premium">
                </div>
                <div class="col-6 col-md-4 col-lg-2 text-center" data-aos="fade-up" data-aos-delay="300">
                    <img src="{{ asset('assets/img/partner/palmpay.png') }}" alt="PalmPay" class="partner-logo-premium">
                </div>
                <div class="col-6 col-md-4 col-lg-2 text-center" data-aos="fade-up" data-aos-delay="400">
                    <img src="{{ asset('assets/img/partner/jamb.png') }}" alt="JAMB" class="partner-logo-premium">
                </div>
                <div class="col-6 col-md-4 col-lg-2 text-center" data-aos="fade-up" data-aos-delay="500">
                    <img src="{{ asset('assets/img/partner/nimc1.png') }}" alt="NIMC" class="partner-logo-premium">
                </div>
                <div class="col-6 col-md-4 col-lg-2 text-center" data-aos="fade-up" data-aos-delay="600">
                    <img src="{{ asset('assets/img/partner/bvnlogo.png') }}" alt="BVN" class="partner-logo-premium">
                </div>
            </div>
        </div>

        <style>
            .partner-logo-premium {
                max-width: 100%;
                height: auto;
                max-height: 60px;
                filter: grayscale(100%);
                opacity: 0.6;
                transition: all 0.4s ease;
            }
            .partner-logo-premium:hover {
                filter: grayscale(0%);
                opacity: 1;
                transform: scale(1.1);
            }
        </style>
    </section>

    
    <!-- Footer -->
    <footer style="background: #1a0505; color: #fff; padding: 80px 0 30px; position: relative; overflow: hidden; padding-bottom: 100px;">
        <!-- Background Decoration -->
        <div style="position: absolute; top: 0; right: 0; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,215,0,0.05) 0%, rgba(0,0,0,0) 70%);"></div>

        <div class="container">
            <div class="row g-5">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6">
                    <h2 style="color: #ffd700; font-weight: 700; margin-bottom: 20px;">Arewa Smart Idea</h2>
                    <p style="color: rgba(255,255,255,0.7); line-height: 1.8; margin-bottom: 25px;">
                        Providing innovative technology solutions to help businesses thrive in the digital world. We are committed to excellence and sustainable growth in Northern Nigeria.
                    </p>
                    <div class="social-links">
                        <a href="#" class="footer-social"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="footer-social"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="footer-social"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="footer-social"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h3 style="color: #fff; font-size: 1.2rem; font-weight: 600; margin-bottom: 25px; border-bottom: 2px solid #ffd700; display: inline-block; padding-bottom: 5px;">Quick Links</h3>
                    <ul class="footer-links list-unstyled">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#partners">Partners</a></li>
                        <li><a href="#support">Support</a></li>
                        <li><a href="#about-us">About Us</a></li>
                        <li><a href="javascript:void(0)" onclick="openDataProtectionModal()">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <!-- Services -->
                <div class="col-lg-3 col-md-6">
                    <h3 style="color: #fff; font-size: 1.2rem; font-weight: 600; margin-bottom: 25px; border-bottom: 2px solid #ffd700; display: inline-block; padding-bottom: 5px;">Our Services</h3>
                    <ul class="footer-links list-unstyled">
                        <li><a href="#">Web Development</a></li>
                        <li><a href="#">Mobile Apps</a></li>
                        <li><a href="#">BVN & NIN Services</a></li>
                        <li><a href="#">Digital Marketing</a></li>
                        <li><a href="#">IT Consultancy</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div class="col-lg-3 col-md-6">
                    <h3 style="color: #fff; font-size: 1.2rem; font-weight: 600; margin-bottom: 25px; border-bottom: 2px solid #ffd700; display: inline-block; padding-bottom: 5px;">Contact Us</h3>
                    <ul class="footer-contact list-unstyled">
                        <li style="margin-bottom: 15px; display: flex;">
                            <i class="fas fa-map-marker-alt" style="color: #ffd700; margin-top: 5px; margin-right: 10px;"></i>
                            <span style="color: rgba(255,255,255,0.8);">NO983 BABANTUDE ADELEKE STREET APAPA LAGOS</span>
                        </li>
                        <li style="margin-bottom: 15px; display: flex;">
                            <i class="fas fa-phone" style="color: #ffd700; margin-top: 5px; margin-right: 10px;"></i>
                            <span style="color: rgba(255,255,255,0.8);">09112345678</span>
                        </li>
                        <li style="margin-bottom: 15px; display: flex;">
                            <i class="fas fa-envelope" style="color: #ffd700; margin-top: 5px; margin-right: 10px;"></i>
                            <span style="color: rgba(255,255,255,0.8);">info@arewasmart.com.ng</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr style="border-color: rgba(255,255,255,0.1); margin: 40px 0;">
            
            <div class="footer-bottom text-center">
                <p style="color: rgba(255,255,255,0.6); margin: 0;">&copy; 2024 Arewa Smart Idea. All rights reserved. | Designed with <i class="fas fa-heart" style="color: #ffd700;"></i> by Arewa Smart Team.</p>
            </div>
        </div>

        <style>
            .footer-links li {
                margin-bottom: 12px;
            }
            .footer-links a {
                color: rgba(255,255,255,0.7);
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-block;
            }
            .footer-links a:hover {
                color: #ffd700;
                transform: translateX(5px);
            }
            .footer-social {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                background: rgba(255,255,255,0.1);
                color: #fff;
                border-radius: 50%;
                margin-right: 10px;
                text-decoration: none;
                transition: all 0.3s ease;
            }
            .footer-social:hover {
                background: #ffd700;
                color: #b71c1c;
                transform: translateY(-3px);
            }
        </style>
    </footer>

    <!-- Privacy Banner (Footer) -->
    <div class="privacy-banner" id="privacyBanner">
        <div class="banner-content">
            <div class="d-flex align-items-center">
                <i class="fas fa-shield-alt privacy-icon"></i>
                <div class="privacy-text">
                    <h5>Your Privacy Matters</h5>
                    <p>We value your privacy and are committed to protecting your personal data in compliance with the Data Protection Regulation (NDPR). We collect data to provide verification services.</p>
                </div>
            </div>
            <div class="banner-actions">
                <a href="javascript:void(0)" class="link-primary" onclick="openDataProtectionModal()">Read Full Policy</a>
                <button type="button" class="btn btn-outline-secondary" onclick="rejectPrivacy()">Reject</button>
                <button type="button" class="btn btn-primary" onclick="acceptPrivacyPolicy()">Accept & Continue</button>
            </div>
        </div>
    </div>


    <!-- Data Protection Modal (Hidden by default, triggered by "Read Full Policy") -->
    <div class="modal fade data-protection-modal" id="dataProtectionModal" tabindex="-1" aria-labelledby="dataProtectionModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataProtectionModalLabel"><i class="fas fa-shield-alt me-2"></i> Data Protection & Privacy Policy</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo" style="height: 60px;">
                        <h4 class="mt-3 text-dark">Arewa Smart Idea Data Privacy Commitment</h4>
                    </div>

                    <p class="lead text-muted mb-4">
                        At Arewa Smart Idea, we are committed to protecting your personal data in compliance with the 
                        <strong>Nigeria Data Protection Regulation (NDPR) 2019</strong> and other applicable laws.
                    </p>

                    <div class="policy-section">
                        <h5>1. Introduction</h5>
                        <p>This Privacy Policy explains how Arewa Smart Idea collects, uses, and protects your personal information when you use our digital solutions, including our website, mobile applications, and NIN/BVN services.</p>
                    </div>

                    <div class="policy-section">
                        <h5>2. Consent</h5>
                        <p>By accessing our platforms or using our services, you consent to the collection and use of your personal data as described in this policy. You have the right to withdraw your consent at any time.</p>
                    </div>

                    <div class="policy-section">
                        <h5>3. Information We Collect</h5>
                        <p>We may collect the following types of information to provide you with better services:</p>
                        <ul>
                            <li><strong>Personal Identification:</strong> Name, Email address, Phone number, NIN, BVN (for verification services).</li>
                            <li><strong>Business Details:</strong> Business name, address, and nature of business.</li>
                            <li><strong>Technical Data:</strong> IP address, browser type, and device information.</li>
                        </ul>
                    </div>

                    <div class="policy-section">
                        <h5>4. Purpose of Collection</h5>
                        <p>Your data is collected for the following lawful purposes:</p>
                        <ul>
                            <li>To provide and manage our services (e.g., identity verification, account management).</li>
                            <li>To communicate with you regarding updates, support, and promotional offers.</li>
                            <li>To comply with legal obligations and regulatory requirements.</li>
                            <li>To improve our platforms and user experience.</li>
                        </ul>
                    </div>

                    <div class="policy-section">
                        <h5>5. Data Security</h5>
                        <p>We implement robust technical and organizational measures to secure your personal data against unauthorized access, loss, or alteration. We use encryption, secure servers, and strict access controls.</p>
                    </div>

                    <div class="policy-section">
                        <h5>6. Your Rights (NDPR)</h5>
                        <p>Subject to the NDPR, you have the right to:</p>
                        <ul>
                            <li>Request access to your personal data held by us.</li>
                            <li>Request correction of inaccurate or incomplete data.</li>
                            <li>Request deletion of your data (Right to be Forgotten).</li>
                            <li>Objec to the processing of your data for marketing purposes.</li>
                        </ul>
                    </div>

                     <div class="policy-section">
                        <h5>7. Contact Us</h5>
                        <p>If you have any questions about this policy or wish to exercise your rights, please contact our Data Protection Officer at:</p>
                        <p><strong>Email:</strong> privacy@arewasmart.com.ng<br>
                        <strong>Phone:</strong> 09112345678<br>
                        <strong>Address:</strong> NO983 Babantude Adelke Street Apapa Lagos.</p>
                    </div>

                    <div class="alert alert-warning mt-4 text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        By clicking "I Agree & Continue", you acknowledge that you have read and understood this Privacy Policy and agree to our Terms of Service.
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('register') }}" class="btn btn-primary px-5 py-2 fw-bold" onclick="acceptPrivacyPolicy()">
                        I Agree & Continue <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu').addEventListener('click', function() {
            document.querySelector('nav ul').classList.toggle('active');
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('nav ul li a').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelector('nav ul').classList.remove('active');
            });
        });
        
        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
            } else {
                header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            }
        });

        // Data Protection Logic
        function openDataProtectionModal() {
            var myModal = new bootstrap.Modal(document.getElementById('dataProtectionModal'), {
                keyboard: false
            });
            myModal.show();
        }

        function acceptPrivacyPolicy() {
            // Optional: Store consent in cookies or local storage
            localStorage.setItem('arewa_privacy_accepted', 'true');
            // Hide Banner
            document.getElementById('privacyBanner').style.display = 'none';
            // If in modal, close it
            var modalEl = document.getElementById('dataProtectionModal');
            var modal = bootstrap.Modal.getInstance(modalEl);
            if(modal) modal.hide();
        }
        
        function rejectPrivacy() {
             document.getElementById('privacyBanner').style.display = 'none';
        }

        // Auto-show banner for first time visitors
        document.addEventListener('DOMContentLoaded', function() {
            // Show banner if not accepted
             if (!localStorage.getItem('arewa_privacy_accepted')) {
                document.getElementById('privacyBanner').style.display = 'block';
            }
        });
    </script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script> AOS.init({ duration: 1000, once: true }) </script>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/2349110501995" class="whatsapp-float" target="_blank" title="Chat with us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

</body>
</html>
