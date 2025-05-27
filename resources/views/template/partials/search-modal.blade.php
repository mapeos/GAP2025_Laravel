    <div
        class="modal search-modal fade"
        id="searchModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="searchModalLabel"
        aria-hidden="true"
        data-bs-backdrop="true"
        data-bs-keyboard="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header d-flex gap-3">
                    <div class="search-wrapper flex-grow-1">
                        <i class="ri-search-line search-icon"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="Type to search..." autofocus />
                        <div class="search-shortcut-indicator d-none d-sm-flex align-items-center gap-2"><kbd>ESC</kbd></div>
                    </div>
                </div>
                <div class="modal-body p-3" data-simplebar>
                    <div class="quick-links mb-4">
                        <h6 class="fw-semibold mb-3">Quick Links</h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <a href="/pages/dashboard" class="quick-link-card">
                                    <i class="ri-dashboard-line"></i>
                                    <div>
                                        <h6 class="mb-0">Dashboard</h6>
                                        <span class="text-muted fs-13">Analytics Overview</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="/pages/users/profile" class="quick-link-card">
                                    <i class="ri-user-line"></i>
                                    <div>
                                        <h6 class="mb-0">Profile</h6>
                                        <span class="text-muted fs-13">View your profile</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="/pages/settings" class="quick-link-card">
                                    <i class="ri-settings-4-line"></i>
                                    <div>
                                        <h6 class="mb-0">Settings</h6>
                                        <span class="text-muted fs-13">Manage preferences</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="/pages/help" class="quick-link-card">
                                    <i class="ri-question-line"></i>
                                    <div>
                                        <h6 class="mb-0">Help Center</h6>
                                        <span class="text-muted fs-13">Get support</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="recent-searches">
                        <h6 class="fw-semibold mb-3">Recent Searches</h6>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="ri-time-line me-2 text-muted"></i>
                                Analytics Dashboard
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="ri-time-line me-2 text-muted"></i>
                                User Settings
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="ri-time-line me-2 text-muted"></i>
                                Email Templates
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Initialize search modal functionality after Bootstrap is loaded
        window.addEventListener("load", () => {
            // Initialize Bootstrap modal
            const searchModalEl = document.getElementById("searchModal");
            const searchModal = new bootstrap.Modal(searchModalEl);

            // Handle keyboard shortcuts
            document.addEventListener("keydown", (e) => {
                // Check for Ctrl+K or Cmd+K
                if ((e.ctrlKey || e.metaKey) && e.key === "k") {
                    e.preventDefault();
                    searchModal.show();
                }

                // Close modal on ESC
                if (e.key === "Escape") {
                    if (searchModal) {
                        searchModal.hide();
                    }
                }
            });

            // Focus input when modal opens
            searchModalEl.addEventListener("shown.bs.modal", () => {
                document.getElementById("searchInput").focus();
            });
        });
    </script>