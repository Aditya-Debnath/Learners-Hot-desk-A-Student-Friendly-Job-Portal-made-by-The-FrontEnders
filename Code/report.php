<?php
require_once 'header.php';
checkLogin();

// Getting Variables
$type = $_GET['target'] ?? ''; // 'job' or 'user'
$id = $_GET['id'] ?? 0;
$reporter_id = $_SESSION['user_id'];

// --- VALIDATION: Get Info about what we are reporting ---
$targetName = "Unknown";
if($type == 'job') {
    $stmt = $pdo->prepare("SELECT title FROM jobs WHERE id=?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    if($data) $targetName = "Job: " . htmlspecialchars($data['title']);
} elseif($type == 'user') {
    $stmt = $pdo->prepare("SELECT name, role FROM users WHERE id=?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    if($data) $targetName = ucfirst($data['role']) . ": " . htmlspecialchars($data['name']);
} else {
    echo "<script>window.location.href='dashboard.php';</script>"; exit;
}

// --- SUBMIT REPORT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason']);
    if(!empty($reason)) {
        // Construct detailed reason
        $full_report = "Target Type: $type | ID: $id | Reason: $reason";
        
        $sql = "INSERT INTO reports (reporter_id, reported_id, job_id, reason) VALUES (?, ?, ?, ?)";
        
        // Logic: Insert ID into correct column (reported_id OR job_id)
        $uID = ($type == 'user') ? $id : null;
        $jID = ($type == 'job')  ? $id : null;

        $pdo->prepare($sql)->execute([$reporter_id, $uID, $jID, $reason]);
        
        echo "<script>alert('Report Submitted. Admin will review.'); window.location.href='dashboard.php';</script>";
        exit;
    }
}
?>

<div class="container py-5">
    <div class="card shadow-sm mx-auto border-0" style="max-width: 600px;">
        <div class="card-header bg-danger text-white text-center">
            <h4 class="mb-0"><i class="fa fa-exclamation-triangle"></i> Submit a Report</h4>
        </div>
        <div class="card-body p-4">
            <p class="lead text-center mb-4">You are reporting <strong><?php echo $targetName; ?></strong></p>
            
            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold">Please describe the issue:</label>
                    <textarea name="reason" class="form-control" rows="5" placeholder="Scam, Spam, Fake Information, Harassment..." required></textarea>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-danger btn-lg">Submit Report</button>
                    <a href="dashboard.php" class="btn btn-light border">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>