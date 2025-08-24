<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Template - {{ config('app.name', 'Bulk Email App') }}</title>
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
        .variable-tag {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px 6px;
            margin: 2px;
            font-size: 12px;
            color: #495057;
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
                        <a class="nav-link active" href="{{ route('templates.index') }}">
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
                        <h2><i class="fas fa-plus me-2"></i>Create New Template</h2>
                        <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Templates
                        </a>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('templates.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Template Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="is_active" class="form-label">Status</label>
                                            <select class="form-select" id="is_active" name="is_active">
                                                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label">Email Subject *</label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required>
                                    <small class="text-muted">You can use variables like {{name}}, {{company_name}}, etc.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Email Content *</label>
                                    <textarea class="form-control" id="content" name="content" rows="15" required>{{ old('content') }}</textarea>
                                    <small class="text-muted">You can use HTML and variables like {{name}}, {{company_name}}, etc.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Available Variables</label>
                                    <div class="p-3 bg-light rounded">
                                        <p class="mb-2"><strong>Common Variables:</strong></p>
                                        <span class="variable-tag">{{name}}</span>
                                        <span class="variable-tag">{{email}}</span>
                                        <span class="variable-tag">{{company_name}}</span>
                                        <span class="variable-tag">{{support_email}}</span>
                                        <span class="variable-tag">{{month_year}}</span>
                                        <span class="variable-tag">{{news_content}}</span>
                                        <span class="variable-tag">{{events_content}}</span>
                                        <span class="variable-tag">{{discount_percentage}}</span>
                                        <span class="variable-tag">{{promo_code}}</span>
                                        <span class="variable-tag">{{expiry_date}}</span>
                                        <span class="variable-tag">{{product_name}}</span>
                                        <span class="variable-tag">{{feature_1}}</span>
                                        <span class="variable-tag">{{feature_2}}</span>
                                        <span class="variable-tag">{{feature_3}}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('templates.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Create Template
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Template Examples -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Template Examples</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Welcome Email</h6>
                                    <pre class="bg-light p-2 rounded"><code>Subject: Welcome to {{company_name}}, {{name}}!

Content:
&lt;h2&gt;Welcome to {{company_name}}!&lt;/h2&gt;
&lt;p&gt;Dear {{name}},&lt;/p&gt;
&lt;p&gt;Thank you for joining {{company_name}}.&lt;/p&gt;</code></pre>
                                </div>
                                <div class="col-md-6">
                                    <h6>Newsletter</h6>
                                    <pre class="bg-light p-2 rounded"><code>Subject: {{company_name}} Newsletter - {{month_year}}

Content:
&lt;h2&gt;{{company_name}} Newsletter&lt;/h2&gt;
&lt;p&gt;Hello {{name}},&lt;/p&gt;
&lt;p&gt;{{news_content}}&lt;/p&gt;</code></pre>
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