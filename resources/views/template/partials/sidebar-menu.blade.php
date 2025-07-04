<nav class="nav-tree">
    <ul class="list-unstyled">
        <!-- Forms And Tables Section -->
        <li class="nav-section"><span class="nav-section-text text-uppercase px-2">Main</span></li>
        <!-- Dashboard -->
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="ri-dashboard-line"></i> <span>Dashboard</span>
            </a>
        </li>

        <!-- News Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-newspaper-line"></i> <span>News</span> <i class="ri-arrow-right-s-line"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.news.index') }}">
                        <i class="ri-list-unordered"></i> <span>All News</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.news.create') }}">
                        <i class="ri-add-line"></i> <span>Create News</span>
                    </a>
                </li>
                <!-- A espera de agregar más enlaces de ser necesarios -->

            </ul>
        </li>
        <!-- Seccion de Calendario -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-calendar-event-fill"></i></i> <span>Eventos</span> <i class="ri-arrow-right-s-line"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('events.calendar') }}">
                        <i class="ri-calendar-line""></i> <span>Calendario</span>
                    </a>
                </li>
                <li class=" nav-item">
                            <a class="nav-link" href="{{ route('admin.events.index') }}">
                                <i class="ri-calendar-2-line"></i> <span>listado de eventos</span>
                            </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.events.types.index') }}">
                        <i class="ri-calendar-todo-line"></i> <span>listado de tipos eventos</span>
                    </a>
                </li>
                <!-- A espera de agregar más enlaces de ser necesarios -->

            </ul>
        </li>

        <!-- Usuarios -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
                <i class="ri-user-line"></i> <span>Usuarios</span>
            </a>
        </li>
        <!-- Notificaciones Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-notification-3-line"></i> <span>Notificaciones</span> <i class="ri-arrow-right-s-line"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.notificaciones.index') }}">
                        <i class="ri-list-unordered"></i> <span>Listado</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.notificaciones.create') }}">
                        <i class="ri-add-line"></i> <span>Crear Notificación</span>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Fin Notificaciones -->
        <!-- WhatsApp Notificación -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.whatsapp.form') }}">
                <i class="ri-whatsapp-line"></i> <span>WhatsApp Notificación</span>
            </a>
        </li>
        <!-- CURSOS PARTICIPANTES INSCRIPCIONES -->
        <li class="nav-section"><span class="nav-section-text text-uppercase px-2">Admin CURSOS</span></li>
        <!-- CURSOS Section -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.cursos.index') }}">
                <i class="ri-file-list-3-line"></i> <span>Cursos</span>
            </a>
        </li>
        <!-- PARTICIPANTES Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-table-line"></i> <span>Participantes</span> <i class="ri-arrow-right-s-line ms-auto"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.participantes.index') }}"> <span>Listar Participantes</span> </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.participantes.create') }}"> <span>Crear Participantes</span> </a>
                </li> -->
            </ul>
        </li>
        <!-- INSCRIPCIONES Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-file-list-3-line"></i> <span>Inscripciones</span> <i class="ri-arrow-right-s-line ms-auto"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.inscripciones.cursos.activos') }}"> <span>Inscribir</span> </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="/pages/forms/select"> <span>Bajas</span> </a>
                </li> -->
            </ul>
        </li>

        <!-- Facultativo Section -->
        <li class="nav-section"><span class="nav-section-text text-uppercase px-2">Facultativo</span></li>
        <!-- citas Section -->
        <li class="nav-item">
            <a class="nav-link" href="/facultativo">
                <i class="ri-home-line"></i> <span>Home</span>
            </a>
        </li>
        <!-- citas Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link">
                <i class="ri-calendar-line"></i> <span>Citas</span> <i class="ri-arrow-right-s-line"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="/facultativo/citas">
                        <i class="ri-calendar-todo-fill"></i><span>Lista de citas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/facultativo/cita/new">
                        <i class="ri-sticky-note-add-line"></i></i><span>Nueva citas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/facultativo/citas/confirmadas">
                        <i class="ri-calendar-check-line"></i> <span>Citas Confirmadas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/facultativo/citas/pendientes" target="_blank">
                        <i class="ri-calendar-schedule-line"></i> <span>Citas Pendientes</span>
                    </a>
            </ul>
        </li>
        <!-- PACIENTE Section -->
        <li class="nav-item">
            <a class="nav-link" href="/facultativo/pacientes">
                <i class="ri-team-line"></i> <span>Pacientes</span>
            </a>
        </li>
        <!-- Tratamientos Section -->
        <li class="nav-item">
            <a class="nav-link" href="/facultativo/tratamientos">
                <i class="ri-capsule-line"></i> <span>Tratamientos</span>
            </a>
        </li>
        <!-- Facultativo Section end -->

        <!-- Pages Section -->
        <li class="nav-section"><span class="nav-section-text text-uppercase px-2">Pages</span></li>
        
        <!-- Gestión de Pagos -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-lock-line"></i> <span>Gestión de Pagos</span> <i class="ri-arrow-right-s-line"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.pagos.metodos') }}">
                        <span>Métodos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.pagos.facturas.index') }}">
                        <span>Facturas</span>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Other Pages Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-pages-line"></i> <span>Other Pages</span> <i class="ri-arrow-right-s-line"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="/pages/others/coming-soon"> <span>Coming Soon</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/others/maintenance"> <span>Maintenance</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/others/password-protected"> <span>Password Protected</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/others/401"> <span>401 - Unauthorized</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/others/error"> <span>404 - Page Not Found</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/others/500"> <span>500 - Internal Server Error</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/others/rtl-demo"> <span>RTL Support Demo</span> </a>
                </li>
            </ul>
        </li>
        <!-- Settings Section -->
        <li class="nav-item">
            <a class="nav-link" href="/pages/settings">
                <i class="ri-settings-3-line"></i> <span>Settings</span>
            </a>
        </li>
        <!-- GitHub Repository Link -->
        <li class="nav-item">
            <a
                class="nav-link"
                href="https://github.com/asterodigital/bootstrap-admin-template"
                target="_blank"
                rel="noopener noreferrer">
                <i class="ri-github-line"></i> <span>GitHub Repository</span>
            </a>
        </li>
    </ul>
</nav>