<?php 
require_once 'header.php'; 
checkLogin(); 

// Variables
$view = $_GET['view'] ?? 'overview';
$role = $_SESSION['role'] ?? 'applicant';
$uid = $_SESSION['user_id'];
?>

<div class="container py-5 flex-grow-1">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px;">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="assets/images/<?php echo !empty($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'avatar7.png'; ?>" class="rounded-circle border" width="80" height="80" style="object-fit: cover;">
                        <h6 class="mt-2 fw-bold"><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></h6>
                        <span class="badge bg-light text-dark border"><?php echo ucfirst($role); ?></span>
                    </div>
                    
                    <div class="nav flex-column nav-pills">
                        <a class="nav-link <?php echo $view=='overview'?'active':''; ?>" href="dashboard.php?view=overview"><i class="fa fa-home me-2"></i> Overview</a>
                        <a class="nav-link <?php echo $view=='profile'?'active':''; ?>" href="dashboard.php?view=profile"><i class="fa fa-user me-2"></i> Edit Profile</a>
                        <a class="nav-link <?php echo $view=='chats'?'active':''; ?>" href="dashboard.php?view=chats"><i class="fa fa-comments me-2"></i> Messages</a>
                        <a class="nav-link <?php echo $view=='notifications'?'active':''; ?>" href="dashboard.php?view=notifications"><i class="fa fa-bell me-2"></i> Notifications</a>
                        
                        <?php if($role == 'provider'): ?>
                            <a class="nav-link <?php echo $view=='my_jobs'?'active':''; ?>" href="dashboard.php?view=my_jobs"><i class="fa fa-briefcase me-2"></i> My Jobs</a>
                        <?php elseif($role == 'applicant'): ?>
                            <!--<a class="nav-link <?php echo $view=='my_apps'?'active':''; ?>" href="dashboard.php?view=my_apps"><i class="fa fa-file-text me-2"></i> My Applications</a>-->
                            <a class="nav-link" href="job-applied.php"><i class="fa fa-file-text me-2"></i> My Applications</a>
                        <!-- Active Class Applied Here -->
                            <a class="nav-link <?php echo $view=='saved'?'active':''; ?>" href="saved-jobs.php"><i class="fa fa-heart me-2"></i> Saved Jobs</a>
                            <a class="nav-link text-danger" href="logout.php"><i class="fa fa-sign-out me-2"></i> Logout</a>
                        <?php elseif($role == 'admin'): ?>
                            <hr class="dropdown-divider">
                            <h6 class="sidebar-heading text-muted text-uppercase small">Admin Panel</h6>
                            <a class="nav-link text-danger <?php echo $view=='admin_jobs'?'active bg-danger text-white':''; ?>" href="dashboard.php?view=admin_jobs"><i class="fa fa-gavel me-2"></i> Manage Jobs</a>
                            <a class="nav-link text-danger <?php echo $view=='users'?'active bg-danger text-white':''; ?>" href="dashboard.php?view=users"><i class="fa fa-users me-2"></i> All Users</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9">

            <!-- 1. EDIT PROFILE (FIXED LOGIC) -->
            <?php if($view == 'profile'): 
                // Fetch User Data
                $u = $pdo->query("SELECT * FROM users WHERE id = $uid")->fetch();

                if(isset($_POST['update_profile'])){
                    $name   = htmlspecialchars(trim($_POST['name']));
                    $mobile = htmlspecialchars(trim($_POST['mobile']));
                    $bio    = htmlspecialchars(trim($_POST['bio']));
                    
                    // --- 1. HANDLE IMAGE UPLOAD ---
                    $pic = $u['profile_pic']; // Default to current
                    if (!empty($_FILES['profile_pic']['name'])) {
                        $pName = time() . '_' . $_FILES['profile_pic']['name'];
                        $pTarget = 'assets/images/' . $pName;
                        if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $pTarget)) {
                            $pic = $pName;
                        }
                    }

                    // --- 2. HANDLE CV UPLOAD (Specific Logic) ---
                    $cv = $u['cv_file']; // Default to current (Don't overwrite with null)
                    
                    if ($role == 'applicant' && !empty($_FILES['cv']['name'])) {
                        // Check if folder exists, create if not
                        if (!is_dir('assets/docs')) { mkdir('assets/docs', 0777, true); }
                        
                        $allowedExt = ['pdf', 'doc', 'docx'];
                        $fileName = $_FILES['cv']['name'];
                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                        if(in_array($fileExt, $allowedExt)) {
                            $newCvName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $fileName);
                            $targetCv = 'assets/docs/' . $newCvName;
                            
                            if(move_uploaded_file($_FILES['cv']['tmp_name'], $targetCv)) {
                                $cv = $newCvName; // Update DB variable
                            } else {
                                echo "<script>alert('Error uploading CV. Check folder permissions.');</script>";
                            }
                        } else {
                            echo "<script>alert('Invalid CV Format. Only PDF, DOC, DOCX allowed.');</script>";
                        }
                    }

                    // --- 3. UPDATE DATABASE ---
                    $sql = "UPDATE users SET name=?, mobile=?, bio=?, profile_pic=?, cv_file=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    if($stmt->execute([$name, $mobile, $bio, $pic, $cv, $uid])) {
                        // Update Session immediately
                        $_SESSION['name'] = $name;
                        $_SESSION['profile_pic'] = $pic;
                        echo "<script>alert('Profile Updated Successfully!'); window.location.href='dashboard.php?view=profile';</script>";
                        exit;
                    }
                }
            ?>
            <div class="card shadow border-0 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Edit Profile</h4>
                    <?php if($u['is_verified']): ?>
                        <span class="badge bg-success p-2"><i class="fa fa-check-circle"></i> Verified</span>
                    <?php else: ?>
                        <a href="verify.php" class="btn btn-warning btn-sm"><i class="fa fa-exclamation-triangle"></i> Verify Account</a>
                    <?php endif; ?>
                </div>

                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($u['name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($u['email']); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mobile</label>
                            <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($u['mobile']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Profile Picture</label>
                            <input type="file" name="profile_pic" class="form-control">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">About Me</label>
                            <textarea name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($u['bio']); ?></textarea>
                        </div>

                        <!-- CV Upload Section (Enhanced) -->
                        <?php if($role == 'applicant'): ?>
                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold">CV / Resume</label>
                            <div class="input-group">
                                <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                            </div>
                            
                            <!-- Display Current CV -->
                            <?php if(!empty($u['cv_file'])): ?>
                                <div class="mt-2 p-2 bg-light border rounded d-flex align-items-center justify-content-between">
                                    <span class="text-success small">
                                        <i class="fa fa-check-circle"></i> CV Uploaded: <strong><?php echo htmlspecialchars($u['cv_file']); ?></strong>
                                    </span>
                                    <a href="assets/docs/<?php echo $u['cv_file']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-download"></i> View
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="mt-2 text-danger small"><i class="fa fa-times-circle"></i> No CV uploaded yet. You cannot apply to jobs.</div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                    </div>
                    <div class="text-end">
                        <button type="submit" name="update_profile" class="btn btn-primary px-4 fw-bold">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- 2. ADMIN MANAGE JOBS -->
            <?php elseif($view == 'admin_jobs' && $role == 'admin'): 
                if(isset($_GET['action']) && isset($_GET['jid'])) {
                    $act = $_GET['action']; // 1=Approve, 2=Reject
                    $targetJid = $_GET['jid'];
                    $pdo->prepare("UPDATE jobs SET is_approved=? WHERE id=?")->execute([$act, $targetJid]);
                    
                    // Notify Provider
                    $jData = $pdo->query("SELECT provider_id, title FROM jobs WHERE id=$targetJid")->fetch();
                    if ($jData) {
                        $msg = ($act==1) ? "Approved" : "Rejected";
                        notify($pdo, $jData['provider_id'], "Your job '{$jData['title']}' was $msg.", "job-detail.php?id=$targetJid");
                    }
                    echo "<script>window.location.href='dashboard.php?view=admin_jobs';</script>";
                }
                $jobs = $pdo->query("SELECT jobs.*, users.name as provider_name FROM jobs JOIN users ON jobs.provider_id = users.id ORDER BY is_approved ASC, created_at DESC")->fetchAll();
            ?>
            <div class="card shadow p-4 border-0">
                <h4 class="mb-4">Manage Job Posts</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark"><tr><th>Title</th><th>Provider</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach($jobs as $j): ?>
                            <tr>
                                <td><a href="job-detail.php?id=<?php echo $j['id']; ?>" target="_blank"><?php echo htmlspecialchars($j['title']); ?></a></td>
                                <td><?php echo $j['provider_name']; ?></td>
                                <td><?php echo date('d M', strtotime($j['created_at'])); ?></td>
                                <td>
                                    <?php 
                                        if($j['is_approved']==0) echo '<span class="badge bg-warning text-dark">Pending</span>';
                                        elseif($j['is_approved']==1) echo '<span class="badge bg-success">Active</span>';
                                        else echo '<span class="badge bg-danger">Rejected</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php if($j['is_approved']==0): ?>
                                    <a href="dashboard.php?view=admin_jobs&action=1&jid=<?php echo $j['id']; ?>" class="btn btn-sm btn-success"><i class="fa fa-check"></i></a>
                                    <a href="dashboard.php?view=admin_jobs&action=2&jid=<?php echo $j['id']; ?>" class="btn btn-sm btn-danger"><i class="fa fa-times"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. ADMIN: ALL USERS (Fixed Layout & Columns) -->
            <?php elseif($view == 'users' && $role == 'admin'): 
                // Handle Delete Logic
                if(isset($_GET['delete_user']) && $_GET['delete_user'] != $uid){
                    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$_GET['delete_user']]);
                    echo "<script>window.location='dashboard.php?view=users';</script>";
                }
                
                // Fetch Users
                $all_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
            ?>
            <div class="card shadow p-4 border-0">
                <h4 class="mb-4">Registered Users</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <!-- Define 6 Distinct Columns for perfect alignment -->
                            <tr>
                                <th style="width: 80px;">Photo</th>
                                <th>Name / Email</th>
                                <th>Role</th>
                                <th>Verification</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($all_users as $u): ?>
                            <tr>
                                <!-- 1. Photo Column -->
                                <td>
                                    <img src="assets/images/<?php echo !empty($u['profile_pic']) ? $u['profile_pic'] : 'default.png'; ?>" 
                                         class="rounded-circle border"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                </td>

                                <!-- 2. Name & Email Column -->
                                <td>
                                    <strong><?php echo htmlspecialchars($u['name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small>
                                </td>

                                <!-- 3. Role Column -->
                                <td>
                                    <?php 
                                        $r = strtolower(trim($u['role'] ?? 'provider'));
                                        
                                        if($r == 'admin') {
                                            $cls = 'bg-danger'; 
                                            $txt = 'Admin';
                                        } elseif($r == 'applicant') {
                                            $cls = 'bg-info text-dark'; 
                                            $txt = 'Applicant';
                                        } else { 
                                            // Force Blue/Provider if empty or 'provider'
                                            $cls = 'bg-primary text-dark'; 
                                            $txt = 'Provider';
                                        }
                                    ?>
                                    <span class="badge <?php echo $cls; ?>"><?php echo ucfirst($txt); ?></span>
                                </td>

                                <!-- 4. Verification Column -->
                                <td>
                                    <?php if($u['is_verified']): ?>
                                        <span class="badge bg-success"><i class="fa fa-check-circle"></i> Verified</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Unverified</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 5. Joined Date Column -->
                                <td><?php echo date('d M, y', strtotime($u['created_at'])); ?></td>

                                <!-- 6. Action Column -->
                                <td>
                                    <?php if($u['id'] != $uid): ?>
                                    <a href="dashboard.php?view=users&delete_user=<?php echo $u['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete user?');">
                                       <i class="fa fa-trash"></i>
                                    </a>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">You</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. PROVIDER: MY JOBS + DELETE FUNCTIONALITY -->
            <?php elseif($view == 'my_jobs' && $role == 'provider'): 

                // --- 1. HANDLE DELETE JOB (Logic Start) ---
                if(isset($_GET['delete_job'])) {
                    $jobId = $_GET['delete_job'];
                    // Secure delete: Only delete if ID matches AND Provider ID matches current user
                    $delStmt = $pdo->prepare("DELETE FROM jobs WHERE id = ? AND provider_id = ?");
                    if($delStmt->execute([$jobId, $uid])) {
                         echo "<script>alert('Job Post Deleted Successfully!'); window.location.href='dashboard.php?view=my_jobs';</script>";
                    } else {
                         echo "<script>alert('Error: You can only delete your own jobs.');</script>";
                    }
                }
                // --- (Logic End) ---

                // 2. Fetch Jobs
                $jobs = $pdo->query("SELECT * FROM jobs WHERE provider_id=$uid ORDER BY created_at DESC")->fetchAll();
            ?>
            <div class="card shadow p-4 border-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>My Posted Jobs</h4>
                    <a href="post-job.php" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Post Job</a>
                </div>
                
                <div class="list-group">
                <?php if(count($jobs) > 0): ?>
                    <?php foreach($jobs as $j): ?>
                        <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center p-3">
                            <div class="mb-2 mb-md-0">
                                <h5 class="mb-1">
                                    <a href="job-detail.php?id=<?php echo $j['id']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($j['title']); ?>
                                    </a>
                                </h5>
                                <span class="badge <?php echo $j['is_approved']?'bg-success':'bg-warning text-dark'; ?>">
                                    <?php echo $j['is_approved']?'Active / Live':'Pending Approval'; ?>
                                </span>
                                <span class="text-muted small ms-2"><i class="fa fa-clock-o"></i> <?php echo date('d M, Y', strtotime($j['created_at'])); ?></span>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <!-- View Applicants -->
                                <a href="job-applicants.php?id=<?php echo $j['id']; ?>" class="btn btn-sm btn-info text-white" title="View Applicants">
                                    <i class="fa fa-users"></i>
                                </a>
                                
                                <!-- View Job -->
                                <a href="job-detail.php?id=<?php echo $j['id']; ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </a>

                                <!-- DELETE BUTTON ADDED HERE -->
                                <a href="dashboard.php?view=my_jobs&delete_job=<?php echo $j['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this job?');" 
                                   title="Delete Job">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class='text-muted text-center py-5'>You haven't posted any jobs yet.</p>
                <?php endif; ?>
                </div>
            </div>

            <!-- 5. CHAT LIST -->
            <?php elseif($view == 'chats'): 
                $chats = $pdo->query("SELECT DISTINCT u.id, u.name, u.profile_pic FROM messages m JOIN users u ON (m.sender_id=u.id OR m.receiver_id=u.id) WHERE (m.sender_id=$uid OR m.receiver_id=$uid) AND u.id != $uid")->fetchAll();
            ?>
            <div class="card shadow p-4 border-0">
                <h4 class="mb-4">Messages</h4>
                <div class="list-group">
                <?php if($chats): foreach($chats as $c): ?>
                    <a href="chat.php?user=<?php echo $c['id']; ?>" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                        <!--<img src="assets/images/<?php echo !empty($c['profile_pic'])?$c['profile_pic']:'default.png'; ?>" class="rounded-circle border me-3" width="50" height="50" style="object-fit:cover;">
                -->
                        <img src="assets/images/<?php echo !empty($c['profile_pic'])?$c['profile_pic']:'default.png'; ?>" 
                             class="rounded-circle border me-3 flex-shrink-0" 
                             style="width: 50px !important; height: 50px !important; object-fit: cover;">
                        <div>
                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($c['name']); ?></h6>
                        </div>
                        <i class="fa fa-chevron-right ms-auto text-muted"></i>
                    </a>
                <?php endforeach; else: ?>
                    <!--<p class="text-center py-5 text-muted">No messages yet.</p>-->
                    <div class="text-center py-5">
                        <i class="fa fa-comments-o fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No conversations yet.</p>
                    </div>
                <?php endif; ?>
                

                <!-- PASTE THE NEW CODE HERE (BETWEEN CHATS AND DEFAULT) -->
                 <?php elseif($view == 'reports' && $role == 'admin'): ?>
                 <?php 
                // 1. Handle Delete
                 if(isset($_GET['del_report'])){
                    $id = $_GET['del_report'];
                    $pdo->prepare("DELETE FROM reports WHERE id=?")->execute([$id]);
                    echo "<script>window.location='dashboard.php?view=reports';</script>";
                    exit;
                }
                // 2. Fetch Reports
                $rSql = "SELECT r.*, reporter.name as reporter_name, accused.name as accused_name, j.title as job_title
                     FROM reports r
                     LEFT JOIN users reporter ON r.reporter_id = reporter.id
                     LEFT JOIN users accused ON r.reported_id = accused.id
                     LEFT JOIN jobs j ON r.job_id = j.id
                     ORDER BY r.created_at DESC";
                $reports = $pdo->query($rSql)->fetchAll();
                ?>
                <div class="card shadow p-4 border-0">
                    <h4 class="mb-4 text-danger"><i class="fa fa-flag"></i> Reports Center</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="bg-light text-center">
                            <tr><th>Reported By</th><th>Target</th><th>Reason</th><th>Date</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php if($reports): foreach($reports as $rp): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($rp['reporter_name'] ?? 'Unknown'); ?></td>
                                        <td>
                                            <?php if($rp['job_id']): ?> <span class="badge bg-warning text-dark">Job</span><?php endif; ?>
                                            <?php if($rp['reported_id']): ?> <span class="badge bg-info text-dark">User</span><?php endif; ?>
                                        </td>
                                        <td class="small"><?php echo htmlspecialchars($rp['reason']); ?></td>
                                        <td class="text-center text-muted"><?php echo date('d M', strtotime($rp['created_at'])); ?></td>
                                        <td><a href="dashboard.php?view=reports&del_report=<?php echo $rp['id']; ?>" class="btn btn-sm btn-outline-success w-100" onclick="return confirm('Resolve?');">Resolve</a></td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="5" class="text-center text-muted">No reports found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. DEFAULT (Notifications/Welcome) -->
            <?php else: 
                $pdo->query("UPDATE notifications SET is_read=1 WHERE user_id=$uid");
                $notifs = $pdo->query("SELECT * FROM notifications WHERE user_id=$uid ORDER BY created_at DESC LIMIT 20")->fetchAll();
            ?>
            <div class="alert alert-info border-0 shadow-sm">
                <strong>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</strong> Check your dashboard updates below.
            </div>
            <div class="card shadow border-0 mt-4">
                <div class="card-header bg-white border-bottom pt-3"><h5>Recent Notifications</h5></div>
                <div class="list-group list-group-flush">
                    <?php if($notifs): foreach($notifs as $n): ?>
                        <a href="<?php echo $n['link']; ?>" class="list-group-item list-group-item-action py-3">
                            <div class="d-flex justify-content-between">
                                <span><?php echo $n['message']; ?></span>
                                <small class="text-muted"><?php echo date('M d, H:i', strtotime($n['created_at'])); ?></small>
                            </div>
                        </a>
                    <?php endforeach; else: ?>
                        <div class="p-4 text-center text-muted">No new notifications.</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>