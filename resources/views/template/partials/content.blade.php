<div class="col">
                <!-- Tarjetas con enlaces a los CRUD de Usuarios, Cursos y Noticias -->
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
                <!-- Calendario de eventos -->
                <div class="row g-4 mb-4">
                  <div class="col-xl-8">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <div><h5 class="card-title mb-0">Calendario de eventos</h5></div>
                        </div>
                        @include('admin.events.calendar-dashboard')
                      </div>
                    </div>
                  </div>
                  <!-- Usuarios Recientes -->
                  <div class="col-xl-4">
                    <div class="card h-100">
                      <div class="card-header">
                        <h5 class="card-title mb-0">Usuarios Recientes</h5>
                      </div>
                      <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                          @php
                            $usuarios = \App\Models\User::orderBy('created_at', 'desc')->limit(8)->get();
                          @endphp
                          @forelse($usuarios as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-3">
                              <div>
                                <i class="ri-user-3-fill text-primary me-2"></i>
                                <a href="{{ route('admin.users.show', $user) }}" class="text-decoration-none">{{ $user->name }}</a>
                                <span class="text-muted small">({{ $user->email }})</span>
                              </div>
                              <span class="badge bg-{{ $user->status === 'activo' ? 'success' : 'warning' }}">{{ ucfirst($user->status) }}</span>
                            </li>
                          @empty
                            <li class="list-group-item text-muted">No hay usuarios registrados.</li>
                          @endforelse
                        </ul>
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
                  <!-- Tarjeta de cursos -->
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
                                      <h6 class="mb-0">
                                        <a href="{{ route('admin.cursos.show', $curso->id) }}" class="text-decoration-none">{{ $curso->titulo }}</a>
                                      </h6>
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
                  <!-- Noticias recientes -->
                  <div class="col-xl-4">
                    <div class="card h-100">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Noticias Recientes</h5>
                        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-info">Ver todas</a>
                      </div>
                      <div class="card-body d-flex flex-column" style="max-height: 440px; overflow-y: auto">
                        <div class="flex-grow-1 px-3" data-simplebar>
                          @php
                            $noticias = \App\Models\News::orderBy('created_at', 'desc')->limit(8)->get();
                          @endphp
                          @forelse($noticias as $news)
                            <div class="mb-3 pb-3 border-bottom">
                              <div class="d-flex align-items-center">
                                <div class="p-2 rounded bg-info bg-opacity-10">
                                  <i class="ri-newspaper-fill text-info fs-4"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                  <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                      <h6 class="mb-0">
                                        <a href="{{ route('admin.news.show', $news) }}" class="text-decoration-none">{{ $news->titulo }}</a>
                                      </h6>
                                      <small class="text-muted">{{ $news->created_at->format('d/m/Y') }}</small>
                                    </div>
                                  </div>
                                  <div class="text-muted small">{{ Str::limit($news->contenido, 60) }}</div>
                                </div>
                              </div>
                            </div>
                          @empty
                            <div class="text-center text-muted">No hay noticias registradas.</div>
                          @endforelse
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>








