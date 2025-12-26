<?php 
require_once 'config.php'; 

// Fetch Notification Count
$notif_count = 0;
if(isset($_SESSION['user_id'])) {
    $nStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
    $nStmt->execute([$_SESSION['user_id']]);
    $notif_count = $nStmt->fetchColumn();
}

// SAFE SESSION HANDLING: Set defaults if keys are missing
$user_name = $_SESSION['name'] ?? 'User'; 
$user_role = $_SESSION['role'] ?? 'applicant';
$user_img  = $_SESSION['profile_pic'] ?? 'avatar7.png'; // Using avatar7.png since it exists in your folder
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learners' Hot-desk - Your Friendly Job Agent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
    
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        footer { margin-top: auto; }
        
        /* FIX IMAGE SIZING */
        .nav-avatar { 
            width: 35px; 
            height: 35px; 
            border-radius: 50%; 
            object-fit: cover; /* Prevents squashing */
            border: 2px solid #28a745; 
            margin-right: 10px;
        }
        
        .notify-badge { 
            position: absolute; top: -5px; right: -5px; padding: 2px 5px; 
            border-radius: 50%; background: red; color: white; font-size: 10px;
        }
        /* === CHAT SYSTEM STYLES === */
        .chat-box { 
            max-height: 400px; 
            overflow-y: auto; 
            background: #f9f9f9; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .message-sent { text-align: right; margin-bottom: 10px; }
        .message-sent .msg-bubble { background: #d1e7dd; display: inline-block; padding: 8px 12px; border-radius: 10px; text-align: left; }
        .message-received { text-align: left; margin-bottom: 10px; }
        .message-received .msg-bubble { background: #fff; border: 1px solid #ddd; display: inline-block; padding: 8px 12px; border-radius: 10px; }
        .text-tiny { font-size: 0.70rem; color: #999; display: block; margin-top: 2px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow py-3">
    <div class="container">
        <a class="navbar-brand" href="index.php">Learners' Hot-desk</a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="jobs.php">Find Jobs</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item position-relative mx-3">
                        <a href="dashboard.php?view=notifications" class="text-dark"><i class="fa fa-bell fa-lg"></i>
                        <?php if($notif_count > 0) echo "<span class='notify-badge'>$notif_count</span>"; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <!-- Secure Image Handling -->
                            <img src="assets/images/<?php echo !empty($user_img) ? $user_img : 'avatar7.png'; ?>" class="nav-avatar">
                            
                            <!-- Secure Name Handling -->
                            <span><?php echo htmlspecialchars($user_name); ?> (<?php echo ucfirst($user_role); ?>)</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <?php if($user_role === 'provider'): ?>
                                <li><a class="dropdown-item" href="post-job.php">Post Job</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-outline-primary me-2" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>