   <!-- Theme Toggle Button -->
   <button class="btn p-0 border-0 shadow-none" id="theme-toggle"><i class="ri-sun-line fs-5"></i></button>

   <!-- Notifications Dropdown -->
   <div class="notifications-dropdown">
       <button
           class="btn p-0 border-0 shadow-none position-relative"
           data-bs-toggle="dropdown"
           aria-expanded="false">
           <i class="ri-notification-3-line fs-5"></i>
           <span
               class="badge topbar-badge bg-danger fw-medium position-absolute rounded-pill start-100 translate-middle">
               3
           </span>
       </button>
       <div class="dropdown-menu dropdown-menu-end notifications-menu overflow-visible p-0">
           <!-- Header -->
           <div class="notifications-header p-3 border-bottom d-flex align-items-center justify-content-between">
               <h6 class="mb-0">Notifications</h6>
               <div class="d-flex align-items-center gap-2">
                   <span class="badge bg-primary-subtle text-primary rounded-pill">3 New</span>
                   <button
                       class="btn btn-icon btn-sm btn-text"
                       data-toggle="tooltip"
                       data-toggle-position="bottom"
                       title="Mark all as read"
                       type="button"
                       aria-label="Mark all as read">
                       <i class="ri-mail-open-line"></i>
                   </button>
               </div>
           </div>
           <!-- Notifications List -->
           <div class="notifications-list" data-simplebar>
               <!-- Message Notification -->
               <div class="dropdown-item notification-item px-3 py-2 border-bottom">
                   <div class="d-flex">
                       <div class="flex-shrink-0">
                           <div class="avatar avatar-sm">
                               <img src="{{ asset('/admin/img/avatars/avatar1.jpg') }}" alt="User" class="rounded-circle" />
                           </div>
                       </div>
                       <div class="flex-grow-1 ms-3">
                           <h6 class="mb-1 fs-14">New message from Sarah</h6>
                           <p class="text-muted mb-1 fs-12">Hey, check out the new dashboard...</p>
                           <small class="text-muted">5 mins ago</small>
                       </div>
                       <div class="notification-actions">
                           <button class="btn notification-close shadow-none"><i class="ri-close-line"></i></button>
                           <div class="notification-indicator"></div>
                       </div>
                   </div>
               </div>
               <!-- System Update Notification (Read) -->
               <div class="dropdown-item notification-item px-3 py-2 border-bottom">
                   <div class="d-flex">
                       <div class="flex-shrink-0">
                           <div class="avatar avatar-sm bg-info-subtle"><i class="ri-refresh-line text-info"></i></div>
                       </div>
                       <div class="flex-grow-1 ms-3">
                           <h6 class="mb-1 fs-14">System update completed</h6>
                           <p class="text-muted mb-1 fs-12">Your system is now up to date</p>
                           <small class="text-muted">1 hour ago</small>
                       </div>
                       <div class="notification-actions">
                           <button class="btn notification-close shadow-none"><i class="ri-close-line"></i></button>
                           <div class="notification-indicator bg-secondary-subtle"></div>
                       </div>
                   </div>
               </div>
               <!-- Order Notification -->
               <div class="dropdown-item notification-item px-3 py-2 border-bottom">
                   <div class="d-flex">
                       <div class="flex-shrink-0">
                           <div class="avatar avatar-sm bg-primary-subtle">
                               <i class="ri-shopping-cart-line text-primary"></i>
                           </div>
                       </div>
                       <div class="flex-grow-1 ms-3">
                           <h6 class="mb-1 fs-14">New order received</h6>
                           <p class="text-muted mb-1 fs-12">Order #123 has been placed</p>
                           <small class="text-muted">2 hours ago</small>
                       </div>
                       <div class="notification-actions">
                           <button class="btn notification-close shadow-none"><i class="ri-close-line"></i></button>
                           <div class="notification-indicator"></div>
                       </div>
                   </div>
               </div>
               <!-- Security Alert Notification -->
               <div class="dropdown-item notification-item px-3 py-2 border-bottom">
                   <div class="d-flex">
                       <div class="flex-shrink-0">
                           <div class="avatar avatar-sm bg-danger-subtle">
                               <i class="ri-shield-keyhole-line text-danger"></i>
                           </div>
                       </div>
                       <div class="flex-grow-1 ms-3">
                           <h6 class="mb-1 fs-14">Security alert</h6>
                           <p class="text-muted mb-1 fs-12">Unusual login attempt detected</p>
                           <small class="text-muted">3 hours ago</small>
                       </div>
                       <div class="notification-actions">
                           <button class="btn notification-close shadow-none"><i class="ri-close-line"></i></button>
                           <div class="notification-indicator"></div>
                       </div>
                   </div>
               </div>
           </div>
           <!-- Footer -->
           <div class="notifications-footer p-2 text-center border-top">
               <a href="/pages/users/notifications" class="btn btn-primary btn-sm rounded-pill w-100">
                   View All Notifications
               </a>
           </div>
       </div>
   </div>
   <!-- User Profile Button -->
   <div class="user-profile">
       <button
           class="btn p-0 border-0 shadow-none d-flex align-items-center"
           type="button"
           data-bs-toggle="offcanvas"
           data-bs-target="#userProfileOffcanvas"
           aria-controls="userProfileOffcanvas">
           <div class="avatar position-relative">
               <img src="{{ asset('/admin/img/avatars/avatar2.jpg') }}" alt="User" class="rounded-circle" />
               <span class="online-indicator"></span>
           </div>
       </button>
   </div>