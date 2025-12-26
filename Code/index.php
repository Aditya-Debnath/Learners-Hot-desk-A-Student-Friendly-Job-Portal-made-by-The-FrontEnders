<?php include 'header.php'; ?>
<section class="section-0" style="background: url('assets/images/banner5.jpg') no-repeat center; background-size:cover; min-height:500px; display:flex; align-items:center;">
    <div class="container text-white">
        <h1>Find your best part-time job</h1>
        <p>Student Friendly Job Portal</p>
        <a href="jobs.php" class="btn btn-primary mt-3">Browse Jobs</a>
    </div>
</section>

<section class="container py-5">
    <h2>Recent Jobs</h2>
    <div class="row pt-4">
    <?php 
    // Show only approved jobs (is_approved=1)
    $jobs = $pdo->query("SELECT * FROM jobs WHERE is_approved = 1 ORDER BY id DESC LIMIT 6")->fetchAll();
    foreach($jobs as $j):
    ?>
        <div class="col-md-4 mb-4">
            <div class="card p-3 shadow-sm h-100 border-0">
                <h5><?php echo $j['title']; ?></h5>
                <p class="text-muted"><?php echo $j['location']; ?></p>
                <div class="mt-auto">
                    <span class="badge bg-light text-dark border"><?php echo $j['job_nature']; ?></span>
                    <a href="job-detail.php?id=<?php echo $j['id']; ?>" class="btn btn-primary btn-sm float-end">View</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</section>
<?php include 'footer.php'; ?>