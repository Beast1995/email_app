<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaigns - {{ config('app.name', 'Bulk Email App') }}</title>
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
                        <h2><i class="fas fa-bullhorn me-2"></i>Email Campaigns</h2>
                        <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>New Campaign
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

                    <div class="card">
                        <div class="card-body">
                            @if($campaigns->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Campaign Name</th>
                                                <th>Template</th>
                                                <th>Status</th>
                                                <th>Recipients</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($campaigns as $campaign)
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
                                                    <td>
                                                        @if($campaign->total_recipients > 0)
                                                            {{ $campaign->sent_count }}/{{ $campaign->total_recipients }}
                                                            @if($campaign->success_rate > 0)
                                                                <br><small class="text-success">{{ $campaign->success_rate }}% success</small>
                                                            @endif
                                                        @else
                                                            {{ count($campaign->recipients) }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $campaign->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if($campaign->status === 'draft')
                                                                <form action="{{ route('campaigns.send', $campaign) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Send" onclick="return confirm('Are you sure you want to send this campaign?')">
                                                                        <i class="fas fa-paper-plane"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this campaign?')">
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
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">No campaigns yet</h4>
                                    <p class="text-muted">Create your first email campaign to get started!</p>
                                    <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Create Campaign
                                    </a>
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