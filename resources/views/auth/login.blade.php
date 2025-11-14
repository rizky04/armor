<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Service</title>
    <link rel="shortcut icon" href="{{asset('assets/assets/img/1234.png')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('assets/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/assets/plugins/fontawesome/css/all.min.css')}}">

    <style>
        /* Reset margin & height */
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
        }

        /* Centering the login form */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 15px;
        }

        .login-card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease;
        }

        .login-logo img {
            display: block;
            margin: 0 auto 20px auto;
            max-width: 100px;
        }

        .login-card h3 {
            text-align: center;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .login-card h4 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 400;
            color: #666;
            font-size: 14px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ddd;
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            background: #4e73df;
            color: #fff;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #2e59d9;
        }

        .forgot-password {
            text-align: right;
            font-size: 13px;
            margin-bottom: 15px;
        }

        /* Mobile responsive */
        @media(max-width: 480px){
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <img src="{{asset('assets/assets/img/1234.png')}}" alt="Logo">
            </div>
            <h3>Sign In</h3>
            <h4>Please login to your account</h4>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror" name="password" required
                        autocomplete="current-password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="forgot-password">
                    <a href="forgetpassword.html" class="text-decoration-none">Forgot Password?</a>
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>
    </div>

    <script src="{{asset('assets/assets/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('assets/assets/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>
