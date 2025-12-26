<?php 
require_once 'header.php'; 

$jid = $_GET['id'] ?? 0;
if($jid == 0) { echo "<script>window.location.href='jobs.php';</script>"; exit; }

// Fetch Job
$stmt = $pdo->prepare("SELECT jobs.*, users.name as provider_name, users.id as pid FROM jobs LEFT JOIN users ON jobs.provider_id=users.id WHERE jobs.id = ?");
$stmt->execute([$jid]);
$job = $stmt->fetch();

if(!$job) { echo "<div class='container py-5 alert alert-danger'>Job not found.</div>"; require_once 'footer.php'; exit; }

// === DEADLINE CHECK LOGIC ===
$today = date("Y-m-d");
$deadline = $job['deadline'];
$is_expired = ($deadline && $deadline < $today); // True if deadline passed
// =============================

if(isset($_POST['apply'])){
    checkLogin();
    
    // Prevent applying if expired
    if ($is_expired) {
        echo "<script>alert('Sorry, this job has expired.');</script>";
    } else {
        $uStmt = $pdo->prepare("SELECT is_verified, cv_file FROM users WHERE id=?");
        $uStmt->execute([$_SESSION['user_id']]);
        $curr = $uStmt->fetch();

        if(!$curr['is_verified']) echo "<script>window.location='verify.php?msg=verify_first';</script>";
        elseif(!$curr['cv_file']) echo "<script>alert('Please upload a CV in Profile first!'); window.location.href='dashboard.php?view=profile';</script>";
        else {
            $chk = $pdo->prepare("SELECT id FROM applications WHERE job_id=? AND applicant_id=?");
            $chk->execute([$jid, $_SESSION['user_id']]);
            if($chk->rowCount() == 0) {
                $pdo->prepare("INSERT INTO applications (job_id, applicant_id) VALUES (?,?)")->execute([$jid, $_SESSION['user_id']]);
                notify($pdo, $job['pid'], "New applicant for ".$job['title'], "dashboard.php?view=my_jobs");
                echo "<script>alert('Applied Successfully!');</script>";
            } else {
                echo "<script>alert('Already Applied.');</script>";
            }
        }
    }
}
?>

<section class="section-4 bg-2">    
    <div class="container pt-5">
        <div class="row pb-5">
            <!-- Left Side -->
            <div class="col-md-8">
                <div class="card shadow border-0 mb-4">
                    <div class="job_details_header p-4">
                        <h4 class="mb-1" style="font-size: 2rem;"><?php echo htmlspecialchars($job['title']); ?></h4>
                        <div class="text-muted mb-2">
                            <i class="fa fa-building"></i> <?php echo htmlspecialchars($job['company_name']); ?> | 
                            <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($job['location']); ?>
                        </div>
                        
                        <?php if($is_expired): ?>
                            <span class="badge bg-danger p-2"><i class="fa fa-calendar-times-o"></i> Application Closed</span>
                        <?php else: ?>
                            <span class="badge bg-success p-2"><i class="fa fa-check-circle"></i> Open for Applications</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="descript_wrap white-bg p-4 border-top">
                        <div class="single_wrap mb-3"><h4>Description</h4><p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p></div>
                        <?php if($job['responsibility']): ?>
                        <div class="single_wrap mb-3"><h4>Responsibility</h4><p><?php echo nl2br(htmlspecialchars($job['responsibility'])); ?></p></div>
                        <?php endif; ?>
                        <?php if($job['qualifications']): ?>
                        <div class="single_wrap mb-3"><h4>Qualifications</h4><p><?php echo nl2br(htmlspecialchars($job['qualifications'])); ?></p></div>
                        <?php endif; ?>
                        <?php if($job['benefits']): ?>
                        <div class="single_wrap mb-3"><h4>Benefits</h4><p><?php echo nl2br(htmlspecialchars($job['benefits'])); ?></p></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Side (Summary & Actions) -->
            <div class="col-md-4">
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white pt-4">
                        <h5 class="fw-bold">Job Overview</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><strong>Published:</strong> <?php echo date('d M, Y', strtotime($job['created_at'])); ?></li>
                            
                            <!-- DEADLINE DISPLAY -->
                            <li class="mb-2 <?php echo $is_expired ? 'text-danger fw-bold' : ''; ?>">
                                <strong>Deadline:</strong> 
                                <?php echo $job['deadline'] ? date('d M, Y', strtotime($job['deadline'])) : 'Open'; ?>
                                <?php if($is_expired) echo "(Expired)"; ?>
                            </li>

                            <li class="mb-2"><strong>Vacancy:</strong> <?php echo $job['vacancy']; ?></li>
                            <li class="mb-2"><strong>Salary:</strong> <?php echo $job['salary']; ?></li>
                            <li class="mb-2"><strong>Nature:</strong> <?php echo $job['job_nature']; ?></li>
                        </ul>
                    </div>

                    <div class="card-footer bg-white pb-4 border-0">
                        
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] === 'applicant'): ?>
                            
                            <?php if(!$is_expired): ?>
                                <form method="post" class="mb-2">
                                    <button name="apply" class="btn btn-primary w-100 btn-lg">Apply Now</button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 mb-2" disabled>Applications Closed</button>
                            <?php endif; ?>

                            <a href="saved-jobs.php?add=<?php echo $jid; ?>" class="btn btn-light border w-100 mb-2 text-danger">
                                <i class="fa fa-heart-o"></i> Save Job
                            </a>
                            <a href="chat.php?user=<?php echo $job['pid']; ?>" class="btn btn-outline-info w-100 mb-2">
                                <i class="fa fa-comments"></i> Message Provider
                            </a>
                            <div class="text-center mt-2"><a href="report.php?type=job&id=<?php echo $jid; ?>" class="text-muted small"><i class="fa fa-flag"></i> Report Job</a></div>

                        <?php elseif(!isset($_SESSION['user_id'])): ?>
                            <a href="login.php" class="btn btn-primary w-100">Login to Apply</a>
                        <?php endif; ?>

                    </div>
                </div>
                
                <div class="card shadow border-0">
                    <div class="card-body">
                        <h5>Company</h5>
                        <p class="mb-1"><strong><?php echo htmlspecialchars($job['company_name']); ?></strong></p>
                        <p class="mb-0 text-muted"><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($job['company_location'] ?? 'Not set'); ?></p>
                        <?php if(!empty($job['company_website'])): ?>
                        <hr>
                        <a href="<?php echo htmlspecialchars($job['company_website']); ?>" target="_blank" class="small">Visit Website</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once 'footer.php'; ?>