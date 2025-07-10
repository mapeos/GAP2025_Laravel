<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Paciente') - GAP</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/dashboard-alumno.css') }}" rel="stylesheet">
    
    @stack('css')
    
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar.collapsed {
            transform: translateX(-240px);
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            border-left: 4px solid #fff;
        }
        
        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: #f8f9fa;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: 40px;
        }
        
        .top-bar {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-area {
            padding: 2rem;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('paciente.home') }}" class="sidebar-brand">
                <i class="ri-heart-pulse-line me-2"></i>
                GAP Médico
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('paciente.home') }}" class="nav-link {{ request()->routeIs('paciente.home') ? 'active' : '' }}">
                    <i class="ri-home-line"></i>
                    <span>Inicio</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('paciente.solicitar-cita') }}" class="nav-link">
                    <i class="ri-calendar-add-line"></i>
                    <span>Solicitar Cita</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('paciente.mis-citas') }}" class="nav-link {{ request()->routeIs('paciente.mis-citas') ? 'active' : '' }}">
                    <i class="ri-calendar-check-line"></i>
                    <span>Mis Citas</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('facultativo.calendario') }}" class="nav-link">
                    <i class="ri-calendar-2-line"></i>
                    <span>Calendario Médico</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('profile.show') }}" class="nav-link">
                    <i class="ri-user-line"></i>
                    <span>Mi Perfil</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('profile.edit') }}" class="nav-link">
                    <i class="ri-settings-line"></i>
                    <span>Configuración</span>
                </a>
            </div>
            
            <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 1.5rem;">
            
            <div class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link" style="background: none; border: none; width: 100%; text-align: left;">
                        <i class="ri-logout-box-line"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" id="sidebarToggle">
                    <i class="ri-menu-line"></i>
                </button>
                <h4 class="mb-0">@yield('title-page', 'Paciente')</h4>
            </div>
            
            <div class="user-menu">
                <div class="user-avatar">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <small class="text-muted">Paciente</small>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
        
        // Mobile sidebar toggle
        if (window.innerWidth <= 768) {
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('show');
            });
        }
    </script>
    
    @stack('js')
</body>
</html> 