<?php 
require_once 'header.php'; 

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // 1. Basic Validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (!in_array($role, ['applicant', 'provider'])) {
        $error = "Invalid role selected.";
    } else {
        // 2. Check if Email Exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            $error = "Email already registered. Please login.";
        } else {
            // 3. Register User
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$name, $email, $hashed_password, $role])) {
                // Javascript Redirect (Cleaner than header() after output)
                echo "<script>window.location.href='login.php?registered=1';</script>";
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 p-5">
                    <h1 class="h3 mb-3 text-center fw-bold">Create an Account</h1>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger p-2 text-center"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="" method="post">
                        <div class="mb-3">
                            <label class="mb-2 fw-bold">Full Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                        </div> 
                        <div class="mb-3">
                            <label class="mb-2 fw-bold">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                        </div> 
                        
                        <div class="mb-3">
                            <label class="mb-2 fw-bold">I want to *</label>
                            <select name="role" class="form-select">
                                <option value="applicant">Find a Job (Applicant)</option>
                                <option value="provider">Hire Someone (Job Provider)</option>
                            </select>
                        </div>

                        <!-- PASSWORD 1 -->
                        <div class="mb-3">
                            <label class="mb-2 fw-bold">Password *</label>
                            <div class="input-group">
                                <input type="password" name="password" id="pass1" class="form-control" placeholder="******" required>
                                <span class="input-group-text" onclick="togglePass('pass1', 'icon1')" style="cursor: pointer;">
                                    <i class="fa fa-eye" id="icon1"></i>
                                </span>
                            </div>
                        </div> 
                        
                        <!-- PASSWORD 2 -->
                        <div class="mb-3">
                            <label class="mb-2 fw-bold">Confirm Password *</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" id="pass2" class="form-control" placeholder="******" required>
                                <span class="input-group-text" onclick="togglePass('pass2', 'icon2')" style="cursor: pointer;">
                                    <i class="fa fa-eye" id="icon2"></i>
                                </span>
                            </div>
                        </div> 
                        
                        <button class="btn btn-primary w-100 mt-3 btn-lg shadow-sm">Register Now</button>
                    </form>                    
                </div>
                <div class="mt-4 text-center">
                    <p>Already have an account? <a href="login.php" class="text-primary fw-bold">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Improved Password Toggle Script -->
<script>
    function togglePass(fieldId, iconId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

<?php require_once 'footer.php'; ?>