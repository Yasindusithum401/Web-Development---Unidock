<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/features.css">
    <link rel="stylesheet" href="css/qa.css">
    <link rel="stylesheet" href="css/search.css">
    <title>Features</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>    
.material-symbols-outlined {
  font-variation-settings:
  'FILL' 0,
  'wght' 400,
  'GRAD' 0,
  'opsz' 24
}

</style>   
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
<!--features-->
<section class="featuressection">
    <h2 class="sectiontitle">Platform Features</h2>
    <div class="featurescontainer">
        <!-- Card 1 -->
        <a href="" class="cardlink" id="uploadCard">
        <div class="featurecard">
            <div class="icon blue">
                <span class="material-symbols-outlined">cloud_upload</span>
            </div>
            <div class="featurecontent">
                <h3>Upload Resources</h3>
                <p>Upload your notes, PDF and useful study links.</p>
            </div>
        </div>
        </a>
        <!-- Card 2 -->
        <a href="#" class="cardlink" id="searchCard"> 
        <div class="featurecard">
            <div class="icon green">
                <span class="material-symbols-outlined">search</span>
            </div>
            <div class="featurecontent">
                <h3>Smart Search</h3>
                <p>Find resources quickly using advanced search.</p>
            </div>
        </div>
        </a>
        <!-- Card 3 -->
        <a href="" class="cardlink" id="todolistCard">
        <div class="featurecard">
            <div class="icon purple">
                <span class="material-symbols-outlined">checklist</span>
            </div>
            <div class="featurecontent">
                <h3>To Do List</h3>
                <p>Your personal space to plan and complete daily tasks.</p>
            </div>
        </div>
        </a>
        <!-- Card 4 -->
        <a href="#" class="cardlink" id="bookmarkCard">
        <div class="featurecard">
            <div class="icon red">
                <span class="material-symbols-outlined">bookmark</span>
            </div>
            <div class="featurecontent">
                <h3>Bookmark & Save</h3>
                <p>Save your favorite resources and access them anytime.</p>
            </div>
        </div>
        </a>
        <!-- Card 5 -->
        <a href="#" class="cardlink" id="qaCard">
        <div class="featurecard">
            <div class="icon lightgreen">
                <span class="material-symbols-outlined">forum</span>
            </div>
            <div class="featurecontent">
                <h3>Ask Questions</h3>
                <p>Get together with friends to sort out problems.</p>
            </div>
        </div>
        </a>
        <!-- Card 6 -->
        <a href="" class="cardlink" id="reminderCard" >
        <div class="featurecard">
            <div class="icon darkblue">
                <span class="material-symbols-outlined">notifications_active</span>
            </div>
            <div class="featurecontent">
                <h3>Reminder</h3>
                <p>Save important jobs and receive reminders.</p>
            </div>
        </div>
        </a>
    </div>
