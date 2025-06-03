<div class="col">
                <!-- Key Metrics Overview Cards -->
                <div class="row g-4 mb-4">
                  <div class="col-xl-3 col-md-6">
                    <div class="small-box text-bg-primary">
                      <div class="inner">
                        <h3>{{ $stats['totalUsers'] }}</h3>
                        <p>Usuarios Totales</p>
                      </div>
                      <div class="small-box-icon"><i class="ri-user-fill"></i></div>
                      <a href="{{ route('admin.users.index') }}" class="small-box-footer">Ver Usuarios <i class="ri-arrow-right-line"></i></a>
                    </div>
                  </div>
                  <div class="col-xl-3 col-md-6">
                    <div class="small-box text-bg-success">
                      <div class="inner">
                        <h3>{{ $stats['activeUsers'] }}</h3>
                        <p>Usuarios Activos</p>
                      </div>
                      <div class="small-box-icon"><i class="ri-user-heart-fill"></i></div>
                      <a href="{{ route('admin.users.index') }}" class="small-box-footer">Ver Detalles <i class="ri-arrow-right-line"></i></a>
                    </div>
                  </div>
                  <div class="col-xl-3 col-md-6">
                    <div class="small-box text-bg-info">
                      <div class="inner">
                        <h3>{{ $stats['totalNews'] }}</h3>
                        <p>Noticias Totales</p>
                      </div>
                      <div class="small-box-icon"><i class="ri-newspaper-fill"></i></div>
                      <a href="{{ route('admin.news.index') }}" class="small-box-footer">Ver Noticias <i class="ri-arrow-right-line"></i></a>
                    </div>
                  </div>
                  <div class="col-xl-3 col-md-6">
                    <div class="small-box text-bg-warning">
                      <div class="inner">
                        <h3>{{ $stats['pendingUsers'] }}</h3>
                        <p>Usuarios Pendientes</p>
                      </div>
                      <div class="small-box-icon"><i class="ri-user-add-fill"></i></div>
                      <a href="{{ route('admin.users.pendent') }}" class="small-box-footer">Ver Pendientes <i class="ri-arrow-right-line"></i></a>
                    </div>
                  </div>
                </div>
                <!-- Performance Analytics Section -->
                <div class="row g-4 mb-4">
                  <!-- Revenue Performance Chart -->
                  <div class="col-xl-8">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <div><h5 class="card-title mb-0">Revenue Performance</h5></div>
                          <div class="d-flex gap-1 gap-md-3">
                            <div class="sales-metric">
                              <h3 class="mb-0">8.5%</h3>
                              <p class="text-muted mb-0">Conversion Rate</p>
                            </div>
                            <div class="sales-metric">
                              <h3 class="mb-0">$1,200</h3>
                              <p class="text-muted mb-0">Avg. Transaction</p>
                            </div>
                            <div class="sales-metric">
                              <h3 class="mb-0">$50K</h3>
                              <p class="text-muted mb-0">Monthly Target</p>
                            </div>
                          </div>
                        </div>
                        <div class="chart-container" style="position: relative; height: 300px">
                          <canvas id="salesPerformanceChart"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Business Performance Metrics -->
                  <div class="col-xl-4">
                    <div class="card h-100">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Business Growth</h5>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Current Year
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Current Year</a></li>
                            <li><a class="dropdown-item" href="#">Previous Year</a></li>
                            <li><a class="dropdown-item" href="#">All Time</a></li>
                          </ul>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 330px">
                          <canvas id="performanceChart"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Activity and Transaction Section -->
                <div class="row g-4 mb-4">
                  <!-- System Activities Log -->
                  <div class="col-xl-4">
                    <div class="card h-100">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">System Activities</h5>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Last 24 Hours
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Last 24 Hours</a></li>
                            <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                            <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                          </ul>
                        </div>
                      </div>
                      <div class="card-body d-flex flex-column">
                        <div class="position-relative px-3 flex-grow-1 overflow-y-auto" data-simplebar>
                          <div class="position-relative pb-4 border-start border-1">
                            <div
                              class="position-absolute start-0 translate-middle-x rounded-circle bg-primary"
                              style="width: 10px; height: 10px; top: 6px"
                            ></div>
                            <div class="ms-4">
                              <div class="d-flex justify-content-between">
                                <span>Security Patch Installed</span> <small class="text-muted">Just Now</small>
                              </div>
                            </div>
                          </div>
                          <div class="position-relative pb-4 border-start border-1">
                            <div
                              class="position-absolute start-0 translate-middle-x rounded-circle bg-success"
                              style="width: 10px; height: 10px; top: 6px"
                            ></div>
                            <div class="ms-4">
                              <div class="d-flex justify-content-between">
                                <span>New Premium User Signup</span> <small class="text-muted">2 min ago</small>
                              </div>
                            </div>
                          </div>
                          <div class="position-relative pb-4 border-start border-1">
                            <div
                              class="position-absolute start-0 translate-middle-x rounded-circle bg-danger"
                              style="width: 10px; height: 10px; top: 6px"
                            ></div>
                            <div class="ms-4">
                              <div class="d-flex justify-content-between">
                                <span>Database Backup Complete</span> <small class="text-muted">10:30</small>
                              </div>
                            </div>
                          </div>
                          <div class="position-relative pb-4 border-start border-1">
                            <div
                              class="position-absolute start-0 translate-middle-x rounded-circle bg-warning"
                              style="width: 10px; height: 10px; top: 6px"
                            ></div>
                            <div class="ms-4">
                              <div class="d-flex justify-content-between">
                                <span>Payment System Updated</span> <small class="text-muted">11:15</small>
                              </div>
                            </div>
                          </div>
                          <div class="position-relative pb-4 border-start border-1">
                            <div
                              class="position-absolute start-0 translate-middle-x rounded-circle bg-info"
                              style="width: 10px; height: 10px; top: 6px"
                            ></div>
                            <div class="ms-4">
                              <div class="d-flex justify-content-between">
                                <span>Enterprise Order Received</span> <small class="text-muted">12:30</small>
                              </div>
                            </div>
                          </div>
                          <div class="position-relative pb-4 border-start border-1">
                            <div
                              class="position-absolute start-0 translate-middle-x rounded-circle bg-primary"
                              style="width: 10px; height: 10px; top: 6px"
                            ></div>
                            <div class="ms-4">
                              <div class="d-flex justify-content-between">
                                <span>Database Optimization</span> <small class="text-muted">13:45</small>
                              </div>
                            </div>
                          </div>
                          <div class="position-relative pb-4 border-start border-1">
                            <div
                              class="position-absolute start-0 translate-middle-x rounded-circle bg-success"
                              style="width: 10px; height: 10px; top: 6px"
                            ></div>
                            <div class="ms-4">
                              <div class="d-flex justify-content-between">
                                <span>API Integration Success</span> <small class="text-muted">14:20</small>
                              </div>
                            </div>
                          </div>
                          <div class="position-relative pb-4 border-start border-1">
                            <div
                              class="position-absolute start-0 translate-middle-x rounded-circle bg-warning"
                              style="width: 10px; height: 10px; top: 6px"
                            ></div>
                            <div class="ms-4">
                              <div class="d-flex justify-content-between">
                                <span>Security Audit Initiated</span> <small class="text-muted">15:00</small>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-footer text-center">
                        <a href="#" class="btn btn-sm btn-light"
                          >View All Activities <i class="ri-arrow-right-line"></i
                        ></a>
                      </div>
                    </div>
                  </div>
                  <!-- Financial Transactions -->
                  <div class="col-xl-4">
                    <div class="card h-100">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Financial Transactions</h5>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Current Week
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">Current Week</a></li>
                            <li><a class="dropdown-item" href="#">Current Month</a></li>
                          </ul>
                        </div>
                      </div>
                      <div class="card-body d-flex flex-column" style="max-height: 440px; overflow-y: auto">
                        <div class="flex-grow-1 px-3" data-simplebar>
                          <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                              <div class="p-2 rounded bg-warning bg-opacity-10">
                                <i class="ri-building-2-fill text-warning fs-4"></i>
                              </div>
                              <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <h6 class="mb-0">Office Utilities</h6>
                                    <small class="text-muted">Today, 10:30 AM</small>
                                  </div>
                                  <span class="text-danger">-$125.00</span>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                              <div class="p-2 rounded bg-success bg-opacity-10">
                                <i class="ri-bank-card-fill text-success fs-4"></i>
                              </div>
                              <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <h6 class="mb-0">Monthly Salary</h6>
                                    <small class="text-muted">Today, 9:00 AM</small>
                                  </div>
                                  <span class="text-success">+$2,450.00</span>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                              <div class="p-2 rounded bg-primary bg-opacity-10">
                                <i class="ri-shopping-cart-2-fill text-primary fs-4"></i>
                              </div>
                              <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <h6 class="mb-0">Office Supplies</h6>
                                    <small class="text-muted">Yesterday, 2:15 PM</small>
                                  </div>
                                  <span class="text-danger">-$86.22</span>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                              <div class="p-2 rounded bg-info bg-opacity-10">
                                <i class="ri-taxi-fill text-info fs-4"></i>
                              </div>
                              <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <h6 class="mb-0">Travel Expenses</h6>
                                    <small class="text-muted">Yesterday, 1:30 PM</small>
                                  </div>
                                  <span class="text-danger">-$24.50</span>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                              <div class="p-2 rounded bg-success bg-opacity-10">
                                <i class="ri-briefcase-4-fill text-success fs-4"></i>
                              </div>
                              <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <h6 class="mb-0">Project Payment</h6>
                                    <small class="text-muted">Yesterday, 11:45 AM</small>
                                  </div>
                                  <span class="text-success">+$350.00</span>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                              <div class="p-2 rounded bg-warning bg-opacity-10">
                                <i class="ri-restaurant-2-fill text-warning fs-4"></i>
                              </div>
                              <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <h6 class="mb-0">Team Lunch</h6>
                                    <small class="text-muted">Yesterday, 8:30 AM</small>
                                  </div>
                                  <span class="text-danger">-$45.80</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Lead Source Analytics -->
                  <div class="col-xl-4">
                    <div class="card h-100">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Lead Acquisition</h5>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Current Day
                          </button>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Current Day</a></li>
                            <li><a class="dropdown-item" href="#">Current Week</a></li>
                            <li><a class="dropdown-item" href="#">Current Month</a></li>
                          </ul>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 250px">
                          <canvas id="leadSourceChart"></canvas>
                        </div>
                        <div class="mt-4">
                          <div class="d-flex align-items-center mb-2">
                            <div class="legend-dot bg-primary"></div>
                            <span class="ms-2">Social Media</span> <span class="ms-auto">25%</span>
                          </div>
                          <div class="d-flex align-items-center mb-2">
                            <div class="legend-dot bg-success"></div>
                            <span class="ms-2">Organic Search</span> <span class="ms-auto">35%</span>
                          </div>
                          <div class="d-flex align-items-center mb-2">
                            <div class="legend-dot bg-info"></div>
                            <span class="ms-2">Direct Calls</span> <span class="ms-auto">20%</span>
                          </div>
                          <div class="d-flex align-items-center">
                            <div class="legend-dot bg-warning"></div>
                            <span class="ms-2">Email Campaign</span> <span class="ms-auto">20%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>








