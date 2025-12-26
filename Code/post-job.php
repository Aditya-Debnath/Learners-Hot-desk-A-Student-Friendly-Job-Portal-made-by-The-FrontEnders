<?php 
require_once 'header.php'; 
checkLogin(); 

// Ensure only Providers (or Admins) can access
if($_SESSION['role'] != 'provider' && $_SESSION['role'] != 'admin') {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

// Verification Check
$u = $pdo->query("SELECT is_verified FROM users WHERE id=".$_SESSION['user_id'])->fetch();
if(!$u['is_verified']) {
    echo "<script>alert('Please verify your account first!'); window.location.href='verify.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Status Logic: Admins auto-approve, Providers pending
    $initial_status = ($_SESSION['role'] == 'admin') ? 1 : 0;
    
    // Validate inputs
    $title = htmlspecialchars($_POST['title']);
    $cat = $_POST['category'];
    $nature = $_POST['job_nature'];
    $vac = (int)$_POST['vacancy'];
    $sal = htmlspecialchars($_POST['salary']);
    $loc = htmlspecialchars($_POST['location']);
    $desc = htmlspecialchars($_POST['description']);
    $ben = htmlspecialchars($_POST['benefits']);
    $resp = htmlspecialchars($_POST['responsibility']);
    $qual = htmlspecialchars($_POST['qualifications']);
    $keys = htmlspecialchars($_POST['keywords']);
    $c_name = htmlspecialchars($_POST['company_name']);
    $c_loc = htmlspecialchars($_POST['company_location']);
    $c_web = htmlspecialchars($_POST['company_website']);
    
    $deadline = $_POST['deadline']; 

    // --- FIX IS HERE: Changed 'category_id' to 'category' ---
    $sql = "INSERT INTO jobs (
                provider_id, title, category, job_nature, 
                vacancy, salary, location, description, 
                benefits, responsibility, qualifications, 
                keywords, company_name, company_location, company_website, 
                deadline, is_active, is_approved
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $pdo->prepare($sql);
    
    try {
        $result = $stmt->execute([
            $_SESSION['user_id'], $title, $cat, $nature, 
            $vac, $sal, $loc, $desc, 
            $ben, $resp, $qual, 
            $keys, $c_name, $c_loc, $c_web, 
            $deadline, $initial_status, $initial_status
        ]);
        
        if($result) {
            $msg = ($_SESSION['role'] == 'admin') ? 'Job Posted Successfully!' : 'Job Posted! Pending Admin Approval.';
            echo "<script>alert('$msg'); window.location.href='dashboard.php?view=my_jobs';</script>";
        }
    } catch (PDOException $e) {
        // Detailed error reporting to help if anything else goes wrong
        echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Get Categories
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <nav aria-label="breadcrumb" class="bg-white rounded-3 p-3 mb-4 shadow-sm">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Post a Job</li>
                    </ol>
                </nav>

                <form method="post">
                    <div class="card border-0 shadow mb-4">
                        <div class="card-body card-form p-4">
                            <h3 class="fs-4 mb-4 border-bottom pb-2">Job Details</h3>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="mb-2 fw-bold">Title *</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Senior React Developer" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="mb-2 fw-bold">Category *</label>
                                    <select name="category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <!-- Store Category ID as value -->
                                        <?php foreach($cats as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label class="mb-2 fw-bold">Job Type</label>
                                    <select class="form-select" name="job_nature">
                                        <option>Full Time</option>
                                        <option>Part Time</option>
                                        <option>Remote</option>
                                        <option>Freelance</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="mb-2 fw-bold">Vacancy</label>
                                    <input type="number" name="vacancy" class="form-control" min="1" placeholder="e.g. 3">
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <label class="mb-2 fw-bold text-danger">Deadline *</label>
                                    <input type="date" name="deadline" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-4 col-md-6">
                                    <label class="mb-2 fw-bold">Salary</label>
                                    <input type="text" name="salary" class="form-control" placeholder="e.g. $50k - $70k">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="mb-2 fw-bold">Job Location *</label>
                                    <input type="text" name="location" class="form-control" placeholder="City, Country" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="mb-2 fw-bold">Job Description</label>
                                <textarea class="form-control" name="description" rows="5" required></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="mb-2 fw-bold">Responsibility</label>
                                <textarea class="form-control" name="responsibility" rows="3"></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="mb-2 fw-bold">Qualifications</label>
                                <textarea class="form-control" name="qualifications" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="mb-2 fw-bold">Benefits</label>
                                <textarea class="form-control" name="benefits" rows="3"></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="mb-2 fw-bold">Keywords</label>
                                <input type="text" name="keywords" class="form-control" placeholder="e.g. design, coding, java">
                            </div>

                            <h3 class="fs-4 mb-4 mt-5 border-bottom pb-2">Company Details</h3>

                            <div class="row">
                                <div class="mb-4 col-md-6">
                                    <label class="mb-2 fw-bold">Company Name</label>
                                    <input type="text" name="company_name" class="form-control">
                                </div>

                                <div class="mb-4 col-md-6">
                                    <label class="mb-2 fw-bold">Company Location</label>
                                    <input type="text" name="company_location" class="form-control">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="mb-2 fw-bold">Website</label>
                                <input type="text" name="company_website" class="form-control" placeholder="https://...">
                            </div>
                        </div> 
                        
                        <div class="card-footer p-4 text-end">
                            <a href="dashboard.php" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5">Publish Job</button>
                        </div>               
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once 'footer.php'; ?>