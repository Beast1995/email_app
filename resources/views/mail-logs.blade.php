<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Logs - {{ config('app.name', 'Bulk Email App') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-envelope-open-text me-2"></i>
                            {{ config('app.name', 'Bulk Email App') }}
                        </h4>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                        <a class="nav-link" href="{{ route('templates.index') }}">
                            <i class="fas fa-file-alt me-2"></i>
                            Email Templates
                        </a>
                        <a class="nav-link" href="{{ route('campaigns.index') }}">
                            <i class="fas fa-bullhorn me-2"></i>
                            Campaigns
                        </a>
                        <a class="nav-link" href="{{ route('test-email') }}">
                            <i class="fas fa-vial me-2"></i>
                            Test Email
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-list me-2"></i>Email Logs</h2>
                        <div>
                            <form action="{{ route('resend-failed') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning me-2" onclick="return confirm('Resend all failed emails?')">
                                    <i class="fas fa-redo me-1"></i>Resend Failed
                                </button>
                            </form>
                            <a href="{{ route('test-email') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Test
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('errors'))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach(session('errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            @if($logs->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Email</th>
                                                <th>Campaign</th>
                                                <th>Status</th>
                                                <th>Sent At</th>
                                                <th>Error Message</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($logs as $log)
                                                <tr>
                                                    <td>{{ $log->to_email }}</td>
                                                    <td>{{ $log->campaign->name ?? 'N/A' }}</td>
                                                    <td>
                                                        @switch($log->status)
                                                            @case('sent')
                                                                <span class="badge bg-success">Sent</span>
                                                                @break
                                                            @case('delivered')
                                                                <span class="badge bg-success">Delivered</span>
                                                                @break
                                                            @case('failed')
                                                                <span class="badge bg-danger">Failed</span>
                                                                @break
                                                            @case('bounced')
                                                                <span class="badge bg-warning">Bounced</span>
                                                                @break
                                                            @case('spam')
                                                                <span class="badge bg-danger">Spam</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary">{{ $log->status }}</span>
                                                        @endswitch
                                                    </td>
                                                    <td>{{ $log->sent_at ? $log->sent_at->format('M d, Y H:i:s') : 'N/A' }}</td>
                                                    <td>
                                                        @if($log->error_message)
                                                            <small class="text-danger">{{ $log->error_message }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No email logs found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 