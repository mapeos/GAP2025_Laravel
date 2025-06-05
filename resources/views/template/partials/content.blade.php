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
                        <h3>{{ $stats['totalCursos'] }}</h3>
                        <p>Cursos Totales</p>
                      </div>
                      <div class="small-box-icon"><i class="ri-book-open-fill"></i></div>
                      <a href="{{ route('admin.cursos.index') }}" class="small-box-footer">Ver Cursos <i class="ri-arrow-right-line"></i></a>
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
                <!-- Usuarios Pendientes -->
                <div class="row g-4 mb-4">
                  <div class="col-xl-4">
                    <div class="card h-100">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Usuarios Pendientes</h5>
                        <a href="{{ route('admin.users.pendent') }}" class="btn btn-sm btn-warning">Ver todos</a>
                      </div>
                      <div class="card-body d-flex flex-column">
                        <div class="position-relative px-3 flex-grow-1 overflow-y-auto" data-simplebar>
                          @php
                            $pendientes = \App\Models\User::where('status', 'pendiente')->orderBy('created_at', 'desc')->get();
                          @endphp
                          @forelse($pendientes as $user)
                            <div class="position-relative pb-4 border-start border-1">
                              <div class="position-absolute start-0 translate-middle-x rounded-circle bg-warning" style="width: 10px; height: 10px; top: 6px"></div>
                              <div class="ms-4">
                                <div class="d-flex justify-content-between">
                                  <span>{{ $user->name }} <small class="text-muted">({{ $user->email }})</small></span>
                                  <small class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                              </div>
                            </div>
                          @empty
                            <div class="text-center text-muted">No hay usuarios pendientes.</div>
                          @endforelse
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Financial Transactions -->
                  <div class="col-xl-4">
                    <div class="card h-100">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Cursos</h5>
                        <a href="{{ route('admin.cursos.index') }}" class="btn btn-sm btn-primary">Ver todos</a>
                      </div>
                      <div class="card-body d-flex flex-column" style="max-height: 440px; overflow-y: auto">
                        <div class="flex-grow-1 px-3" data-simplebar>
                          @php
                            $cursos = \App\Models\Curso::orderBy('fechaInicio', 'desc')->get();
                          @endphp
                          @forelse($cursos as $curso)
                            <div class="mb-3 pb-3 border-bottom">
                              <div class="d-flex align-items-center">
                                <div class="p-2 rounded bg-success bg-opacity-10">
                                  <i class="ri-book-open-fill text-success fs-4"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                  <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                      <h6 class="mb-0">{{ $curso->titulo }}</h6>
                                      <small class="text-muted">{{ $curso->fechaInicio }} - {{ $curso->fechaFin }}</small>
                                    </div>
                                    <span class="text-primary">Plazas: {{ $curso->plazas }}</span>
                                  </div>
                                  <div class="text-muted small">{{ $curso->descripcion }}</div>
                                </div>
                              </div>
                            </div>
                          @empty
                            <div class="text-center text-muted">No hay cursos registrados.</div>
                          @endforelse
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Lead Source Analytics -->
                  <div class="col-xl-4">
                    <div class="card h-100">
                      <div class="card-header">
                        <h5 class="card-title mb-0">Procedencia de Usuarios Registrados</h5>
                      </div>
                      <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 250px">
                          <canvas id="leadSourceChart"></canvas>
                        </div>
                        <div class="mt-4">
                          <div class="d-flex align-items-center mb-2">
                            <div class="legend-dot bg-primary"></div>
                            <span class="ms-2">Web</span> <span class="ms-auto" id="percent-web">0%</span>
                          </div>
                          <div class="d-flex align-items-center mb-2">
                            <div class="legend-dot bg-success"></div>
                            <span class="ms-2">API</span> <span class="ms-auto" id="percent-api">0%</span>
                          </div>
                          <div class="d-flex align-items-center">
                            <div class="legend-dot bg-warning"></div>
                            <span class="ms-2">Otro</span> <span class="ms-auto" id="percent-otro">0%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>








