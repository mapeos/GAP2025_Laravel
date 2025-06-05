<nav class="nav-tree">
    <ul class="list-unstyled">
        <!-- Forms And Tables Section -->
        <li class="nav-section"><span class="nav-section-text text-uppercase px-2">Main</span></li>
        <!-- Dashboard -->
        <li class="nav-item active">
            <a class="nav-link" href="/pages/dashboard">
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


        <!-- Analytics -->
        <li class="nav-item">
            <a class="nav-link" href="/pages/analytics">
                <i class="ri-line-chart-line"></i> <span>Analytics</span>
            </a>
        </li>
        <!-- Interface Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-layout-grid-line"></i> <span>Interface</span> <i class="ri-arrow-right-s-line"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/accordion"> <span>Accordion</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/alerts"> <span>Alerts</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/badges"> <span>Badges</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/breadcrumb"> <span>Breadcrumb</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/buttons"> <span>Buttons</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/button-group"> <span>Button Group</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/cards"> <span>Cards</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/carousel"> <span>Carousel</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/close-button"> <span>Close Button</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/collapse"> <span>Collapse</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/colors"> <span>Colors</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/dropdowns"> <span>Dropdowns</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/info-box"> <span>Info Box</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/list-group"> <span>List Group</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/modal"> <span>Modal</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/navbar"> <span>Navbar</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/navs-and-tabs"> <span>Navs and tabs</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/offcanvas"> <span>Offcanvas</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/pagination"> <span>Pagination</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/placeholders"> <span>Placeholders</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/popovers"> <span>Popovers</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/progress"> <span>Progress</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/scrollspy"> <span>Scrollspy</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/small-box"> <span>Small Box</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/spinners"> <span>Spinners</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/text-animations"> <span>Text Animations</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/toasts"> <span>Toasts</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/tooltips"> <span>Tooltips</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/interface/typography"> <span>Typography</span> </a>
                </li>
            </ul>
        </li>
        <!-- CURSOS PARTICIPANTES INSCRIPCIONES -->
        <li class="nav-section"><span class="nav-section-text text-uppercase px-2">Admin CURSOS</span></li>
        <!-- CURSOS Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-file-list-3-line"></i> <span>Cursos</span> <i class="ri-arrow-right-s-line ms-auto"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.cursos.index') }}">
                        <span>Ver Cursos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.cursos.create') }}"> 
                        <span>Crear Cursos</span>
                     </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="/pages/forms/switches"> <span>Switches</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/forms/vertical"> <span>Vertical</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/forms/horizontal"> <span>Horizontal</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/forms/validation"> <span>Validation</span> </a>
                </li>  -->
            </ul>
        </li>
        <!-- PARTICIPANTES Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-table-line"></i> <span>Participantes</span> <i class="ri-arrow-right-s-line ms-auto"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="/pages/tables/basic"> <span>Listar Participantes</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/tables/enhanced"> <span>Crear Participantes</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/tables/advanced"> <span>Advanced Tables</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/tables/datatable"> <span>Datatable</span> </a>
                </li>
            </ul>
        </li>
        <!-- INSCRIPCIONES Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-file-list-3-line"></i> <span>Inscripciones</span> <i class="ri-arrow-right-s-line ms-auto"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item">
                    <a class="nav-link" href="/pages/forms/form-controls"> <span>Inscribir</span> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/forms/select"> <span>Bajas</span> </a>
                </li>
            </ul>
        </li>
        <!-- Pages Section -->
        <li class="nav-section"><span class="nav-section-text text-uppercase px-2">Pages</span></li>
        <!-- Administración Section -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-settings-3-line"></i> <span>Administración</span>
                <i class="ri-arrow-right-s-line"></i>
            </a>
            <ul class="submenu">

                <!-- Users Section -->
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="#">
                        <i class="ri-user-line"></i> <span>Usuarios</span>
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                    <ul class="submenu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users.create') }}">
                                <span>Añadir Usuario</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users.index') }}">
                                <span>Lista Usuarios</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pages/users/profile">
                                <span>Perfiles</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pages/users/security">
                                <span>Seguridad</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Roles & Permissions Section -->
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="#">
                        <i class="ri-shield-user-line"></i> <span>Roles & Permisos</span>
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                    <ul class="submenu">

                        <!-- Roles Subsection -->
                        <li class="nav-item has-submenu">
                            <a class="nav-link" href="#">
                                <span>Roles</span>
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                            <ul class="submenu">
                                <li class="nav-item">
                                    <a class="nav-link" href="/pages/roles-permissions/roles/list">
                                        <span>Lista</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/pages/roles-permissions/roles/add">
                                        <span>Añadir</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Permissions Subsection -->
                        <li class="nav-item has-submenu">
                            <a class="nav-link" href="#">
                                <span>Permisos</span>
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                            <ul class="submenu">
                                <li class="nav-item">
                                    <a class="nav-link" href="/pages/roles-permissions/permissions/list">
                                        <span>Lista</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/pages/roles-permissions/permissions/add">
                                        <span>Añadir</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Groups Subsection -->
                        <li class="nav-item has-submenu">
                            <a class="nav-link" href="#">
                                <span>Grupos</span>
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                            <ul class="submenu">
                                <li class="nav-item">
                                    <a class="nav-link" href="/pages/roles-permissions/groups/list">
                                        <span>Lista</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/pages/roles-permissions/groups/add">
                                        <span>Añadir</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>

            </ul>
        </li>

        <!-- Gestión de pagos -->
        <li class="nav-item has-submenu parent">
            <a class="nav-link" href="#">
                <i class="ri-lock-line"></i> <span>Gestión de Pagos</span> <i class="ri-arrow-right-s-line"></i>
            </a>
            <ul class="submenu">
                <li class="nav-item has-submenu">
                    <a class="nav-link" href="#"> <span>Pagos</span> <i class="ri-arrow-right-s-line"></i> </a>
                    <ul class="submenu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/pagos') }}">
                                <span>Gestión</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pages/authentication/basic/register" target="_blank">
                                <span>Lista</span>
                            </a>
                        </li>  
                    </ul>
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