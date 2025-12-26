<?php 
require_once 'header.php'; 
checkLogin(); 

$my_id = $_SESSION['user_id'];
$chat_partner_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$partner_details = null;

// 1. Fetch Contact List (People I have chatted with)
$contacts_sql = "
    SELECT DISTINCT u.id, u.name, u.profile_pic, u.role,
           (SELECT message FROM messages WHERE (sender_id = u.id AND receiver_id = $my_id) OR (sender_id = $my_id AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_msg
    FROM users u
    JOIN messages m ON (u.id = m.sender_id OR u.id = m.receiver_id)
    WHERE (m.sender_id = $my_id OR m.receiver_id = $my_id) AND u.id != $my_id
    ORDER BY m.created_at DESC
";
$contacts = $pdo->query($contacts_sql)->fetchAll();

// 2. Check if a new chat is being started via URL (from Job/Applicant page)
if ($chat_partner_id && $chat_partner_id != $my_id) {
    // Fetch details of the person we want to chat with
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$chat_partner_id]);
    $partner_details = $stmt->fetch();
}

?>

<div class="container py-5" style="height: 85vh;">
    <div class="card shadow border-0 h-100 overflow-hidden">
        <div class="row g-0 h-100">
            
            <!-- LEFT SIDEBAR: Contact List -->
            <div class="col-md-4 border-end bg-light d-flex flex-column h-100">
                <div class="p-3 bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fa fa-comments me-2 text-primary"></i> Messages</h5>
                </div>
                
                <div class="contacts-list flex-grow-1 overflow-auto p-2">
                    <!-- Existing Contacts -->
                    <?php foreach($contacts as $c): ?>
                        <a href="chat.php?user_id=<?php echo $c['id']; ?>" class="d-flex align-items-center p-2 mb-1 rounded text-decoration-none text-dark <?php echo ($chat_partner_id == $c['id']) ? 'bg-white shadow-sm border-start border-4 border-primary' : ''; ?> hover-bg-gray">
                            <img src="assets/images/<?php echo $c['profile_pic'] ? $c['profile_pic'] : 'default.png'; ?>" 
                                 class="rounded-circle me-3 border flex-shrink-0" style="width: 45px !important; height: 45px !important; object-fit: cover;">
                            <div class="w-100 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($c['name']); ?></h6>
                                    <span class="badge bg-secondary" style="font-size:10px;"><?php echo ucfirst($c['role']); ?></span>
                                </div>
                                <small class="text-muted text-truncate d-block" style="font-size: 0.85rem;"><?php echo htmlspecialchars(substr($c['last_msg'], 0, 30)); ?>...</small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- RIGHT SIDE: Chat Area -->
            <div class="col-md-8 d-flex flex-column bg-white h-100">
                
                <?php if ($partner_details): ?>
                    <!-- Header -->
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white shadow-sm" style="z-index: 10;">
                        <div class="d-flex align-items-center">
                            <img src="assets/images/<?php echo $partner_details['profile_pic'] ? $partner_details['profile_pic'] : 'default.png'; ?>" class="rounded-circle me-2 border" style="width: 45px; height: 45px; object-fit: cover;">
                            <div>
                                <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($partner_details['name']); ?></h5>
                                <small class="text-success"><i class="fa fa-circle text-xs"></i> Online</small>
                            </div>
                        </div>
                        <!-- Report User Button -->
                        <a href="report.php?target=user&id=<?php echo $partner_details['id']; ?>" class="btn btn-outline-danger btn-sm" title="Report User"><i class="fa fa-flag"></i></a>
                    </div>

                    <!-- Messages Box (Scrollable) -->
                    <div id="chatBox" class="flex-grow-1 p-4 d-flex flex-column" style="overflow-y: auto; background-color: #f4f6f8;">
                        <div class="text-center mt-5"><div class="spinner-border text-primary"></div></div>
                    </div>

                    <!-- Footer (Input) -->
                    <div class="p-3 border-top bg-light">
                        <form id="chatForm" class="d-flex gap-2">
                            <input type="hidden" id="receiver_id" value="<?php echo $chat_partner_id; ?>">
                            <input type="text" id="msgInput" class="form-control rounded-pill p-3 border-0 shadow-sm" placeholder="Type your message..." required autocomplete="off">
                            <button type="submit" class="btn btn-primary rounded-circle shadow-sm" style="width: 50px; height: 50px;"><i class="fa fa-paper-plane"></i></button>
                        </form>
                    </div>

                    <style>
                    /* SIDEBAR STYLES */
                    .active-chat-link { background-color: #e9f5ff; border-left: 4px solid #007bff; }
                     /* Strict Image Sizing for Contact List */
                     .contacts-list img.rounded-circle {
                        width: 45px !important;
                        height: 45px !important;
                        min-width: 45px; /* Prevents squashing */
                        object-fit: cover;
                    }
                    /* MESSAGE BUBBLES */
                    .message-sent .msg-bubble { 
                        background: #0084ff;  /* Facebook Messenger Blue */
                        color: white; 
                        border-radius: 15px 15px 0 15px; 
                    }
                    /* RECEIVED: White Background with Dark Border & Black Text */
                    .message-received .msg-bubble { 
                        background: #ffffff; 
                        color: #212529 !important; /* Forces BLACK TEXT */
                        border: 1px solid #dee2e6;
                        border-radius: 15px 15px 15px 0;
                    }
                    /* Message Wrapper Constraints */
                    .msg-wrapper { 
                        max-width: 75%; 
                        word-wrap: break-word; /* Prevents long text breaking layout */
                        }
                    </style>

                    <!-- AUTO SCROLL & AJAX -->
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const chatBox = document.getElementById("chatBox");
                            const receiverId = document.getElementById("receiver_id").value;
                            let isAutoScroll = true;

                            // Function: Fetch Messages
                            function loadMessages() {
                                let fd = new FormData();
                                fd.append('action', 'fetch');
                                fd.append('partner_id', receiverId);

                                fetch('chat_handler.php', { method: 'POST', body: fd })
                                .then(res => res.text())
                                .then(html => {
                                    chatBox.innerHTML = html;
                                    if(isAutoScroll) {
                                        chatBox.scrollTop = chatBox.scrollHeight;
                                    }
                                });
                            }

                            // Detect Scroll (Stop auto-scroll if user looks at old messages)
                            chatBox.addEventListener("scroll", function(){
                                if (chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 50) {
                                    isAutoScroll = true;
                                } else {
                                    isAutoScroll = false;
                                }
                            });

                            // Init
                            loadMessages();
                            setInterval(loadMessages, 3000); // Check new messages every 3 sec

                            // Send Message
                            document.getElementById("chatForm").addEventListener("submit", function(e) {
                                e.preventDefault();
                                const input = document.getElementById("msgInput");
                                const msg = input.value.trim();
                                if(!msg) return;

                                let fd = new FormData();
                                fd.append('action', 'send');
                                fd.append('receiver_id', receiverId);
                                fd.append('message', msg);

                                input.value = ''; // Clear box immediately
                                isAutoScroll = true; // Snap to bottom

                                fetch('chat_handler.php', { method: 'POST', body: fd })
                                .then(() => loadMessages());
                            });
                        });
                    </script>

                <?php else: ?>
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                        <i class="fa fa-paper-plane-o fa-5x mb-3 text-secondary opacity-50"></i>
                        <h4>Welcome to CareerVibe Chat</h4>
                        <p>Select a conversation from the left to start talking.</p>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>