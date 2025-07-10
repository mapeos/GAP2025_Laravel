<!-- Top Section -->
   <div class="offcanvas-top">
       <div class="user-profile-header text-center">
           <div class="user-profile-cover"></div>
           <div class="user-profile-avatar">
               <div class="avatar-wrapper">
                   <img src="{{ asset('/admin/img/avatars/avatar2.jpg') }}" alt="User" class="rounded-circle" />
                   <span class="status-badge online"></span>
               </div>
           </div>
           <div class="user-profile-info">
               <h5 class="user-name">{{ auth()->user()?->name ?? '' }}</h5>
               <p class="user-email">{{ auth()->user()?->email ?? '' }}</p>
           </div>
           <button
               type="button"
               class="btn-close-custom d-flex align-items-center justify-content-center"
               data-bs-dismiss="offcanvas"
               aria-label="Close">
               <i class="ri-close-line"></i>
           </button>
       </div>
   </div>

   <!-- Content Section -->
   <div class="offcanvas-content">
       <nav class="nav-tree p-0">
           <ul class="list-unstyled">
               <li class="nav-item">
                   <a class="nav-link" href="/pages/users/profile">
                       <i class="ri-user-line"></i> <span>Profile</span>
                   </a>
               </li>
               <li class="nav-item">
                   <a class="nav-link" href="/pages/users/security">
                       <i class="ri-shield-keyhole-line"></i> <span>Security</span>
                   </a>
               </li>
               <li class="nav-item">
                   <a class="nav-link" href="/pages/users/billing">
                       <i class="ri-wallet-3-line"></i> <span>Billing</span>
                   </a>
               </li>
               <li class="nav-item">
                   <a class="nav-link" href="/pages/users/notifications">
                       <i class="ri-notification-4-line"></i> <span>Notifications</span>
                       <span class="badge text-bg-danger ms-2">3</span>
                   </a>
               </li>
               @if(Auth::user()->hasRole('Facultativo'))
               <li class="nav-item">
                   <a class="nav-link" href="{{ route('facultativo.calendario') }}">
                       <i class="ri-calendar-2-line"></i> <span>Calendario</span>
                   </a>
               </li>
               @endif
           </ul>
       </nav>
   </div>
   <!-- Bottom Section -->
   <div class="offcanvas-bottom">
      
       
       <!-- Logout Button -->
       <div class="logout-section">
           <form id="logout-form-offcanvas" action="{{ route('logout') }}" method="POST" style="display: none;">
               @csrf
           </form>
           <a href="#" class="btn-logout d-flex align-items-center justify-content-center"
              onclick="event.preventDefault(); document.getElementById('logout-form-offcanvas').submit();">
               <i class="ri-logout-box-r-line"></i> <span>Log Out</span>
           </a>
       </div>
   </div>