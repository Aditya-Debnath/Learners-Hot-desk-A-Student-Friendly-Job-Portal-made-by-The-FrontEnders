<?php 
// Include the header which contains the session logic, navbar, and CSS links
include 'header.php'; 
?>

<style>
    /* Custom styles for the About Page */
    .about-hero {
        background: linear-gradient(rgba(40, 167, 69, 0.1), rgba(255, 255, 255, 1));
        padding: 80px 0;
        text-align: center;
    }
    .mission-section {
        padding: 60px 0;
    }
    .team-section {
        background-color: #f8f9fa;
        padding: 60px 0;
    }
    .team-card {
        border: none;
        transition: transform 0.3s;
        background: transparent;
    }
    .team-card:hover {
        transform: translateY(-10px);
    }
    .team-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 20px;
        border: 5px solid #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .section-title {
        position: relative;
        margin-bottom: 40px;
        font-weight: 700;
    }
    .section-title::after {
        content: '';
        width: 60px;
        height: 3px;
        background: #28a745;
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }
</style>

<main>
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <h1 class="display-4 fw-bold">Learners' Hot-desk</h1>
            <p class="lead text-success">"Your Friendly Job Agent"</p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h2 class="section-title">Our Goal</h2>
                    <p class="fs-5 text-muted">
                        Our main goal is to provide jobs to students for a better life and a better economy. 
                        We believe that by empowering the student community with professional opportunities, 
                        we help to grow our nation and build a brighter future for everyone.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="text-center section-title">Meet The FrontEnders ✨</h2>
            <div class="row text-center mt-5">
                
                <!-- Member 1 -->
                <div class="col-md-4 mb-4">
                    <div class="card team-card">
                        <img src="assets/images/avatar7.png" class="team-img" alt="Sayma Sultana">
                        <div class="card-body">
                            <h4 class="card-title">Sayma Sultana</h4>
                            <p class="text-success fw-bold">Developer</p>
                        </div>
                    </div>
                </div>

                <!-- Member 2 -->
                <div class="col-md-4 mb-4">
                    <div class="card team-card">
                        <img src="assets/images/avatar7.png" class="team-img" alt="Aditya Debnath">
                        <div class="card-body">
                            <h4 class="card-title">Aditya Debnath</h4>
                            <p class="text-success fw-bold">Developer</p>
                        </div>
                    </div>
                </div>

                <!-- Member 3 -->
                <div class="col-md-4 mb-4">
                    <div class="card team-card">
                        <img src="assets/images/avatar7.png" class="team-img" alt="Afia Tabassum">
                        <div class="card-body">
                            <h4 class="card-title">Afia Tabassum</h4>
                            <p class="text-success fw-bold">Developer</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 text-center">
        <div class="container">
            <h3>Ready to start your journey?</h3>
            <p>Join Learners' Hot-desk today and find the perfect job for your skills.</p>
            <a href="register.php" class="btn btn-primary btn-lg mt-3">Join Us Now</a>
        </div>
    </section>
</main>

<?php 
// Include the footer which contains the JS scripts and closing body/html tags
include 'footer.php'; 
?>