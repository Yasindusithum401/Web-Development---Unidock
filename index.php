<?php require_once __DIR__ . '/includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <title>Unidock</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
        <li><a href="Contact.php">Contact Us</a></li>
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
<!--home main hero text-->
<div class="hero">
    <div class="herotext">
        <section>
            <h1>
                Your Academic <br> Success Starts Here.
            </h1>
        </section>
        <div class="text">
            <p>
                <span id="text"></span>
            </p>
        </div>
        <div class="buttons">
            <div class="getstart">
                <button id="getstartbtn" data-loggedin="<?php echo is_logged_in() ? '1' : '0'; ?>">
                    Get Start 
                </button>
            </div>
            <div class="ExploreResources">
                <button id="ExploreResources">
                    Explore Resources
                </button>
            </div>
        </div>
    </div>
    <aside class="homeimage">
        <img src="images/uphomeimage.png" alt="home image">
    </aside>
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
            <li><a href="Contact.php">Contact Us</a></li>
        </ul>
    </div>
    <div class="about">
        <h3>About</h3>
        <p> Student Resources Hub is a platform designed to help
            students access study materials, organize tasks,
            manage reminders, and discover useful academic
            resources in one convenient place.Our goal is to support students in achieving academic success through simple and effective digital tools.
        </p>
    </div>
    <div class="f0llowus">
        <h3>Follow Us</h3>
        <div class="iconfooter"><!--add karanna  link-->
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
                <form action="auth/login.php" method="post">
                    <div id="loginFormContainer" class="formcontainer active">
                        <h2>Welcome To Unidock</h2>
                        <?php if (isset($_GET['login_error'])): ?>
                            <p style="color:#e60023;text-align:center;margin-bottom:10px;"><?php echo sanitize($_GET['login_error']); ?></p>
                        <?php endif; ?>
                        <input type="hidden" name="redirect" value="index.php">
                        <div class="formgroup">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="formgroup">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" name="login" class="submitbtn">Log In</button>  
                        
                        <p style="margin-top: 15px; font-size: 14px; text-align: center;">
                            Not on Unidock yet? <a href="#" id="switchToSignUp" style="color: #1A80F5; font-weight: bold; text-decoration: none;">Sign up</a>
                        </p>
                    </div>
                </form>
                 <!--sign up section-->
                <form action="auth/register.php" method="post">
                <div id="signupFormContainer" class="formcontainer">
                    <h2>Create Account</h2>
                    <?php if (isset($_GET['signup_error'])): ?>
                        <p style="color:#e60023;text-align:center;margin-bottom:10px;"><?php echo sanitize($_GET['signup_error']); ?></p>
                    <?php endif; ?>
                    <input type="hidden" name="redirect" value="index.php">
                    <div class="formgroup"> 
                        <label for="signupName">Full Name</label>
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
                </div>
                </form>
            </div>
        </div>
    </div>
<script>
<?php if (isset($_GET['login_error']) || isset($_GET['signup_error'])): ?>
window.addEventListener('DOMContentLoaded', function () {
    document.getElementById('modaloverlay').classList.add('active');
    <?php if (isset($_GET['signup_error'])): ?>
    document.getElementById('loginFormContainer').classList.remove('active');
    document.getElementById('signupFormContainer').classList.add('active');
    <?php endif; ?>
});
<?php endif; ?>
</script>
<script src="js/login.js"></script>
</body>
</html>