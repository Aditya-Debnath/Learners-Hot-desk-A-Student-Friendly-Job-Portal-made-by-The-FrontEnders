<?php 
include 'header.php'; 
checkLogin(); 

// 1. Validation: Is ID provided?
if(!isset($_GET['id'])) {
    echo "<script>window.location.href='dashboard.php?view=my_jobs';</script>";
    exit;
}

$jid = $_GET['id'];
$uid = $_SESSION['user_id'];

// 2. Security: Ensure the logged-in Provider OWNS this job
$check = $pdo->prepare("SELECT title FROM jobs WHERE id=? AND provider_id=?");
$check->execute([$jid, $uid]);
$job = $check->fetch();

if(!$job) {
    echo "<div class='container py-5 text-center'><h3>Access Denied or Job Not Found</h3></div>";
    include 'footer.php'; exit;
}

// 3. Update Status if provider clicks "Viewed" (Optional feature, marked for logic completeness)
if(isset($_GET['mark_viewed'])) {
    $app_id = $_GET['mark_viewed'];
    $pdo->prepare("UPDATE applications SET status='viewed' WHERE id=?")->execute([$app_id]);
    echo "<script>window.location.href='job-applicants.php?id=$jid';</script>";
}

// 4. Fetch Applicants & Their CV Data
// We join 'applications' with 'users' to get name, email, mobile, and CV file.
$sql = "SELECT users.id as user_id, users.name, users.email, users.mobile, users.cv_file, users.profile_pic, 
               applications.id as app_id, applications.applied_at, applications.status 
        FROM applications 
        JOIN users ON applications.applicant_id = users.id 
        WHERE applications.job_id = ? 
        ORDER BY applications.applied_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$jid]);
$applicants = $stmt->fetchAll();
?>

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="bg-white rounded-3 p-3 mb-4 shadow-sm">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard.php?view=my_jobs">My Jobs</a></li>
                        <li class="breadcrumb-item active">Applicants for: <strong><?php echo htmlspecialchars($job['title']); ?></strong></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0 p-4">
                    <h4 class="mb-4">Total Applicants: <?php echo count($applicants); ?></h4>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Applicant</th>
                                    <th>Contact Info</th>
                                    <th>Applied Date</th>
                                    <th>CV / Resume</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($applicants) > 0): ?>
                                    <?php foreach($applicants as $app): ?>
                                    <tr class="<?php echo ($app['status']=='pending')?'table-warning':''; ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="assets/images/<?php echo $app['profile_pic'] ? $app['profile_pic'] : 'default.png'; ?>" class="rounded-circle me-2" style="width:40px; height:40px; object-fit:cover;">
                                                <div>
                                                    <span class="fw-bold"><?php echo htmlspecialchars($app['name']); ?></span>
                                                    <br>
                                                    <span class="badge bg-secondary text-light" style="font-size:10px">ID: <?php echo $app['user_id']; ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fa fa-envelope text-muted"></i> <?php echo htmlspecialchars($app['email']); ?><br>
                                            <i class="fa fa-phone text-muted"></i> <?php echo htmlspecialchars($app['mobile']); ?>
                                        </td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($app['applied_at'])); ?></td>
                                        <td>
                                            <?php if(!empty($app['cv_file'])): ?>
                                                <a href="assets/docs/<?php echo htmlspecialchars($app['cv_file']); ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                                                    <i class="fa fa-file-pdf-o text-danger"></i> Download CV
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted font-italic">No CV Uploaded</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- Start Chat -->
                                            <a href="chat.php?user_id=<?php echo $app['user_id']; ?>" class="btn btn-sm btn-primary" title="Message">
                                                <i class="fa fa-comment"></i>
                                            </a>
                                            
                                            <?php if($app['status'] == 'pending'): ?>
                                            <a href="job-applicants.php?id=<?php echo $jid; ?>&mark_viewed=<?php echo $app['app_id']; ?>" class="btn btn-sm btn-success" title="Mark as Seen">
                                                <i class="fa fa-check"></i>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- Report User -->
                                             <a href="report.php?target=user&id=<?php echo $app['user_id']; ?>" class="btn btn-sm btn-outline-danger" title="Report Applicant"><i class="fa fa-flag"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">No applicants for this job yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>