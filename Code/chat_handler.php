<?php
// chat_handler.php
require_once 'config.php';

// Stop if not logged in
if (!isset($_SESSION['user_id'])) exit;

$my_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// 1. SAVE MESSAGE
if ($action === 'send') {
    $receiver_id = (int)$_POST['receiver_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $pdo->prepare($sql)->execute([$my_id, $receiver_id, $message]);
        
        // (Optional) Update/Create Notification for receiver
        notify($pdo, $receiver_id, "New message from " . $_SESSION['name'], "chat.php?user_id=" . $my_id);
    }
}

// 2. FETCH MESSAGES
if ($action === 'fetch') {
    $partner_id = (int)$_POST['partner_id'];

    // Mark incoming messages as read
    $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?")
        ->execute([$partner_id, $my_id]);

    // Fetch conversation history
    $sql = "SELECT * FROM messages 
            WHERE (sender_id = ? AND receiver_id = ?) 
               OR (sender_id = ? AND receiver_id = ?) 
            ORDER BY created_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$my_id, $partner_id, $partner_id, $my_id]);
    $msgs = $stmt->fetchAll();

    if ($msgs) {
        foreach ($msgs as $m) {
            $isMe = ($m['sender_id'] == $my_id);
            
            // Align: Right (Me) vs Left (Partner)
            $alignClass = $isMe ? "align-items-end" : "align-items-start";
            
            // Color: Blue (Me) vs White (Partner)
            // Note: Added text-dark for received to ensure Black Text
            $bubbleClass = $isMe ? "bg-primary text-white" : "bg-white border text-dark";
            
            // Border Radius tweaks
            $radius = $isMe ? "border-radius: 15px 15px 0 15px;" : "border-radius: 15px 15px 15px 0;";

            $time = date("g:i a", strtotime($m['created_at']));
            $content = nl2br(htmlspecialchars($m['message']));

            echo "
            <div class='d-flex flex-column mb-2 $alignClass' style='width: 100%;'>
                <div class='p-3 shadow-sm' style='max-width: 75%; font-size: 0.95rem; $bubbleClass $radius'>
                    $content
                </div>
                <small class='text-muted mt-1' style='font-size: 0.70rem;'>$time</small>
            </div>
            ";
        }
    } else {
        echo "<div class='text-center mt-5 text-muted'><p>Say 'Hi' to start the conversation!</p></div>";
    }
}
?>