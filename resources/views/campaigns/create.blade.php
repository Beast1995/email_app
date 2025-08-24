<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Campaign - {{ config('app.name', 'Bulk Email App') }}</title>
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
        .recipient-row {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
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
                        <h2><i class="fas fa-plus me-2"></i>Create New Campaign</h2>
                        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Campaigns
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
                            <form action="{{ route('campaigns.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Campaign Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="template_id" class="form-label">Email Template *</label>
                                            <select class="form-select" id="template_id" name="template_id" required>
                                                <option value="">Select a template</option>
                                                @foreach($templates as $template)
                                                    <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                                        {{ $template->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="scheduled_at" class="form-label">Schedule Campaign (Optional)</label>
                                    <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                                    <small class="text-muted">Leave empty to send immediately</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Recipients *</label>
                                    <div id="recipients-container">
                                        <div class="recipient-row">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="email" class="form-control" name="recipients[0][email]" placeholder="Email address" required>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="recipients[0][name]" placeholder="Name (optional)">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-recipient" style="display: none;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-recipient">
                                        <i class="fas fa-plus me-1"></i>Add Another Recipient
                                    </button>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('campaigns.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Create Campaign
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let recipientCount = 1;

        document.getElementById('add-recipient').addEventListener('click', function() {
            const container = document.getElementById('recipients-container');
            const newRow = document.createElement('div');
            newRow.className = 'recipient-row';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-5">
                        <input type="email" class="form-control" name="recipients[${recipientCount}][email]" placeholder="Email address" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="recipients[${recipientCount}][name]" placeholder="Name (optional)">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-recipient">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            recipientCount++;

            // Show remove buttons for all rows except the first
            document.querySelectorAll('.remove-recipient').forEach(btn => {
                btn.style.display = 'block';
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-recipient')) {
                e.target.closest('.recipient-row').remove();
                
                // Hide remove button if only one recipient remains
                if (document.querySelectorAll('.recipient-row').length === 1) {
                    document.querySelector('.remove-recipient').style.display = 'none';
                }
            }
        });
    </script>
</body>
</html> 