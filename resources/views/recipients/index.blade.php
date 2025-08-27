<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipients - {{ config('app.name', 'Bulk Email App') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; margin: 2px 0; border-radius: 8px; transition: all 0.3s ease; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background-color: rgba(255,255,255,0.1); }
        .main-content { background-color: #f8f9fa; min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar p-3">
                <div class="text-center mb-4">
                    <h4 class="text-white"><i class="fas fa-envelope-open-text me-2"></i>{{ config('app.name', 'Bulk Email App') }}</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a class="nav-link" href="{{ route('templates.index') }}"><i class="fas fa-file-alt me-2"></i>Email Templates</a>
                    <a class="nav-link" href="{{ route('campaigns.index') }}"><i class="fas fa-bullhorn me-2"></i>Campaigns</a>
                    <a class="nav-link active" href="{{ route('recipients.index') }}"><i class="fas fa-users me-2"></i>Recipients</a>
                </nav>
            </div>
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-users me-2"></i>Manage Recipients</h2>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="mb-0"><i class="fas fa-keyboard me-2"></i>Manual Entry</h5></div>
                            <div class="card-body">
                                <form action="{{ route('recipients.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" name="email" class="form-control" placeholder="user@example.com" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="Optional">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Group</label>
                                        <select name="email_group_id" class="form-select">
                                            <option value="">-- None --</option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}" {{ $group->is_active ? '' : 'disabled' }}>
                                                    {{ $group->name }} {{ $group->is_active ? '' : '(inactive)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                    <button class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Recipient</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>Upload CSV</h5></div>
                            <div class="card-body">
                                <form action="{{ route('recipients.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">CSV File *</label>
                                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                                        <small class="text-muted">Columns: email,name (header optional)</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Assign Group</label>
                                        <select name="email_group_id" class="form-select">
                                            <option value="">-- None --</option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}" {{ $group->is_active ? '' : 'disabled' }}>
                                                    {{ $group->name }} {{ $group->is_active ? '' : '(inactive)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_active2" name="is_active" checked>
                                        <label class="form-check-label" for="is_active2">Active by default</label>
                                    </div>
                                    <button class="btn btn-primary"><i class="fas fa-upload me-1"></i>Upload</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="mb-0"><i class="fas fa-table me-2"></i>Recipients</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recipients as $r)
                                    <tr>
                                        <td>{{ $r->email }}</td>
                                        <td>{{ $r->name ?: '-' }}</td>
                                        <td>{{ $r->group->name ?? '-' }}</td>
                                        <td>
                                            @if($r->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('recipients.toggle', $r) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    <button class="btn btn-sm {{ $r->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="Toggle status">
                                                        @if($r->is_active)
                                                            <i class="fas fa-toggle-off"></i> Deactivate
                                                        @else
                                                            <i class="fas fa-toggle-on"></i> Activate
                                                        @endif
                                                    </button>
                                                </form>
                                                <form action="{{ route('recipients.destroy', $r) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this recipient?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $recipients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 