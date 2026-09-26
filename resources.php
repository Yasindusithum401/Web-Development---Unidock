<?php 
require_once __DIR__ . '/includes/functions.php'; 
require_once __DIR__ . '/includes/db.php';
require_login();
$limit = 3; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$category = isset($_GET['category']) ? trim($_GET['category']) : 'all';

if ($category !== 'all' && $category !== '') {
    $countSql = "SELECT COUNT(*) FROM resources WHERE subject = ?";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([$category]);
} else {
    $countSql = "SELECT COUNT(*) FROM resources";
    $countStmt = $pdo->query($countSql);
}
$totalResources = $countStmt->fetchColumn();
$totalPages = (int) ceil($totalResources / $limit);
if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

// Fetch resources 
if ($category !== 'all' && $category !== '') {
    $sql = "SELECT id, title, subject, subject_code, description, file_path 
            FROM resources WHERE subject = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $category, PDO::PARAM_STR);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $sql = "SELECT id, title, subject, subject_code, description, file_path 
            FROM resources ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
}
$resources = $stmt->fetchAll();

// Bookmarks check
$bookmarkedIds = [];
if (is_logged_in()) {
    $b = $pdo->prepare('SELECT resource_id FROM bookmarks WHERE user_id = ?');
    $b->execute([current_user_id()]);
    $bookmarkedIds = array_column($b->fetchAll(), 'resource_id');
}
foreach ($resources as &$r) {
    $r['bookmarked'] = in_array($r['id'], $bookmarkedIds);
}
unset($r);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/resources.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
</head>
<body>
<!-- Navigation Bar -->
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
        <li><a href="resources.php" class="active">Resources</a></li>
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

<header class="resource-banner">
    <div class="banner-content">
        <h1>Explore Resources</h1>
        <p>Find the best study materials shared by students.</p>
    </div>
    <div class="banner-image">
        <img src="images/resources image.png" alt="Resources Illustration">
    </div>
</header>

