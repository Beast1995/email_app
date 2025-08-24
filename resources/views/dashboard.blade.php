<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Bulk Email App') }} - Dashboard</title>
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
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
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
                        <a class="nav-link active" href="{{ route('dashboard') }}">
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
                        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                        <div>
                            <a href="{{ route('campaigns.create') }}" class="btn btn-primary me-2">
                                <i class="fas fa-plus me-1"></i>New Campaign
                            </a>
                            <a href="{{ route('templates.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-1"></i>New Template
                            </a>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Campaigns</h6>
                                            <h3 class="mb-0">{{ \App\Models\EmailCampaign::count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-bullhorn fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Email Templates</h6>
                                            <h3 class="mb-0">{{ \App\Models\EmailTemplate::count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-file-alt fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Emails Sent</h6>
                                            <h3 class="mb-0">{{ \App\Models\EmailLog::where('status', 'sent')->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-paper-plane fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Success Rate</h6>
                                            <h3 class="mb-0">
                                                @php
                                                    $totalSent = \App\Models\EmailLog::where('status', 'sent')->count();
                                                    $totalFailed = \App\Models\EmailLog::where('status', 'failed')->count();
                                                    $total = $totalSent + $totalFailed;
                                                    echo $total > 0 ? round(($totalSent / $total) * 100, 1) : 0;
                                                @endphp%
                                            </h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Campaigns -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bullhorn me-2"></i>Recent Campaigns
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $recentCampaigns = \App\Models\EmailCampaign::with('template')
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
                                    @endphp
                                    
                                    @if($recentCampaigns->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Campaign</th>
                                                        <th>Template</th>
                                                        <th>Status</th>
                                                        <th>Created</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentCampaigns as $campaign)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $campaign->name }}</strong>
                                                                @if($campaign->description)
                                                                    <br><small class="text-muted">{{ Str::limit($campaign->description, 50) }}</small>
                                                                @endif
                                                            </td>
                                                            <td>{{ $campaign->template->name }}</td>
                                                            <td>
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
                                                            </td>
                                                            <td>{{ $campaign->created_at->format('M d, Y') }}</td>
                                                            <td>
                                                                <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No campaigns yet. Create your first campaign to get started!</p>
                                            <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>Create Campaign
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Recent Templates
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $recentTemplates = \App\Models\EmailTemplate::orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
                                    @endphp
                                    
                                    @if($recentTemplates->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($recentTemplates as $template)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $template->name }}</strong>
                                                        <br><small class="text-muted">{{ Str::limit($template->subject, 40) }}</small>
                                                    </div>
                                                    <a href="{{ route('templates.show', $template) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No templates yet. Create your first template!</p>
                                            <a href="{{ route('templates.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>Create Template
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 