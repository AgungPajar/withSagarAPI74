<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administrator')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        body { padding-top: 60px; }
        .import-toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1055;
            width: 350px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('admin.dashboard') ?? '#' }}">Admin Panel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      @auth('admin')
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.jurusan.index') }}">Jurusan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.kelas.index') }}">Kelas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.siswa.index') }}">Siswa</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('admin.ekskul.index') }}">Ekskul</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-link nav-link" type="submit">Logout</button>
            </form>
        </li>
      </ul>
      @endauth
    </div>
  </div>
</nav>

<div class="container">
    @yield('content')
</div>

@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Import Progress Global UI -->
@auth('admin')
<div class="import-toast-container" id="importToastContainer">
    <!-- Toasts will be injected here -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('importToastContainer');
        let activeToasts = {};

        function checkImportStatus() {
            fetch('{{ route('admin.import.status') }}')
                .then(res => res.json())
                .then(data => {
                    const imports = data.imports || {};
                    
                    // Render or update toasts
                    for (const [id, statusData] of Object.entries(imports)) {
                        let toastEl = document.getElementById(`import-toast-${id}`);
                        
                        let percentage = 0;
                        if (statusData.total > 0) {
                            percentage = Math.round((statusData.processed / statusData.total) * 100);
                        }
                        
                        let statusText = 'Processing...';
                        let progressBarClass = 'bg-primary';
                        
                        if (statusData.status === 'completed') {
                            statusText = 'Completed!';
                            progressBarClass = 'bg-success';
                            percentage = 100;
                        } else if (statusData.status === 'failed') {
                            statusText = 'Failed: ' + (statusData.error || 'Unknown error');
                            progressBarClass = 'bg-danger';
                        }
                        
                        if (!toastEl) {
                            toastEl = document.createElement('div');
                            toastEl.id = `import-toast-${id}`;
                            toastEl.className = 'toast show mb-2';
                            toastEl.setAttribute('role', 'alert');
                            toastEl.setAttribute('aria-live', 'assertive');
                            toastEl.setAttribute('aria-atomic', 'true');
                            
                            toastEl.innerHTML = `
                                <div class="toast-header">
                                    <strong class="me-auto">Import Siswa</strong>
                                    <small class="text-muted status-text">${statusText}</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated ${progressBarClass}" role="progressbar" style="width: ${percentage}%;" aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="mt-2 small text-muted text-center progress-counts">
                                        ${statusData.processed} / ${statusData.total}
                                    </div>
                                </div>
                            `;
                            container.appendChild(toastEl);
                            activeToasts[id] = toastEl;
                            
                            // Remove element when closed
                            toastEl.querySelector('.btn-close').addEventListener('click', () => {
                                toastEl.remove();
                                delete activeToasts[id];
                            });
                        } else {
                            // Update existing
                            toastEl.querySelector('.status-text').innerText = statusText;
                            const pb = toastEl.querySelector('.progress-bar');
                            pb.style.width = percentage + '%';
                            pb.className = `progress-bar progress-bar-striped progress-bar-animated ${progressBarClass}`;
                            toastEl.querySelector('.progress-counts').innerText = `${statusData.processed} / ${statusData.total}`;
                            
                            if (statusData.status === 'completed' || statusData.status === 'failed') {
                                pb.classList.remove('progress-bar-animated');
                                // Auto remove after 5 seconds if completed
                                setTimeout(() => {
                                    if (document.body.contains(toastEl)) {
                                        toastEl.remove();
                                        delete activeToasts[id];
                                    }
                                }, 5000);
                            }
                        }
                    }
                    
                    // Call again if there are still active imports
                    let hasActive = false;
                    for (const [id, s] of Object.entries(imports)) {
                        if (s.status === 'pending' || s.status === 'processing') {
                            hasActive = true;
                            break;
                        }
                    }
                    
                    if (hasActive) {
                        setTimeout(checkImportStatus, 3000);
                    } else {
                        // Keep polling slowly just in case a new import is started in another tab
                        setTimeout(checkImportStatus, 10000); 
                    }
                })
                .catch(err => {
                    setTimeout(checkImportStatus, 10000);
                });
        }

        // Initial check
        checkImportStatus();
    });
</script>
@endauth

@stack('scripts')
</body>
</html>
