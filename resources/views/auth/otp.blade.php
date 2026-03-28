<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - CoreInventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #f8f9fc; }</style>
</head>
<body class="d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5 text-center">
                        <h5 class="mb-3 fw-bold">Two-Factor Authentication</h5>
                        <p class="text-muted mb-4 small">Please enter the 6-digit OTP sent to your registered email.</p>
                        <form method="POST" action="{{ route('otp.verify') }}">
                            @csrf
                            <div class="mb-4">
                                <input type="text" name="otp" class="form-control form-control-lg text-center fw-bold tracking-wide" maxlength="6" placeholder="• • • • • •" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">Verify OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
