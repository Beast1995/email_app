<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe - {{ config('app.name', 'Bulk Email App') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .unsubscribe-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="unsubscribe-card p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-envelope-open-text fa-3x text-primary mb-3"></i>
                        <h2>Unsubscribe Confirmation</h2>
                    </div>
                    
                    <div class="text-center">
                        <p class="lead">
                            You have been successfully unsubscribed from our mailing list.
                        </p>
                        
                        <p class="text-muted">
                            Email: <strong>{{ $email }}</strong>
                        </p>
                        
                        <p>
                            You will no longer receive emails from us. If you change your mind, 
                            you can always resubscribe by contacting us.
                        </p>
                        
                        <div class="mt-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Go to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 