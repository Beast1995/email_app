<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Campaigns - {{ config('app.name', 'Bulk Email App') }}</title>
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
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
        }
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border: none;
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
                        <h2><i class="fas fa-paper-plane me-2"></i>Send Campaigns</h2>
                        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Campaigns
                        </a>
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

                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif

                    <!-- Bulk Actions -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Bulk Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <form action="{{ route('campaigns.send-all-draft') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Send all draft campaigns?')">
                                                <i class="fas fa-paper-plane me-2"></i>Send All Draft Campaigns
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('campaigns.send-scheduled') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Send all scheduled campaigns?')">
                                                <i class="fas fa-clock me-2"></i>Send Scheduled Campaigns
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Quick Stats</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $draftCampaigns = \App\Models\EmailCampaign::where('status', 'draft')->count();
                                        $scheduledCampaigns = \App\Models\EmailCampaign::where('status', 'scheduled')->count();
                                        $totalCampaigns = \App\Models\EmailCampaign::count();
                                    @endphp
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <h4 class="text-primary">{{ $draftCampaigns }}</h4>
                                            <small>Draft</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-warning">{{ $scheduledCampaigns }}</h4>
                                            <small>Scheduled</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-success">{{ $totalCampaigns }}</h4>
                                            <small>Total</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Draft Campaigns -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Draft Campaigns</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $draftCampaigns = \App\Models\EmailCampaign::with('template')
                                    ->where('status', 'draft')
                                    ->get();
                            @endphp
                            
                            @if($draftCampaigns->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Campaign Name</th>
                                                <th>Template</th>
                                                <th>Recipients</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($draftCampaigns as $campaign)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $campaign->name }}</strong>
                                                        @if($campaign->description)
                                                            <br><small class="text-muted">{{ Str::limit($campaign->description, 50) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $campaign->template->name }}</td>
                                                    <td>{{ count($campaign->recipients) }}</td>
                                                    <td>{{ $campaign->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <form action="{{ route('campaigns.send-campaign', $campaign) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Send this campaign?')">
                                                                    <i class="fas fa-paper-plane"></i> Send
                                                                </button>
                                                            </form>
                                                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-edit fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No draft campaigns found.</p>
                                    <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Create Campaign
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Scheduled Campaigns -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Scheduled Campaigns</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $scheduledCampaigns = \App\Models\EmailCampaign::with('template')
                                    ->where('status', 'scheduled')
                                    ->get();
                            @endphp
                            
                            @if($scheduledCampaigns->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Campaign Name</th>
                                                <th>Template</th>
                                                <th>Scheduled For</th>
                                                <th>Recipients</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($scheduledCampaigns as $campaign)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $campaign->name }}</strong>
                                                        @if($campaign->description)
                                                            <br><small class="text-muted">{{ Str::limit($campaign->description, 50) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $campaign->template->name }}</td>
                                                    <td>
                                                        {{ $campaign->scheduled_at->format('M d, Y H:i') }}
                                                        @if($campaign->scheduled_at->isPast())
                                                            <br><span class="badge bg-success">Ready to send</span>
                                                        @else
                                                            <br><span class="badge bg-warning">Future</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ count($campaign->recipients) }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            @if($campaign->scheduled_at->isPast())
                                                                <form action="{{ route('campaigns.send-campaign', $campaign) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Send this scheduled campaign?')">
                                                                        <i class="fas fa-paper-plane"></i> Send Now
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No scheduled campaigns found.</p>
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