</section>
<!--footer section-->
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
<!--features login-->
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
<!--Upload Resource-->
<div class="uploadOverlay" id="uploadOverlay">
    <div class="uploadContainer">
        <button type="button" class="uploadClose" id="closeUpload">
            &times;
        </button>
        <h2>Upload Study Resource</h2>
        <?php if (isset($_GET['upload_error'])): ?>
            <p style="color:#e60023;text-align:center;margin-bottom:15px;"><?php echo sanitize($_GET['upload_error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['upload_success'])): ?>
            <p style="color:#2878d7;text-align:center;margin-bottom:15px;">Resource uploaded successfully!</p>
        <?php endif; ?>
        <form action="upload.php" method="POST" enctype="multipart/form-data"><!--php file eka danna e wage pdf eka varify kara ganna remmember -->
            <div class="uploadGroup">
                <label for="title">Resource Title</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Enter Resource Title"
                    required>
            </div>
            <div class="uploadGroup">
                <label for="subject">Subject</label>
                <select id="subject" name="subject" required>
                    <option value="" disabled selected>Select Subject</option>
                    <option value="Web Development">Web Development</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Physics">Physics</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Art">Art</option>
                    <option value="Chemistry">Chemistry</option>
                    <option value="Business Studies">Business Studies</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Languages">Languages</option>
                    <option value="Program Designing and Programming">Program Designing and Programming</option>
                </select>
            </div>
            <div class="uploadGroup">
                <label for="subjectCode">Subject Code</label>
                <input
                    type="text"
                    id="subjectCode"
                    name="subjectCode"
                    placeholder="Example : ICT1213"
                    required>
            </div>
            <div class="uploadGroup">
                <label for="resource">Upload PDF</label>
                <input
                    type="file"
                    id="resource"
                    name="resource"
                    accept=".pdf,application/pdf"
                    required>
                <small>Only PDF files are allowed.</small>
            </div>
            <button type="submit" class="uploadBtn">Upload PDF</button>
        </form>
    </div>
</div>
<!--Advanced Search-->
<div class="uploadOverlay" id="searchOverlay">
    <div class="uploadContainer" style="width:820px;">
        <button type="button" class="uploadClose" id="closeSearch">&times;</button>
        <h2 style="color:#33c200;">Advanced Search</h2>
        <div class="searchBar">
            <input type="text" id="searchInput" placeholder="Search by title, subject or subject code...">
            <button type="button" id="runSearchBtn">Search</button>
        </div>
        <div id="searchResults"><p style="text-align:center;color:#888;">Type something and hit Search.</p></div>
    </div>
</div>
<!--reminder eka-->
<div class="reminderOverlay" id="reminderOverlay">
    <div class="reminderContainer">
        <button 
        type="button" 
        class="reminderClose"
        id="closeReminder">
        &times;
        </button>
        <h2> My Reminders</h2>
        <div id="reminderList">
            <div class="emptyReminder">No reminders yet.</div>
        </div>
        <button 
        class="addReminderBtn"
        id="openAddReminder">
        + Add Reminder
        </button>
    </div>
</div>
<div class="addReminderOverlay" id="addReminderOverlay">
    <div class="addReminderContainer">
        <button 
        class="reminderClose"
        id="closeAddReminder">
        &times;
        </button>
        <h2> Add New Reminder</h2>
        <div class="uploadGroup">
            <label>Title</label>
            <input 
            type="text"
            id="reminderTitle"
            placeholder="Enter reminder title">
        </div>
        <div class="uploadGroup">
            <label>Note </label>
            <textarea 
            id="reminderNote"
            placeholder="Write your note">
            </textarea>
        </div>
        <button 
        class="uploadBtn"
        id="saveReminder">
        Save Reminder
        </button>
    </div>
</div>
<!--To do list eka -->
<div class="todolistoverlay" id="todolistoverlay">
    <div class="todolistContainer">
        <button 
        type="button" 
        class="todolistClose"
        id="closetodolist">
        &times;
        </button>
        <h2>My To Do List</h2>
        <button id="clearAllTodoBtn" class="clearAllBtn">Clear All Tasks</button>
        <div id="todoList">
            <div class="emptytodolist">No To Do list yet.</div>
        </div>
        <button 
        class="addtodolistBtn"
        id="openAddtodolist">
        + Add To Do list
        </button>
    </div>
</div>
<div class="addtodolisOverlay" id="addtodolistOverlay">
    <div class="addtodolistContainer">
        <button 
        class="todolistClose"
        id="closeAddtodolist">
        &times;
        </button>
        <h2> Add New To Do List</h2>
        <div class="uploadGroup2">
            <label>Title</label>
            <input 
            type="text"
            id="todolistTitle"
            placeholder="Enter To Do title">
        </div>
        <div class="uploadGroup2">
            <label>Note </label>
            <textarea 
            id="TodolistNote"
            placeholder="Write your note">
            </textarea>
        </div>
        <button 
        class="uploadBtn2"
        id="savetodolist">
        Save TO DO list
        </button>
    </div>
</div>

<div class="qaOverlay" id="qaOverlay">
    <div class="qaApp" id="qaApp">
        <!-- Chat list -->
        <div class="qaSidebar">
            <div class="qaSidebarHeader">
                <h2><i class="fa-solid fa-comments"></i> Ask Questions</h2>
                <button type="button" class="qaCloseBtn" id="closeQA">&times;</button>
            </div>
            <div id="qaChatList" class="qaChatList">
                <p class="qaEmptyState">Loading questions...</p>
            </div>
            <button type="button" class="qaNewChatBtn" id="openAskQuestion" title="Ask a question">
                <i class="fa-solid fa-pen"></i>
            </button>
        </div>

        <!-- Chat thread -->
        <div class="qaThread">
            <div class="qaThreadEmpty" id="qaThreadEmpty">
                <i class="fa-solid fa-comments"></i>
                <p>Select a question to start chatting</p>
            </div>
            <div class="qaThreadActive" id="qaThreadActive" style="display:none;">
                <div class="qaThreadHeader">
                    <button type="button" class="qaBackBtn" id="qaBackBtn"><i class="fa-solid fa-arrow-left"></i></button>
                    <div class="qaAvatar" id="qaThreadAvatar">Q</div>
                    <div class="qaThreadInfo">
                        <h3 id="qaThreadTitle"></h3>
                        <span id="qaThreadAuthor"></span>
                    </div>
                    <button type="button" class="qaDeleteBtn" id="qaDeleteQuestionBtn" title="Delete question" style="display:none;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
                <div class="qaMessages" id="qaMessages"></div>
                <form class="qaComposer" id="qaComposer">
                    <textarea id="qaReplyInput" placeholder="Type a reply..." rows="1" required></textarea>
                    <button type="submit" class="qaSendBtn" id="qaSendBtn"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ask a Question -->
<div class="qaOverlay" id="askQuestionOverlay">
    <div class="qaNewChatContainer">
        <div class="qaNewChatHeader">
            <h2>New Question</h2>
            <button type="button" class="qaCloseBtn" id="closeAskQuestion">&times;</button>
        </div>
        <div class="qaNewChatBody">
            <p class="qaFormError" id="qaFormError"></p>
            <div class="uploadGroup">
                <label for="qaTitle">Title</label>
                <input type="text" id="qaTitle" placeholder="What's your question about?">
            </div>
            <div class="uploadGroup">
                <label for="qaBody">Details</label>
                <textarea id="qaBody" placeholder="Explain your problem so others can help..." rows="5"></textarea>
            </div>
            <button type="button" class="uploadBtn" id="submitQuestion">Post Question</button>
        </div>
    </div>
</div>

<!-- Bookmark Overlay -->
<div class="uploadOverlay" id="bookmarkOverlay">
    <div class="uploadContainer" style="width: 600px;">
        <button type="button" class="uploadClose" id="closeBookmark">&times;</button>
        <h2 style="color: #ff4d4f;"><i class="fa-solid fa-bookmark"></i> My Bookmarks</h2>
        <div id="bookmarkList">
            <p class="empty-bookmark-msg">Loading bookmarks...</p>
        </div>
    </div>
</div>
<script>
    const IS_LOGGED_IN = <?php echo is_logged_in() ? 'true' : 'false'; ?>;
    const CURRENT_USER_ID = <?php echo is_logged_in() ? (int) current_user_id() : 0; ?>;
    const AUTO_OPEN_SEARCH = <?php echo (isset($_GET['search']) && $_GET['search'] == '1') ? 'true' : 'false'; ?>;
</script>
<script src="js/login.js"></script>
<script src="js/features.js"></script>
<script src="js/qa.js"></script>
<script src="js/search.js"></script>
</body>
</html>