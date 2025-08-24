<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Details - {{ config('app.name', 'Bulk Email App') }}</title>
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
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
                        <a class="nav-link active" href="{{ route('campaigns.index') }}">
                            <i class="fas fa-bullhorn me-2"></i>
                            Campaigns
                        </a>
                        <a class="nav-link" href="{{ route('templates.create') }}">
                            <i class="fas fa-plus me-2"></i>
                            New Template
                        </a>
                        <a class="nav-link" href="{{ route('campaigns.create') }}">
                            <i class="fas fa-plus me-2"></i>
                            New Campaign
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-bullhorn me-2"></i>Campaign Details</h2>
                        <div>
                            <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i>Back to Campaigns
                            </a>
                            @if($campaign->status === 'draft')
                                <form action="{{ route('campaigns.send', $campaign) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to send this campaign?')">
                                        <i class="fas fa-paper-plane me-1"></i>Send Campaign
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Campaign Information -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Campaign Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Name:</strong> {{ $campaign->name }}</p>
                                            <p><strong>Template:</strong> {{ $campaign->template->name }}</p>
                                            <p><strong>Status:</strong> 
                                                @switch($campaign->status)
                                                    @case('draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                        @break
                                                    @case('scheduled')
                                                        <span class="badge bg-info">Scheduled</span>
                                                        @break
                                                    @case('sending')
                                                        <span class="badge bg-warning">Sending</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success">Completed</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-danger">Failed</span>
                                                        @break
                                                @endswitch
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Created:</strong> {{ $campaign->created_at->format('M d, Y H:i') }}</p>
                                            @if($campaign->scheduled_at)
                                                <p><strong>Scheduled:</strong> {{ $campaign->scheduled_at->format('M d, Y H:i') }}</p>
                                            @endif
                                            @if($campaign->sent_at)
                                                <p><strong>Sent:</strong> {{ $campaign->sent_at->format('M d, Y H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($campaign->description)
                                        <p><strong>Description:</strong> {{ $campaign->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <h3>Statistics</h3>
                                    <div class="row">
                                        <div class="col-6">
                                            <h4>{{ $stats['total'] }}</h4>
                                            <small>Total Recipients</small>
                                        </div>
                                        <div class="col-6">
                                            <h4>{{ $stats['sent'] }}</h4>
                                            <small>Sent</small>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <h4>{{ $stats['success_rate'] }}%</h4>
                                            <small>Success Rate</small>
                                        </div>
                                        <div class="col-6">
                                            <h4>{{ $stats['failed'] }}</h4>
                                            <small>Failed</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recipients -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Recipients ({{ count($campaign->recipients) }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($campaign->recipients as $recipient)
                                            <tr>
                                                <td>{{ $recipient['email'] }}</td>
                                                <td>{{ $recipient['name'] ?? 'N/A' }}</td>
                                                <td>
                                                    @php
                                                        $log = $campaign->logs->where('to_email', $recipient['email'])->first();
                                                    @endphp
                                                    @if($log)
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
                                                                <span class="badge bg-secondary">Pending</span>
                                                        @endswitch
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Email Logs -->
                    @if($campaign->logs->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Email Logs</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Sent At</th>
                                                <th>Error</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($campaign->logs->take(10) as $log)
                                                <tr>
                                                    <td>{{ $log->to_email }}</td>
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
                                                                <span class="badge bg-secondary">Pending</span>
                                                        @endswitch
                                                    </td>
                                                    <td>{{ $log->sent_at ? $log->sent_at->format('M d, Y H:i') : 'N/A' }}</td>
                                                    <td>{{ $log->error_message ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($campaign->logs->count() > 10)
                                    <p class="text-muted text-center mt-2">Showing first 10 logs. Total: {{ $campaign->logs->count() }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 