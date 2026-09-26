<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $fullName = sanitize($_POST['fullName'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $subject  = sanitize($_POST['subject'] ?? '');
    $body     = trim($_POST['message'] ?? '');

    if ($fullName === '' || $email === '' || $body === '') {
        header('Location: contact.php?contact_error=' . urlencode('Please fill in your name, email, and message.'));
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: contact.php?contact_error=' . urlencode('Please enter a valid email address.'));
        exit;
    }

    $userId = is_logged_in() ? current_user_id() : null;

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO messages (user_id, full_name, email, subject, body) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $fullName, $email, $subject, $body]);
    } catch (PDOException $e) {
        header('Location: contact.php?contact_error=' . urlencode('Something went wrong. Please try again.'));
        exit;
    }

    header('Location: contact.php?contact_success=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/contact.css">
</head>
<body>
<!--navigation bar-->
<nav class="navbar">
    <div class="logo">
        <img src="images/logo.png" alt="logo image">
    </div>
    <div class="menutoggle" id="menuToggle">
        <i class="fa-solid fa-bars"></i>
    </div>
    <ul class="navtype" id="navMenu">
        <li><a href="index.php">Home</a></li>
        <li><a href="features.php">Features</a></li>
        <li><a href="resources.php">Resources</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact US</a></li>
        <li class="mobilelogin">
            <?php if (is_logged_in()): ?>
                <a href="auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="#" id="openModalBtn2">Login</a>
            <?php endif; ?>
        </li>
    </ul>
    <?php if (is_logged_in()): ?>
        <a href="auth/logout.php" class="loginbtn">Logout</a>
    <?php else: ?>
        <a href="#" class="loginbtn" id="openModalBtn">Login</a>
    <?php endif; ?>
</nav>

<div class="about2">
    <h1>Contact Us</h1>
</div>

<div class="p">
    <p>Got a question, suggestion, or found an issue with a resource? Send us a message below and we'll get back to you.</p>
</div>

<div class="contact-wrap">
    <div class="contact-form-box">

        <?php if (isset($_GET['contact_success'])): ?>
            <div class="contact-alert contact-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                Thanks! Your message has been sent successfully. We'll get back to you soon.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['contact_error'])): ?>
            <div class="contact-alert contact-alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo sanitize($_GET['contact_error']); ?>
            </div>
        <?php endif; ?>

        <form action="contact.php" method="post" class="contact-form">
            <div class="formgroup">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" placeholder="Your full name"
                       value="<?php echo isset($_POST['fullName']) ? sanitize($_POST['fullName']) : ''; ?>" required>
            </div>

            <div class="formgroup">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="you@example.com"
                       value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>" required>
            </div>

            <div class="formgroup">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" placeholder="What's this about? (optional)"
                       value="<?php echo isset($_POST['subject']) ? sanitize($_POST['subject']) : ''; ?>">
            </div>

            <div class="formgroup">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="6" placeholder="Write your query or message here..." required><?php echo isset($_POST['message']) ? sanitize($_POST['message']) : ''; ?></textarea>
            </div>

            <button type="submit" name="send_message" class="submitbtn">Send Message</button>
        </form>
    </div>
</div>

<!--footer section eka -->
<footer class="footer">
    <div class="footerlogo">
        <img src="images/logo.png" alt="logo image">
        <p> A Platform For Students To Share, Discover and Learn Together. </p>
    </div>
    <div class="link">
        <h3>
            Quick Links
        </h3>
        <ul class="qlink">
            <li><a href="index.php">Home</a></li>
            <li><a href="features.php">Features</a></li>
            <li><a href="resources.php">Resources</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
    </div>
    <div class="about">
        <h3>About</h3>
        <p> Student Resources Hub is a platform designed to help
            students access study materials, organize tasks,
            manage reminders, and discover useful academic
            resources in one convenient place. Our goal is to support students in achieving academic success through simple and effective digital tools.
        </p>
    </div>
    <div class="f0llowus">
        <h3>Follow Us</h3>
        <div class="iconfooter">
            <a href="#" target="_blank"><i class="fa-brands fa-github"></i></a>
            <a href="#" target="_blank"><i class="fa-brands fa-threads"></i></a>
            <a href="#" target="_blank"><i class="fa-brands fa-linkedin"></i></a>
            <a href="#" target="_blank"><i class="fa-solid fa-x"></i></a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 UniDock. All Rights Reserved.</p>
    </div>
</footer>

<!--login section-->
<div class="modaloverlay" id="modaloverlay">
    <div class="modalcontainer">
        <button type="button" class="closebtn" id="closeModalBtn">&times;</button>
        <div class="modalleft">

            <form id="loginFormContainer" class="formcontainer active" action="auth/login.php" method="post">
                <h2>Welcome To Unidock</h2>
                <?php if (isset($_GET['login_error'])): ?>
                    <p style="color:#e60023;text-align:center;margin-bottom:10px;"><?php echo sanitize($_GET['login_error']); ?></p>
                <?php endif; ?>
                <input type="hidden" name="redirect" value="contact.php">
                <div class="formgroup">
                    <label for="email">Email</label>
                    <input type="email" id="loginEmail" name="email" placeholder="Email" required>
                </div>
                <div class="formgroup">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="submitbtn">Log In</button>

                <p style="margin-top: 15px; font-size: 14px; text-align: center;">
                    Not on Unidock yet? <a href="#" id="switchToSignUp" style="color: #1A80F5; font-weight: bold; text-decoration: none;">Sign up</a>
                </p>
            </form>

            <!--sign up section-->
            <form id="signupFormContainer" class="formcontainer" action="auth/register.php" method="post">
                <h2>Create Account</h2>
                <?php if (isset($_GET['signup_error'])): ?>
                    <p style="color:#e60023;text-align:center;margin-bottom:10px;"><?php echo sanitize($_GET['signup_error']); ?></p>
                <?php endif; ?>
                <input type="hidden" name="redirect" value="contact.php">
                <div class="formgroup"> <label for="signupName">Full Name</label>
                    <input type="text" id="signupName" name="signupname" placeholder="Full Name" required>
                </div>
                <div class="formgroup">
                    <label for="signupEmail">Email</label>
                    <input type="email" id="signupEmail" name="signupEmail" placeholder="Email" required>
                </div>
                <div class="formgroup">
                    <label for="signupPassword">Password</label>
                    <input type="password" id="signupPassword" name="signupPassword" placeholder="Create Password" required>
                </div>
                <button type="submit" name="signup" class="submitbtn" style="background-color: #0076d3;">Sign up</button>

                <p style="margin-top: 15px; font-size: 14px; text-align: center;">
                    Already have an account? <a href="#" id="switchToLogIn" style="color: #1A80F5; font-weight: bold; text-decoration: none;">Log in</a>
                </p>
            </form>
        </div>
    </div>
</div>

<?php if (isset($_GET['contact_success'])): ?>
<script>
    setTimeout(function () {
        var alertBox = document.querySelector('.contact-alert-success');
        if (alertBox) alertBox.style.display = 'none';
    }, 6000);
</script>
<?php endif; ?>

<script src="js/login.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>