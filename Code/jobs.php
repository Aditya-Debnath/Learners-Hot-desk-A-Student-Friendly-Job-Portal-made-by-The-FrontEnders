<?php 
include 'header.php'; 

// Filter Logic
$where = "WHERE is_approved = 1";
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $where .= " AND (title LIKE ? OR keywords LIKE ? OR company_name LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}

if (isset($_GET['location']) && !empty($_GET['location'])) {
    $where .= " AND location LIKE ?";
    $params[] = "%" . trim($_GET['location']) . "%";
}

$sql = "SELECT * FROM jobs $where ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();
?>

<section class="section-3 py-5 bg-2">
    <div class="container">     
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2>Find Jobs</h2>  
                <p>Browse the latest approved listings</p>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-md-3 sidebar mb-4">
                <div class="card border-0 shadow p-4">
                    <form action="jobs.php" method="GET">
                        <div class="mb-4">
                            <label class="form-label">Keywords</label>
                            <input type="text" name="search" value="<?php echo $_GET['search'] ?? ''; ?>" placeholder="Job Title, Keyword..." class="form-control">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" value="<?php echo $_GET['location'] ?? ''; ?>" placeholder="City, State..." class="form-control">
                        </div>

                        <button class="btn btn-primary w-100">Filter Jobs</button>
                        <a href="jobs.php" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
                    </form>
                </div>
            </div>

            <!-- Job Listing -->
            <div class="col-md-9">
                <div class="job_listing_area">                    
                    <div class="job_lists">
                        <div class="row">
                            <?php if(count($jobs) > 0): ?>
                                <?php foreach($jobs as $j): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card border-0 p-3 shadow h-100 d-flex flex-column">
                                        <div class="card-body">
                                            <h4 class="border-0 fs-5 pb-2 mb-0">
                                                <a href="job-detail.php?id=<?php echo $j['id']; ?>" class="text-dark text-decoration-none">
                                                    <?php echo htmlspecialchars($j['title']); ?>
                                                </a>
                                            </h4>
                                            <small class="text-muted"><?php echo htmlspecialchars($j['company_name']); ?></small>
                                            
                                            <div class="bg-light p-2 border mt-3 rounded">
                                                <p class="mb-0 text-truncate"><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($j['location']); ?></p>
                                                <p class="mb-0"><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($j['job_nature']); ?></p>
                                                <p class="mb-0 text-success"><i class="fa fa-money"></i> <?php echo htmlspecialchars($j['salary']); ?></p>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-0 mt-auto">
                                            <div class="d-grid">
                                                <a href="job-detail.php?id=<?php echo $j['id']; ?>" class="btn btn-outline-primary">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info text-center">No jobs found matching your criteria.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>