<main class="resources-container">
    <div class="content-wrapper">
        <aside class="categories-sidebar">
            <h3>Categories</h3>
            <?php
            $mainCategories = ['Web Development', 'Computer Science', 'Mathematics', 'Physics', 'Engineering'];
            $extraCategories = ['Art', 'Chemistry', 'Business Studies', 'Software Engineering', 'Languages', 'Program Designing and Programming'];
            ?>
            <ul class="category-list" id="categoryList">
                <li class="<?php echo $category === 'all' ? 'active' : ''; ?>"><a href="resources.php?category=all">All Categories</a></li>
                <?php foreach ($mainCategories as $cat): ?>
                    <li class="<?php echo $category === $cat ? 'active' : ''; ?>"><a href="resources.php?category=<?php echo urlencode($cat); ?>"><?php echo sanitize($cat); ?></a></li>
                <?php endforeach; ?>
                <li>
                    <ul class="extra-categories" id="extraCategories" style="display:none;">
                        <?php foreach ($extraCategories as $cat): ?>
                            <li class="<?php echo $category === $cat ? 'active' : ''; ?>"><a href="resources.php?category=<?php echo urlencode($cat); ?>"><?php echo sanitize($cat); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="more-btn" id="moreCategoriesBtn">
                    More <i class="fa-solid fa-chevron-down"></i>
                </li>
            </ul>
        </aside>

        <section class="resources-list-section">
            <h2>All Resources</h2>
            <div class="resources-list" id="resourcesList">
                <?php if (empty($resources)): ?>
                    <p style="text-align:center; width:100%; color:#666; padding: 30px;">No resources available yet.</p>
                <?php else: ?>
                    <?php foreach ($resources as $res): ?>
                        <div class="resource-card" data-category="<?php echo sanitize($res['subject']); ?>">
                            <div class="resource-info">
                                <h3><?php echo sanitize($res['title']); ?></h3>
                                <p><strong>Subject:</strong> <?php echo sanitize($res['subject']); ?> <?php echo !empty($res['subject_code']) ? '(' . sanitize($res['subject_code']) . ')' : ''; ?></p>
                                <?php if (!empty($res['description'])): ?>
                                    <p><?php echo sanitize($res['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="resource-actions">
                                <!--View Button-->
                                <a href="<?php echo sanitize($res['file_path']); ?>" target="_blank" class="btn-view" style="background-color: #1A80F5; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; margin-right: 5px; font-size: 14px;">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <!--Download Button-->
                                <a href="<?php echo sanitize($res['file_path']); ?>" download class="btn-download">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                                <!--Bookmark Button-->
                                <button class="bookmark-btn <?php echo !empty($res['bookmarked']) ? 'active' : ''; ?>" data-id="<?php echo $res['id']; ?>">
                                    <i class="fa-solid fa-bookmark"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination-container">
                    <?php if ($page > 1): ?>
                        <a href="resources.php?page=<?php echo $page - 1; ?>&category=<?php echo urlencode($category); ?>"><i class="fa-solid fa-chevron-left"></i> Prev</a>
                    <?php else: ?>
                        <span class="disabled"><i class="fa-solid fa-chevron-left"></i> Prev</span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active-page"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="resources.php?page=<?php echo $i; ?>&category=<?php echo urlencode($category); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="resources.php?page=<?php echo $page + 1; ?>&category=<?php echo urlencode($category); ?>">Next <i class="fa-solid fa-chevron-right"></i></a>
                    <?php else: ?>
                        <span class="disabled">Next <i class="fa-solid fa-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="footerlogo">
        <img src="images/logo.png" alt="logo image">
        <p>A Platform For Students To Share, Discover and Learn Together.</p>
    </div>
    <div class="link">
        <h3>Quick Links</h3>
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
        <p>Student Resources Hub is a platform designed to help students access study materials, organize tasks, manage reminders, and discover useful academic resources in one convenient place.</p>
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

<!-- Login Modal -->
<div class="modaloverlay" id="modaloverlay">
    <div class="modalcontainer">
        <button type="button" class="closebtn" id="closeModalBtn">&times;</button>
        <div class="modalleft">
            <form id="loginFormContainer" class="formcontainer active" action="auth/login.php" method="post">
                <h2>Welcome To Unidock</h2>
                <?php if (isset($_GET['login_error'])): ?>
                    <p style="color:#e60023;text-align:center;margin-bottom:10px;"><?php echo sanitize($_GET['login_error']); ?></p>
                <?php endif; ?>
                <input type="hidden" name="redirect" value="resources.php">
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
            </form>

            <form id="signupFormContainer" class="formcontainer" action="auth/register.php" method="post">
                <h2>Create Account</h2>
                <?php if (isset($_GET['signup_error'])): ?>
                    <p style="color:#e60023;text-align:center;margin-bottom:10px;"><?php echo sanitize($_GET['signup_error']); ?></p>
                <?php endif; ?>
                <input type="hidden" name="redirect" value="resources.php">
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
            </form>
        </div>
    </div>
</div>

<script>
const IS_LOGGED_IN = <?php echo is_logged_in() ? 'true' : 'false'; ?>;

// More Categories Toggle
const moreBtn = document.getElementById('moreCategoriesBtn');
const extraCategories = document.getElementById('extraCategories');
if (moreBtn && extraCategories) {
    moreBtn.addEventListener('click', function() {
        if (extraCategories.style.display === "block") {
            extraCategories.style.display = "none";
            moreBtn.innerHTML = 'More <i class="fa-solid fa-chevron-down"></i>';
        } else {
            extraCategories.style.display = "block";
            moreBtn.innerHTML = 'Less <i class="fa-solid fa-chevron-up"></i>';
        }
    });
}

// Bookmark Toggle Logic
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.bookmark-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!IS_LOGGED_IN) {
                alert('Please log in to bookmark resources.');
                const modal = document.getElementById('modaloverlay');
                if (modal) modal.classList.add('active');
                return;
            }

            const resourceId = this.getAttribute('data-id');
            const formData = new FormData();
            formData.append('resource_id', resourceId);

            try {
                const response = await fetch('bookmark.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    this.classList.toggle('active', data.bookmarked);
                } else {
                    alert(data.message || 'Error updating bookmark.');
                }
            } catch (err) {
                console.error('Bookmark error:', err);
            }
        });
    });
});
</script>
<script src="js/login.js"></script>
</body>
</html>