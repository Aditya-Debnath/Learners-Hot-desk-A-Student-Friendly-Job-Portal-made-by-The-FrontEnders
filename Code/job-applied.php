<?php
require_once 'header.php';
checkLogin();

// Strict Access Control
if($_SESSION['role'] !== 'applicant') {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit;
}

$uid = $_SESSION['user_id'];

// Fetch Applications
// Joining jobs table to get details + Joining users to get Provider ID (for chat)
$sql = "SELECT jobs.id as job_id, jobs.title, jobs.company_name, jobs.location, 
               applications.applied_at, applications.status,
               users.id as provider_id
        FROM applications 
        JOIN jobs ON applications.job_id = jobs.id
        JOIN users ON jobs.provider_id = users.id 
        WHERE applications.applicant_id = ? 
        ORDER BY applications.applied_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$uid]);
$apps = $stmt->fetchAll();

// Session Data for Sidebar
$user_name = $_SESSION['name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'applicant';
$user_pic  = $_SESSION['profile_pic'] ?? 'avatar7.png';
?>

<div class="container py-5">
    <div class="row">
        
        <!-- SIDEBAR -->
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
                        <!-- Active Class Applied Here -->
                        <a class="nav-link active" href="job-applied.php"><i class="fa fa-file-text me-2"></i> My Applications</a>
                        <a class="nav-link" href="saved-jobs.php"><i class="fa fa-heart me-2"></i> Saved Jobs</a>
                        
                        <a class="nav-link text-danger" href="logout.php"><i class="fa fa-sign-out me-2"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow border-0 p-4">
                <h4 class="mb-4">Application History</h4>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($apps) > 0): ?>
                                <?php foreach($apps as $app): ?>
                                <tr>
                                    <td>
                                        <a href="job-detail.php?id=<?php echo $app['job_id']; ?>" class="fw-bold text-decoration-none">
                                            <?php echo htmlspecialchars($app['title']); ?>
                                        </a>
                                        <br>
                                        <small class="text-muted"><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($app['location']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                                    <td><?php echo date('d M, Y', strtotime($app['applied_at'])); ?></td>
                                    <td>
                                        <?php if($app['status'] == 'viewed'): ?>
                                            <span class="badge bg-success">Viewed</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <!-- View Details -->
                                        <a href="job-detail.php?id=<?php echo $app['job_id']; ?>" class="btn btn-sm btn-outline-primary" title="View Job"><i class="fa fa-eye"></i></a>
                                        
                                        <!-- Chat with Provider -->
                                        <a href="chat.php?user=<?php echo $app['provider_id']; ?>" class="btn btn-sm btn-outline-info" title="Message Provider"><i class="fa fa-comment-o"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa fa-briefcase fa-3x mb-3 text-secondary"></i><br>
                                        You haven't applied to any jobs yet.
                                        <br>
                                        <a href="jobs.php" class="btn btn-primary mt-3">Find Jobs Now</a>
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