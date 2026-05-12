<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login & Register</title>
<link rel="stylesheet" href="style.css">
<script defer src="script.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Top copyright */
    .top-bar {
        position: absolute;
        top: 10px;
        width: 100%;
        text-align: center;
        font-size: 14px;
        color: #0e0e0eff;
        font-weight: 500;
        z-index: 1000;
    }

    /* Social media inside overlay */
    .social-bar {
        margin-top: 15px;
        text-align: center;
    }
    .social-bar a {
        color: #111111ff;
        font-size: 20px;
        margin: 0 8px;
        transition: color 0.3s;
    }
    .social-bar a:hover {
        color: #7e8709ff; /* hover effect */
    }
</style>
</head>
<body>
    
  <!-- Top Copyright -->
  <div class="top-bar">
    © 2025 Orchid Noir Studio. All rights reserved
  </div>

  <div class="main-container">
    <!-- Dark/Light Mode Toggle -->
    <div class="mode-toggle">
      <label class="switch">
        <input type="checkbox" id="mode-switch">
        <span class="slider">
          <i class="fas fa-sun"></i>
          <i class="fas fa-moon"></i>
        </span>
      </label>
    </div>

    <div class="spotlight"></div>

    <div class="main-container">
      <div class="container" id="container">
        
        <!-- Sign Up -->
        <div class="form-container sign-up-container">
            <form id="register-form" action="process_register.php" method="POST">
                <h1>Create Account</h1>
                <div class="input-box">
                    <i class="fa fa-user"></i>
                    <input type="text" name="first_name" placeholder="First Name" required>
                </div>
                <div class="input-box">
                    <i class="fa fa-user"></i>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                </div>
                <div class="input-box">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-box">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-box">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <button class="btn">Register</button>
            </form>
        </div>

        <!-- Sign In -->
        <div class="form-container sign-in-container">
            <form id="login-form" action="process_login.php" method="POST">
                <h1>Sign in</h1>
                <div class="input-box">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-box">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button class="btn">Login</button>
            </form>
        </div>

        <!-- Overlay -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>HELLO, FRIEND!</h1>
                    <p class="toggle-msg">Already have an account? <span class="toggle-btn" id="signIn">Login</span></p>
                    
                    <!-- Social Icons here -->
                    <div class="social-bar">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>WELCOME BACK!!!</h1>
                    <p class="toggle-msg">Don't have an account yet? <span class="toggle-btn" id="signUp">Register</span></p>
                    
                    <!-- Social Icons here -->
                    <div class="social-bar">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>

</body>
</html>





