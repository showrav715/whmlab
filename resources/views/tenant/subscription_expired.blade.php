<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expired</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .subscription-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 2rem;
        }
        
        .btn-renew {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        
        .btn-renew:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .features-list {
            text-align: left;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .feature-item i {
            color: #28a745;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="subscription-card">
        <div class="icon-circle">
            <i class="las la-exclamation-triangle"></i>
        </div>
        
        <h2 class="mb-3">Subscription Expired</h2>
        <p class="text-muted mb-4">
            Your subscription for <strong>{{ $tenant->getSetting('name', 'this website') }}</strong> has expired. 
            Please renew your subscription to continue using our services.
        </p>
        
        <div class="features-list">
            <h6 class="mb-3"><i class="las la-lock text-warning"></i> Limited Access</h6>
            <div class="feature-item">
                <i class="las la-times text-danger"></i>
                <span>Website functionality disabled</span>
            </div>
            <div class="feature-item">
                <i class="las la-times text-danger"></i>
                <span>Admin panel access restricted</span>
            </div>
            <div class="feature-item">
                <i class="las la-times text-danger"></i>
                <span>Email services suspended</span>
            </div>
            <div class="feature-item">
                <i class="las la-check text-success"></i>
                <span>Data preserved for 30 days</span>
            </div>
        </div>
        
        <div class="d-grid gap-2">
            <a href="mailto:support@{{ config('app.domain', 'example.com') }}" class="btn btn-renew">
                <i class="las la-envelope me-2"></i>
                Contact Support to Renew
            </a>
            <small class="text-muted mt-2">
                Or contact your administrator to renew the subscription
            </small>
        </div>
        
        <hr class="my-4">
        
        <div class="text-center">
            <small class="text-muted">
                <i class="las la-info-circle"></i>
                If you believe this is an error, please contact support immediately
            </small>
        </div>
    </div>
</body>
</html>