<div class="topbar container-fluid px-3">
  <nav class="d-flex align-items-center justify-content-between py-2">
    <div class="d-flex align-items-center gap-4">
      <!-- Aquí puedes añadir un logo o título si lo deseas -->
      <span class="fw-bold fs-5 text-primary">
        {{ Auth::user() ? Auth::user()->name : 'Usuario' }}
      </span>
    </div>
    <div class="d-flex align-items-center gap-4">
      @include('template.partials.top-right-buttons')
    </div>
  </nav>
</div>
