<?php
session_start();

// Jika sudah login
if (isset($_SESSION['username'])) {
    header("Location: halaman_utama.php");
    exit;
}

$error = "";

// Username dan password yang ditentukan
$valid_username = "admin";
$valid_password = "password123"; // Ganti dengan password yang Anda inginkan

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi login sederhana
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        header("Location: halaman_utama.php");
        exit;
    } else {
        $error = "Nama pengguna atau sandi salah!";
    }
}

// Cegat akses langsung ke file ini
if (basename($_SERVER['PHP_SELF']) == 'login.php' && empty($_POST)) {
    // Biarkan akses normal
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CatatanKu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">

<div class="login-container">
    <!-- Background Animation -->
    <div class="background-animation">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <!-- Logo & Welcome Section -->
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-tasks"></i>
                <span>CatatanKu</span>
            </div>
            <h1 class="welcome-title">Selamat Datang</h1>
            <p class="welcome-subtitle">Silahkan login untuk mengakses catatan kegiatan Anda</p>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo $error; ?></span>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" class="login-form">
            <div class="input-group">
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                </div>
                <input type="text" name="username" placeholder="Nama pengguna" required class="input-field" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <div class="input-group">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <input type="password" name="password" placeholder="Sandi" required class="input-field" id="passwordField">
                <button type="button" class="password-toggle" id="passwordToggle">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <button type="submit" name="login" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                <span>Masuk ke Akun</span>
            </button>
        </form>

        <!-- Additional Info -->
        <div class="login-footer">
            <div class="feature-list">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Kelola kegiatan harian</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Pantau progress Anda</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Akses di mana saja</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Credentials -->
    <div class="demo-credentials">
        <div class="demo-header">
            <i class="fas fa-info-circle"></i>
            <span>Informasi Login</span>
        </div>
        <div class="demo-content">
            <p><strong>Nama pengguna:</strong> admin</p>
            <p><strong>kata Sandi:</strong> password123</p>
            <p class="demo-note">Untuk keamanan, ganti sandi di kode PHP</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordField = document.getElementById('passwordField');
    
    if (passwordToggle && passwordField) {
        passwordToggle.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle eye icon
            const icon = this.querySelector('i');
            if (type === 'password') {
                icon.className = 'fas fa-eye';
            } else {
                icon.className = 'fas fa-eye-slash';
            }
        });
    }
    
    // Form validation and animation
    const loginForm = document.querySelector('.login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('.input-field');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.parentElement.classList.add('error');
                    isValid = false;
                } else {
                    input.parentElement.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                
                // Add shake animation to empty fields
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.parentElement.classList.add('shake');
                        setTimeout(() => {
                            input.parentElement.classList.remove('shake');
                        }, 500);
                    }
                });
            }
        });
    }
    
    // Input focus effects
    const inputFields = document.querySelectorAll('.input-field');
    inputFields.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Auto-fill detection
        setTimeout(() => {
            if (this.value) {
                this.parentElement.classList.add('focused');
            }
        }, 100);
    });
    
    // Enter key to submit
    document.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const focused = document.activeElement;
            if (focused && focused.classList.contains('input-field')) {
                loginForm.requestSubmit();
            }
        }
    });
});
</script>

<style>
/* ====== VARIABLES ====== */
:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --dark: #1e293b;
    --darker: #0f172a;
    --light: #f8fafc;
    --gray: #64748b;
    --gray-light: #cbd5e1;
    --card-bg: rgba(255, 255, 255, 0.08);
    --card-border: rgba(255, 255, 255, 0.12);
    --shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    --transition: all 0.3s ease;
}

/* ====== RESET & BASE ====== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    color: var(--light);
    line-height: 1.6;
}

.login-body {
    background: linear-gradient(135deg, var(--darker), var(--dark));
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow: hidden;
}

/* ====== BACKGROUND ANIMATION ====== */
.background-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    overflow: hidden;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: linear-gradient(45deg, var(--primary), transparent);
    opacity: 0.1;
    animation: float 15s infinite linear;
}

