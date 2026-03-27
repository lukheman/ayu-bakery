<div class="mt-4">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-shape shape-1"></div>
            <div class="hero-shape shape-2"></div>
            <div class="hero-shape shape-3"></div>
        </div>
        <div class="container hero-container">
            <div class="hero-content">
                <span class="hero-badge">
                    <i class="fas fa-sparkles"></i>
                    New Version 2.0 is Here!
                </span>
                <h1 class="hero-title">
                    Build Beautiful
                    <span class="gradient-text">Admin Dashboards</span>
                    In Minutes
                </h1>
                <p class="hero-description">
                    A modern, elegant admin dashboard template built with Laravel 12, Livewire, and Tailwind CSS.
                    Features dark mode, 18+ reusable components, and user management out of the box.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket"></i>
                        Get Started Free
                    </a>
                    <a href="#features" class="btn btn-outline btn-lg">
                        <i class="fas fa-play-circle"></i>
                        Watch Demo
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-value">18+</span>
                        <span class="stat-label">Components</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-value">Dark/Light</span>
                        <span class="stat-label">Theme Support</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-value">100%</span>
                        <span class="stat-label">Responsive</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="dashboard-preview">
                    <div class="preview-header">
                        <div class="preview-dots">
                            <span class="dot red"></span>
                            <span class="dot yellow"></span>
                            <span class="dot green"></span>
                        </div>
                    </div>
                    <div class="preview-content">
                        <div class="preview-sidebar">
                            <div class="sidebar-item active"></div>
                            <div class="sidebar-item"></div>
                            <div class="sidebar-item"></div>
                            <div class="sidebar-item"></div>
                        </div>
                        <div class="preview-main">
                            <div class="preview-cards">
                                <div class="preview-card card-1"></div>
                                <div class="preview-card card-2"></div>
                                <div class="preview-card card-3"></div>
                            </div>
                            <div class="preview-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section features-section" id="features">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Features</span>
                <h2 class="section-title">Everything You Need to Build Amazing Dashboards</h2>
                <p class="section-description">
                    Packed with features that help you build professional admin interfaces faster than ever.
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <i class="fas fa-moon"></i>
                    </div>
                    <h3>Dark Mode</h3>
                    <p>Beautiful dark theme with smooth transitions. User preference is saved automatically.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #0ea5e9, #06b6d4);">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Livewire Powered</h3>
                    <p>Dynamic, reactive components without writing JavaScript. Real-time updates made easy.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-puzzle-piece"></i>
                    </div>
                    <h3>18+ Components</h3>
                    <p>Reusable Blade components for cards, tables, buttons, modals, and more.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>User Management</h3>
                    <p>Full CRUD operations with search, pagination, and form validation out of the box.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #ef4444, #f87171);">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Responsive Design</h3>
                    <p>Perfect on any device with mobile-first approach and collapsible sidebar.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Authentication Ready</h3>
                    <p>Modern login system with form validation, remember me, and session management.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Components Section -->
    <section class="section components-section" id="components">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Components</span>
                <h2 class="section-title">Beautiful, Reusable UI Components</h2>
                <p class="section-description">
                    All components are built with accessibility and customization in mind.
                </p>
            </div>
            <div class="components-showcase">
                <div class="component-preview-card">
                    <h4>Buttons</h4>
                    <div class="component-demo">
                        <button class="demo-btn primary">Primary</button>
                        <button class="demo-btn secondary">Secondary</button>
                        <button class="demo-btn success">Success</button>
                        <button class="demo-btn warning">Warning</button>
                        <button class="demo-btn danger">Danger</button>
                    </div>
                </div>
                <div class="component-preview-card">
                    <h4>Badges</h4>
                    <div class="component-demo">
                        <span class="demo-badge primary"><i class="fas fa-circle"></i> Active</span>
                        <span class="demo-badge success"><i class="fas fa-check"></i> Completed</span>
                        <span class="demo-badge warning"><i class="fas fa-clock"></i> Pending</span>
                        <span class="demo-badge danger"><i class="fas fa-times"></i> Cancelled</span>
                    </div>
                </div>
                <div class="component-preview-card">
                    <h4>Alerts</h4>
                    <div class="component-demo column">
                        <div class="demo-alert success">
                            <i class="fas fa-check-circle"></i>
                            <span>Your changes have been saved successfully.</span>
                        </div>
                        <div class="demo-alert warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Please review your settings before continuing.</span>
                        </div>
                    </div>
                </div>
                <div class="component-preview-card">
                    <h4>Progress Bars</h4>
                    <div class="component-demo column">
                        <div class="demo-progress">
                            <div class="progress-label">
                                <span>Project Progress</span>
                                <span>75%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill"
                                    style="width: 75%; background: linear-gradient(90deg, #6366f1, #8b5cf6);"></div>
                            </div>
                        </div>
                        <div class="demo-progress">
                            <div class="progress-label">
                                <span>Storage Used</span>
                                <span>45%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill"
                                    style="width: 45%; background: linear-gradient(90deg, #0ea5e9, #06b6d4);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section">
        <div class="container">
            <div class="cta-card">
                <div class="cta-content">
                    <h2>Ready to Build Something Amazing?</h2>
                    <p>Get started with AdminPro today and create stunning admin dashboards in minutes.</p>
                    <div class="cta-actions">
                        <a href="{{ route('login') }}" class="btn btn-white btn-lg">
                            <i class="fas fa-rocket"></i>
                            Get Started Free
                        </a>
                        <a href="#" class="btn btn-outline-white btn-lg">
                            <i class="fab fa-github"></i>
                            View on GitHub
                        </a>
                    </div>
                </div>
                <div class="cta-decoration">
                    <div class="decoration-circle"></div>
                    <div class="decoration-circle small"></div>
                </div>
            </div>
        </div>
    </section>

    <x-slot:styles>
        <style>
            /* Hero Section */
            .hero {
                min-height: 100vh;
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
                padding-top: 80px;
            }

            .hero-bg {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 0;
            }

            .hero-shape {
                position: absolute;
                border-radius: 50%;
                animation: float 20s ease-in-out infinite;
            }

            .shape-1 {
                width: 600px;
                height: 600px;
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
                top: -200px;
                right: -200px;
            }

            .shape-2 {
                width: 400px;
                height: 400px;
                background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(6, 182, 212, 0.1));
                bottom: -100px;
                left: -100px;
                animation-delay: -7s;
            }

            .shape-3 {
                width: 300px;
                height: 300px;
                background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1));
                top: 50%;
                left: 30%;
                animation-delay: -14s;
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0) rotate(0deg);
                }

                33% {
                    transform: translateY(-30px) rotate(5deg);
                }

                66% {
                    transform: translateY(20px) rotate(-5deg);
                }
            }

            .hero-container {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4rem;
                align-items: center;
                position: relative;
                z-index: 1;
            }

            .hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
                color: var(--primary-color);
                padding: 0.5rem 1rem;
                border-radius: 50px;
                font-size: 0.9rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
                border: 1px solid rgba(99, 102, 241, 0.2);
            }

            .hero-title {
                font-size: 3.5rem;
                font-weight: 800;
                line-height: 1.1;
                margin-bottom: 1.5rem;
            }

            .gradient-text {
                background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero-description {
                font-size: 1.25rem;
                color: var(--text-secondary);
                margin-bottom: 2rem;
                line-height: 1.7;
            }

            .hero-actions {
                display: flex;
                gap: 1rem;
                margin-bottom: 3rem;
            }

            .hero-stats {
                display: flex;
                align-items: center;
                gap: 2rem;
            }

            .stat-item {
                text-align: center;
            }

            .stat-value {
                display: block;
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--text-primary);
            }

            .stat-label {
                font-size: 0.9rem;
                color: var(--text-muted);
            }

            .stat-divider {
                width: 1px;
                height: 40px;
                background: var(--border-color);
            }

            /* Dashboard Preview */
            .dashboard-preview {
                background: var(--bg-white);
                border-radius: 20px;
                box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
                overflow: hidden;
                border: 1px solid var(--border-color);
            }

            .preview-header {
                background: linear-gradient(135deg, #1e293b, #334155);
                padding: 1rem 1.5rem;
            }

            .preview-dots {
                display: flex;
                gap: 8px;
            }

            .dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
            }

            .dot.red {
                background: #ef4444;
            }

            .dot.yellow {
                background: #f59e0b;
            }

            .dot.green {
                background: #10b981;
            }

            .preview-content {
                display: flex;
                min-height: 300px;
            }

            .preview-sidebar {
                width: 60px;
                background: #1e293b;
                padding: 1rem;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .sidebar-item {
                height: 8px;
                background: #334155;
                border-radius: 4px;
            }

            .sidebar-item.active {
                background: var(--primary-color);
            }

            .preview-main {
                flex: 1;
                padding: 1.5rem;
                background: #f8fafc;
            }

            [data-theme="dark"] .preview-main {
                background: #0f172a;
            }

            .preview-cards {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .preview-card {
                height: 60px;
                border-radius: 10px;
            }

            .card-1 {
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
            }

            .card-2 {
                background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            }

            .card-3 {
                background: linear-gradient(135deg, #10b981, #34d399);
            }

            .preview-chart {
                height: 120px;
                background: var(--bg-white);
                border-radius: 10px;
                border: 1px solid var(--border-color);
            }

            /* Section Styles */
            .section-header {
                text-align: center;
                max-width: 700px;
                margin: 0 auto 4rem;
            }

            .section-badge {
                display: inline-block;
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
                color: var(--primary-color);
                padding: 0.5rem 1.25rem;
                border-radius: 50px;
                font-size: 0.85rem;
                font-weight: 600;
                margin-bottom: 1rem;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .section-title {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
            }

            .section-description {
                font-size: 1.15rem;
                color: var(--text-secondary);
            }

            /* Features Grid */
            .features-section {
                background: var(--bg-white);
            }

            .features-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 2rem;
            }

            .feature-card {
                background: var(--bg-light);
                padding: 2rem;
                border-radius: 16px;
                transition: all 0.3s ease;
                border: 1px solid var(--border-color);
            }

            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }

            .feature-icon {
                width: 60px;
                height: 60px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1.5rem;
            }

            .feature-icon i {
                font-size: 1.5rem;
                color: white;
            }

            .feature-card h3 {
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 0.75rem;
            }

            .feature-card p {
                color: var(--text-secondary);
                font-size: 0.95rem;
            }

            /* Components Showcase */
            .components-showcase {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
            }

            .component-preview-card {
                background: var(--bg-white);
                border-radius: 16px;
                padding: 2rem;
                border: 1px solid var(--border-color);
            }

            .component-preview-card h4 {
                font-size: 1rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .component-demo {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .component-demo.column {
                flex-direction: column;
            }

            .demo-btn {
                padding: 0.625rem 1.25rem;
                border-radius: 8px;
                font-weight: 500;
                font-size: 0.9rem;
                border: none;
                cursor: pointer;
                transition: all 0.3s;
            }

            .demo-btn.primary {
                background: #6366f1;
                color: white;
            }

            .demo-btn.secondary {
                background: #64748b;
                color: white;
            }

            .demo-btn.success {
                background: #10b981;
                color: white;
            }

            .demo-btn.warning {
                background: #f59e0b;
                color: white;
            }

            .demo-btn.danger {
                background: #ef4444;
                color: white;
            }

            .demo-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 0.5rem 1rem;
                border-radius: 50px;
                font-size: 0.8rem;
                font-weight: 600;
            }

            .demo-badge.primary {
                background: rgba(99, 102, 241, 0.15);
                color: #6366f1;
            }

            .demo-badge.success {
                background: rgba(16, 185, 129, 0.15);
                color: #10b981;
            }

            .demo-badge.warning {
                background: rgba(245, 158, 11, 0.15);
                color: #f59e0b;
            }

            .demo-badge.danger {
                background: rgba(239, 68, 68, 0.15);
                color: #ef4444;
            }

            .demo-alert {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 1rem 1.25rem;
                border-radius: 10px;
                font-size: 0.9rem;
            }

            .demo-alert.success {
                background: rgba(16, 185, 129, 0.1);
                color: #10b981;
            }

            .demo-alert.warning {
                background: rgba(245, 158, 11, 0.1);
                color: #f59e0b;
            }

            .demo-progress {
                width: 100%;
            }

            .progress-label {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
            }

            .progress-bar {
                height: 8px;
                background: var(--border-color);
                border-radius: 50px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                border-radius: 50px;
            }

            /* CTA Section */
            .cta-section {
                background: var(--bg-light);
            }

            .cta-card {
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                border-radius: 24px;
                padding: 4rem;
                position: relative;
                overflow: hidden;
            }

            .cta-content {
                position: relative;
                z-index: 1;
                text-align: center;
                max-width: 600px;
                margin: 0 auto;
            }

            .cta-content h2 {
                font-size: 2.5rem;
                font-weight: 700;
                color: white;
                margin-bottom: 1rem;
            }

            .cta-content p {
                font-size: 1.15rem;
                color: rgba(255, 255, 255, 0.8);
                margin-bottom: 2rem;
            }

            .cta-actions {
                display: flex;
                justify-content: center;
                gap: 1rem;
            }

            .btn-white {
                background: white;
                color: #6366f1;
            }

            .btn-white:hover {
                background: #f8fafc;
                transform: translateY(-2px);
            }

            .btn-outline-white {
                background: transparent;
                border: 2px solid rgba(255, 255, 255, 0.3);
                color: white;
            }

            .btn-outline-white:hover {
                background: rgba(255, 255, 255, 0.1);
                border-color: white;
            }

            .cta-decoration {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 50%;
            }

            .decoration-circle {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
            }

            .decoration-circle:first-child {
                width: 400px;
                height: 400px;
                right: -100px;
                top: -100px;
            }

            .decoration-circle.small {
                width: 200px;
                height: 200px;
                right: 50px;
                bottom: -50px;
            }

            /* Responsive */
            @media (max-width: 1024px) {
                .hero-container {
                    grid-template-columns: 1fr;
                    text-align: center;
                }

                .hero-title {
                    font-size: 2.75rem;
                }

                .hero-actions {
                    justify-content: center;
                }

                .hero-stats {
                    justify-content: center;
                }

                .hero-image {
                    display: none;
                }

                .features-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .components-showcase {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .hero-title {
                    font-size: 2.25rem;
                }

                .hero-description {
                    font-size: 1.1rem;
                }

                .hero-actions {
                    flex-direction: column;
                }

                .hero-stats {
                    flex-direction: column;
                    gap: 1.5rem;
                }

                .stat-divider {
                    width: 40px;
                    height: 1px;
                }

                .features-grid {
                    grid-template-columns: 1fr;
                }

                .section-title {
                    font-size: 2rem;
                }

                .cta-card {
                    padding: 2.5rem 1.5rem;
                }

                .cta-content h2 {
                    font-size: 1.75rem;
                }

                .cta-actions {
                    flex-direction: column;
                }
            }
        </style>
    </x-slot:styles>
</div>
