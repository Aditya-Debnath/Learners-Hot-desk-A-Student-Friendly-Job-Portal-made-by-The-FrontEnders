<?php 
// 1. INCLUDE HEADER & CHECK LOGIN
include 'header.php'; 
checkLogin(); 

// 2. FETCH CURRENT STATUS
$stmt = $pdo->prepare("SELECT mobile, is_verified FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// 3. HANDLE POST ACTION (OTP Logic)
// Check if ANY POST request is sent
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    // Simulate Verify
    $upd = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
    $upd->execute([$_SESSION['user_id']]);
    $_SESSION['is_verified'] = 1; // Update active session immediately

    // Javascript Alert & Redirect (Reliable)
    echo "<script>
            alert('Mobile Verified Successfully!'); 
            window.location.href='dashboard.php?view=profile';
          </script>";
    exit;
}
?>

<div class="container py-5 text-center">
    <div class="card p-5 mx-auto shadow border-0" style="max-width:500px;">
        <h3 class="mb-3 text-primary">Verify Mobile Number</h3>
        
        <?php if($user['is_verified']): ?>
            <!-- IF ALREADY VERIFIED -->
            <div class="alert alert-success">
                <i class="fa fa-check-circle fa-3x mb-3"></i>
                <h5 class="alert-heading">You are already verified!</h5>
            </div>
            <a href="dashboard.php" class="btn btn-outline-primary w-100">Back to Dashboard</a>
            
        <?php else: ?>
            <!-- IF NOT VERIFIED -->
            <p class="text-muted mb-4">Verification is required to Apply for Jobs or Post new listings.</p>
            
            <form method="post">
                <div class="mb-4">
                    <label class="form-label text-start d-block fw-bold">Mobile Number on Record</label>
                    <input type="text" class="form-control form-control-lg bg-light" 
                           value="<?php echo htmlspecialchars($user['mobile'] ? $user['mobile'] : 'No number added'); ?>" 
                           readonly>
                    <?php if(!$user['mobile']): ?>
                        <small class="text-danger d-block mt-1">Please add a mobile number in <a href="dashboard.php?view=profile">Edit Profile</a> first.</small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fs-5" 
                        <?php echo (!$user['mobile']) ? 'disabled' : ''; ?>>
                    <i class="fa fa-paper-plane me-2"></i> Send OTP (Simulate)
                </button>
            </form>
        <?php endif; ?>
        
    </div>
</div>

<?php include 'footer.php'; ?>