.shape-1 {
    width: 300px;
    height: 300px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.shape-2 {
    width: 200px;
    height: 200px;
    top: 60%;
    right: 10%;
    animation-delay: 3s;
    background: linear-gradient(45deg, var(--secondary), transparent);
}

.shape-3 {
    width: 150px;
    height: 150px;
    bottom: 20%;
    left: 20%;
    animation-delay: 6s;
    background: linear-gradient(45deg, var(--warning), transparent);
}

.shape-4 {
    width: 250px;
    height: 250px;
    top: 30%;
    right: 20%;
    animation-delay: 9s;
    background: linear-gradient(45deg, var(--danger), transparent);
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    25% {
        transform: translateY(-20px) rotate(90deg);
    }
    50% {
        transform: translateY(0) rotate(180deg);
    }
    75% {
        transform: translateY(20px) rotate(270deg);
    }
}

/* ====== LOGIN CONTAINER ====== */
.login-container {
    width: 100%;
    max-width: 440px;
    position: relative;
    z-index: 1;
}

/* ====== LOGIN CARD ====== */
.login-card {
    background: var(--card-bg);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 40px 35px;
    border: 1px solid var(--card-border);
    box-shadow: var(--shadow);
    animation: slideUp 0.6s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ====== LOGIN HEADER ====== */
.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--primary);
}

.logo i {
    font-size: 2rem;
}

.welcome-title {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 8px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.welcome-subtitle {
    color: var(--gray-light);
    font-size: 0.95rem;
}

/* ====== ERROR MESSAGE ====== */
.error-message {
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: shake 0.5s ease;
}

.error-message i {
    color: var(--danger);
    font-size: 1.1rem;
}

.error-message span {
    color: #fca5a5;
    font-size: 0.9rem;
    font-weight: 500;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* ====== LOGIN FORM ====== */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.input-group {
    position: relative;
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    transition: var(--transition);
    overflow: hidden;
}

.input-group.focused {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

.input-group.error {
    border-color: var(--danger);
    animation: shake 0.5s ease;
}

.input-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray);
    transition: var(--transition);
}

.input-group.focused .input-icon {
    color: var(--primary);
}

.input-group.error .input-icon {
    color: var(--danger);
}

.input-field {
    flex: 1;
    background: transparent;
    border: none;
    padding: 15px 15px 15px 0;
    color: var(--light);
    font-size: 1rem;
    outline: none;
}

.input-field::placeholder {
    color: var(--gray);
    transition: var(--transition);
}

.input-group.focused .input-field::placeholder {
    color: var(--gray-light);
}

.password-toggle {
    background: none;
    border: none;
    color: var(--gray);
    padding: 15px;
    cursor: pointer;
    transition: var(--transition);
}

.password-toggle:hover {
    color: var(--primary);
}

/* ====== LOGIN BUTTON ====== */
.login-btn {
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 16px 24px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}

.login-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
}

.login-btn:active {
    transform: translateY(0);
}

/* ====== LOGIN FOOTER ====== */
.login-footer {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid var(--card-border);
}

.feature-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--gray-light);
    font-size: 0.9rem;
}

.feature-item i {
    color: var(--secondary);
    font-size: 0.8rem;
}

/* ====== DEMO CREDENTIALS ====== */
.demo-credentials {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 20px;
    margin-top: 25px;
    backdrop-filter: blur(10px);
    animation: slideUp 0.8s ease 0.2s both;
}

.demo-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    color: var(--warning);
    font-weight: 600;
}

.demo-header i {
    font-size: 1.1rem;
}

.demo-content {
    color: var(--gray-light);
    font-size: 0.9rem;
}

.demo-content p {
    margin-bottom: 5px;
}

.demo-content strong {
    color: var(--light);
}

.demo-note {
    font-size: 0.8rem;
    color: var(--gray);
    margin-top: 8px;
    font-style: italic;
}

/* ====== RESPONSIVE DESIGN ====== */
@media (max-width: 480px) {
    .login-container {
        max-width: 100%;
    }
    
    .login-card {
        padding: 30px 25px;
        border-radius: 20px;
    }
    
    .logo {
        font-size: 1.5rem;
    }
    
    .logo i {
        font-size: 1.7rem;
    }
    
    .welcome-title {
        font-size: 1.5rem;
    }
    
    .input-group {
        flex-direction: row;
    }
    
    .input-icon {
        width: 45px;
        height: 45px;
    }
    
    .input-field {
        padding: 12px 12px 12px 0;
    }
    
    .password-toggle {
        padding: 12px;
    }
    
    .login-btn {
        padding: 14px 20px;
    }
    
    .shape {
        display: none;
    }
    
    .shape-1, .shape-2 {
        display: block;
        width: 150px;
        height: 150px;
    }
}

/* ====== LOADING STATE ====== */
.login-btn.loading {
    position: relative;
    color: transparent;
    pointer-events: none;
}

.login-btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid transparent;
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ====== ACCESSIBILITY ====== */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .background-animation {
        display: none;
    }
}
</style>
</body>
</html>