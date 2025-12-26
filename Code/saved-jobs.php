<?php
require_once 'header.php';
checkLogin();

// Strict Access Control
if($_SESSION['role'] !== 'applicant') {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit;
}

// Handle Unsave/Remove
if (isset($_GET['remove'])) {
    $job_id = $_GET['remove'];
    $uid = $_SESSION['user_id'];
    $pdo->prepare("DELETE FROM saved_jobs WHERE job_id = ? AND user_id = ?")->execute([$job_id, $uid]);
    echo "<script>window.location.href='saved-jobs.php';</script>";
    exit;
}

// Handle Add/Save Logic
if (isset($_GET['add'])) {
    $job_id = $_GET['add'];
    $uid = $_SESSION['user_id'];
    // Check for duplicates
    $chk = $pdo->prepare("SELECT id FROM saved_jobs WHERE job_id = ? AND user_id = ?");
    $chk->execute([$job_id, $uid]);
    if ($chk->rowCount() == 0) {
        $pdo->prepare("INSERT INTO saved_jobs (job_id, user_id) VALUES (?, ?)")->execute([$job_id, $uid]);
        echo "<script>alert('Job Saved Successfully!');</script>";
    }
    // Remove query params to prevent re-submission
    echo "<script>window.location.href='saved-jobs.php';</script>";
    exit;
}

// Fetch Saved Jobs
$uid = $_SESSION['user_id'];
$sql = "SELECT jobs.*, saved_jobs.saved_at 
        FROM saved_jobs 
        JOIN jobs ON saved_jobs.job_id = jobs.id 
        WHERE saved_jobs.user_id = ? 
        ORDER BY saved_jobs.saved_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$uid]);
$saved = $stmt->fetchAll();

// Session Data for Sidebar
$user_name = $_SESSION['name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'applicant';
$user_pic  = $_SESSION['profile_pic'] ?? 'avatar7.png';
?>

<div class="container py-5">
    <div class="row">
        
        <!-- SIDEBAR (Modern & Consistent) -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px;">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="assets/images/<?php echo $user_pic; ?>" class="rounded-circle border" width="80" height="80" style="object-fit: cover;">
                        <h6 class="mt-2 fw-bold"><?php echo htmlspecialchars($user_name); ?></h6>
                        <span class="badge bg-light text-dark border"><?php echo ucfirst($user_role); ?></span>
                    </div>
                    
                    <div class="nav flex-column nav-pills">
                        <a class="nav-link" href="dashboard.php?view=overview"><i class="fa fa-home me-2"></i> Overview</a>
                        <a class="nav-link" href="dashboard.php?view=profile"><i class="fa fa-user me-2"></i> Edit Profile</a>
                        <a class="nav-link" href="dashboard.php?view=chats"><i class="fa fa-comments me-2"></i> Messages</a>
                        <a class="nav-link <?php echo $view=='notifications'?'active':''; ?>" href="dashboard.php?view=notifications"><i class="fa fa-bell me-2"></i> Notifications</a>
                        <a class="nav-link" href="job-applied.php"><i class="fa fa-file-text me-2"></i> My Applications</a>
                        <!-- Active Class Applied Here -->
                        <a class="nav-link active bg-danger text-white" href="saved-jobs.php"><i class="fa fa-heart me-2"></i> Saved Jobs</a>
                        
                        <a class="nav-link text-danger" href="logout.php"><i class="fa fa-sign-out me-2"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow border-0 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Saved Jobs</h4>
                    <a href="jobs.php" class="btn btn-outline-primary btn-sm"><i class="fa fa-search"></i> Browse More</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Job Title / Company</th>
                                <th>Saved On</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($saved) > 0): ?>
                                <?php foreach($saved as $j): ?>
                                <tr>
                                    <td>
                                        <a href="job-detail.php?id=<?php echo $j['id']; ?>" class="fw-bold text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($j['title']); ?>
                                        </a>
                                        <div class="text-muted small">
                                            <i class="fa fa-building"></i> <?php echo htmlspecialchars($j['company_name']); ?>
                                            &bull;
                                            <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($j['location']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M, Y', strtotime($j['saved_at'])); ?></td>
                                    <td>
                                        <?php if($j['is_approved']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="job-detail.php?id=<?php echo $j['id']; ?>" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                        <a href="saved-jobs.php?remove=<?php echo $j['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this job from saved list?');"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fa fa-heart-o fa-3x mb-3 text-secondary"></i><br>
                                        No saved jobs yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>