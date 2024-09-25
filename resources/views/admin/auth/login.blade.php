<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh; /* Full viewport height */
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #e0f7fa; /* Light blue background color */
        }

        .login-container {
            display: flex;
            width: 700px; /* Total width of the login form */
            height: 600px; /* Total height of the login form */
            border-radius: 8px;
            overflow: hidden; /* To round corners */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            background-color: white; /* Background color for the form container */
        }

        .image-section {
            flex: 1; /* Takes half the width */
            background-image: url('/admin/img/road.jpg');
            background-size: cover;
            background-position: center;
        }

        .form-section {
            flex: 1; /* Takes half the width */
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white; /* Background color for form */
        }

        .login-form {
            width: 300px; /* Width of the form */
            padding: 20px;
        }

        .login-form h2 {
            text-align: center;
            color: #007bff; /* Blue color for the header */
            margin-bottom: 20px;
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #007bff; /* Blue border for inputs */
            border-radius: 4px;
            box-sizing: border-box;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: #007bff; /* Blue button color */
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-form button .fab {
            margin-right: 10px;
        }

        .login-form button:hover {
            background-color: #0056b3; /* Darker blue hover color */
        }

        .login-form a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .login-form a:hover {
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
        }

        .divider hr {
            flex: 1;
            border: none;
            border-top: 1px solid #007bff;
        }

        .divider span {
            padding: 0 10px;
            color: #007bff;
        }

        .login-form p {
            text-align: center;
            color: #007bff; /* Blue text color */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="image-section"></div>
        <div class="form-section">
            <form class="login-form" action="{{ route('admin.login') }}" method="POST">
                @csrf
                <h2>Login</h2>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Sign in</button>
                <a href="#">Forgot password?</a>
                <div class="divider">
                    <hr>
                    <span>or</span>
                    <hr>
                </div>
                <button type="button">
                    <i class="fab fa-google"></i>
                    Sign in with Google
                </button>
                <button type="button">
                    <i class="fab fa-facebook-f"></i>
                    Sign in with Facebook
                </button>
                <p>Don't have an account? <a href="#">Sign up</a></p>
            </form>
        </div>
    </div>
</body>
</html>
