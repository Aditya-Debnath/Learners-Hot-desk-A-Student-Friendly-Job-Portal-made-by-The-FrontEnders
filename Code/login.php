<?php 
require_once 'header.php'; 

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$msg = '';

// Check for success message from Registration
if (isset($_GET['registered'])) {
    $msg = "Account created successfully! Please login.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // 1. Check User in DB
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. Verify Password
    if ($user && password_verify($password, $user['password'])) {
        
        // 3. Set Session Data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['profile_pic'] = $user['profile_pic']; // used in header.php avatar
        $_SESSION['is_verified'] = $user['is_verified']; 
        
        // 4. Redirect Based on Role
        if ($user['role'] == 'admin') {
            header("Location: dashboard.php?view=admin_jobs");
        } elseif ($user['role'] == 'provider') {
            header("Location: dashboard.php?view=my_jobs");
        } else {
            // Applicant typically goes to Home to search, or dashboard
            header("Location: index.php"); 
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 p-5">
                    <h1 class="h3 mb-3">Login</h1>
                    
                    <?php if($msg): ?>
                        <div class="alert alert-success"><?php echo $msg; ?></div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="" method="post">
                        <div class="mb-3">
                            <label class="mb-2">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                        </div> 
                        <div class="mb-3">
                            <label class="mb-2">Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="******" required>
                        </div> 
                        
                        <button class="btn btn-primary w-100 mt-2">Login</button>
                        
                        <!-- Simple 'Forgot Password' link placeholder -->
                        <div class="text-end mt-2">
                            <a href="#" class="small text-muted">Forgot Password?</a>
                        </div>
                    </form>                    
                </div>
                <div class="mt-4 text-center">
                    <p>Don't have an account? <a href="register.php" class="text-primary fw-bold">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>