function requireLoginOrOpen(overlayEl) {
    if (!IS_LOGGED_IN) {
        const modalOverlay = document.getElementById('modaloverlay');
        const loginFormContainer = document.getElementById('loginFormContainer');
        const signupFormContainer = document.getElementById('signupFormContainer');
        if (modalOverlay) {
            modalOverlay.classList.add('active');
            loginFormContainer.classList.add('active');
            signupFormContainer.classList.remove('active');
        }
        return false;
    }
    overlayEl.style.display = "flex";
    return true;
}

// Upload card popup eka
const uploadCard = document.getElementById("uploadCard");
const uploadOverlay = document.getElementById("uploadOverlay");
const closeUpload = document.getElementById("closeUpload");
const pdfInput = document.getElementById("resource");
uploadCard.addEventListener("click", function (e) {
    e.preventDefault();
    requireLoginOrOpen(uploadOverlay);
});
closeUpload.addEventListener("click", function () {
    uploadOverlay.style.display = "none";
});
window.addEventListener("click", function (e) {
    if (e.target === uploadOverlay) {
        uploadOverlay.style.display = "none";
    }
});
if (pdfInput) {
    pdfInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            if (file.type !== "application/pdf") {
                alert("Please upload PDF files only.");
                this.value = "";
            }
        }
    });
}

// Smart Search
const searchCard = document.getElementById("searchCard");
const searchOverlay = document.getElementById("searchOverlay");
const closeSearch = document.getElementById("closeSearch");
const searchInput = document.getElementById("searchInput");
const searchSubject = document.getElementById("searchSubject");
const runSearchBtn = document.getElementById("runSearchBtn");
const searchResults = document.getElementById("searchResults");

function openSearchOverlay() {
    searchOverlay.style.display = "flex";
}
if (searchCard) {
    searchCard.addEventListener("click", function (e) {
        e.preventDefault();
        openSearchOverlay();
    });
}
if (closeSearch) {
    closeSearch.addEventListener("click", () => searchOverlay.style.display = "none");
}
window.addEventListener("click", function (e) {
    if (e.target === searchOverlay) searchOverlay.style.display = "none";
});

async function runSearch() {
    const q = encodeURIComponent(searchInput.value.trim());
    const subject = encodeURIComponent(searchSubject.value);
    searchResults.innerHTML = '<p style="text-align:center;color:#888;">Searching...</p>';
    try {
        const res = await fetch(`search.php?q=${q}&subject=${subject}`);
        const data = await res.json();
        if (!data.success || data.results.length === 0) {
            searchResults.innerHTML = '<p style="text-align:center;color:#888;">No resources found.</p>';
            return;
        }
        searchResults.innerHTML = "";
        data.results.forEach(r => {
            const card = document.createElement("div");
            card.className = "searchResultCard";
            card.innerHTML = `
                <div>
                    <h4>${r.title}</h4>
                    <p>${r.subject}${r.subject_code ? " · " + r.subject_code : ""}</p>
                </div>
                <div class="actions">
                    <a href="${r.file_path}" target="_blank" title="Open PDF"><i class="fa-solid fa-file-pdf"></i></a>
                    ${IS_LOGGED_IN ? `<button class="bookmarkBtn" data-id="${r.id}" title="Bookmark"><i class="fa-${r.bookmarked ? "solid" : "regular"} fa-bookmark"></i></button>` : ""}
                </div>
            `;
            searchResults.appendChild(card);
        });
    } catch (err) {
        searchResults.innerHTML = '<p style="text-align:center;color:#e60023;">Search failed. Please try again.</p>';
    }
}
if (runSearchBtn) {
    runSearchBtn.addEventListener("click", runSearch);
    searchInput.addEventListener("keyup", (e) => { if (e.key === "Enter") runSearch(); });
}
if (searchResults) {
    searchResults.addEventListener("click", async function (e) {
        const btn = e.target.closest(".bookmarkBtn");
        if (!btn) return;
        const resourceId = btn.dataset.id;
        const res = await fetch("bookmark.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `resource_id=${resourceId}`
        });
        const data = await res.json();
        if (data.success) {
            const icon = btn.querySelector("i");
            icon.classList.toggle("fa-solid", data.bookmarked);
            icon.classList.toggle("fa-regular", !data.bookmarked);
        }
    });
}
// get strat eka 
if (typeof AUTO_OPEN_SEARCH !== "undefined" && AUTO_OPEN_SEARCH) {
    window.addEventListener("DOMContentLoaded", openSearchOverlay);
}

// Reminder popup
const reminderCard = document.getElementById("reminderCard");
const reminderOverlay = document.getElementById("reminderOverlay");
const closeReminder = document.getElementById("closeReminder");
const openAddReminder = document.getElementById("openAddReminder");
const addReminderOverlay = document.getElementById("addReminderOverlay");
const closeAddReminder = document.getElementById("closeAddReminder");
const reminderList = document.getElementById("reminderList");

async function loadReminders() {
    if (!IS_LOGGED_IN) return;
    const res = await fetch("reminder.php?action=list");
    const data = await res.json();
    if (!data.success) return;
    if (data.reminders.length === 0) {
        reminderList.innerHTML = '<div class="emptyReminder">No reminders yet.</div>';
        return;
    }
    reminderList.innerHTML = "";
    data.reminders.forEach(r => {
        const card = document.createElement("div");
        card.className = "reminderCard";
        card.innerHTML = `<h3>${r.title}</h3><p>${r.note ? r.note : ""}</p>
            <button class="deleteReminderBtn" data-id="${r.id}" style="border:none;background:none;color:#c00;cursor:pointer;float:right;">Delete</button>`;
        reminderList.appendChild(card);
    });
}
reminderCard.onclick = function (e) {
    e.preventDefault();
    if (requireLoginOrOpen(reminderOverlay)) loadReminders();
};
closeReminder.onclick = function () { reminderOverlay.style.display = "none"; };
openAddReminder.onclick = function () { addReminderOverlay.style.display = "flex"; };
closeAddReminder.onclick = function () { addReminderOverlay.style.display = "none"; };
window.addEventListener("click", function (e) {
    if (e.target === reminderOverlay) reminderOverlay.style.display = "none";
    if (e.target === addReminderOverlay) addReminderOverlay.style.display = "none";
});
const saveReminderBtn = document.getElementById("saveReminder");
if (saveReminderBtn) {
    saveReminderBtn.addEventListener("click", async function () {
        const title = document.getElementById("reminderTitle").value.trim();
        const note = document.getElementById("reminderNote").value.trim();
        if (!title) { alert("Please enter a title."); return; }
        const res = await fetch("reminder.php?action=add", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `title=${encodeURIComponent(title)}&note=${encodeURIComponent(note)}`
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById("reminderTitle").value = "";
            document.getElementById("reminderNote").value = "";
            addReminderOverlay.style.display = "none";
            reminderOverlay.style.display = "flex";
            loadReminders();
        }
    });
}
if (reminderList) {
    reminderList.addEventListener("click", async function (e) {
        const btn = e.target.closest(".deleteReminderBtn");
        if (!btn) return;
        await fetch("reminder.php?action=delete", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${btn.dataset.id}`
        });
        loadReminders();
    });
}

//ToDo list 
const todolistCard = document.getElementById("todolistCard");
const todolistoverlay = document.getElementById("todolistoverlay");
const closetodolist = document.getElementById("closetodolist");
const openAddtodolist = document.getElementById("openAddtodolist");
const addtodolistOverlay = document.getElementById("addtodolistOverlay");
const closeAddtodolist = document.getElementById("closeAddtodolist");
const todoListContainer = document.getElementById("todoList");
const clearAllTodoBtn = document.getElementById("clearAllTodoBtn");

async function loadTodos() {
    if (!IS_LOGGED_IN) return;
    const res = await fetch("todo.php?action=list");
    const data = await res.json();
    if (!data.success) return;
    if (data.todos.length === 0) {
        todoListContainer.innerHTML = '<div class="emptytodolist">No To Do list yet.</div>';
        return;
    }
    todoListContainer.innerHTML = "";
    data.todos.forEach(t => {
        const card = document.createElement("div");
        card.className = "todolistCard" + (Number(t.completed) === 1 ? " completed" : "");
        card.dataset.id = t.id;
        card.innerHTML = `<h3>${t.title}</h3><p>${t.note ? t.note : ""}</p>`;
        todoListContainer.appendChild(card);
    });
}
todolistCard.onclick = function (b) {
    b.preventDefault();
    if (requireLoginOrOpen(todolistoverlay)) loadTodos();
};
closetodolist.onclick = function () { todolistoverlay.style.display = "none"; };
openAddtodolist.onclick = function () { addtodolistOverlay.style.display = "flex"; };
closeAddtodolist.onclick = function () { addtodolistOverlay.style.display = "none"; };
window.addEventListener("click", function (b) {
    if (b.target === todolistoverlay) todolistoverlay.style.display = "none";
    if (b.target === addtodolistOverlay) addtodolistOverlay.style.display = "none";
});
const saveTodoBtn = document.getElementById("savetodolist");
if (saveTodoBtn) {
    saveTodoBtn.addEventListener("click", async function () {
        const title = document.getElementById("todolistTitle").value.trim();
        const note = document.getElementById("TodolistNote").value.trim();
        if (!title) { alert("Please enter a title."); return; }
        const res = await fetch("todo.php?action=add", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `title=${encodeURIComponent(title)}&note=${encodeURIComponent(note)}`
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById("todolistTitle").value = "";
            document.getElementById("TodolistNote").value = "";
            addtodolistOverlay.style.display = "none";
            todolistoverlay.style.display = "flex";
            loadTodos();
        }
    });
}
if (todoListContainer) {
    todoListContainer.addEventListener("click", async function (e) {
        const card = e.target.closest(".todolistCard");
        if (!card) return;
        await fetch("todo.php?action=toggle", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${card.dataset.id}`
        });
        loadTodos();
    });
}
if (clearAllTodoBtn) {
    clearAllTodoBtn.addEventListener("click", async function () {
        if (confirm("Are you sure you want to delete all tasks?")) {
            await fetch("todo.php?action=clear", { method: "POST" });
            loadTodos();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const bookmarkCard = document.getElementById('bookmarkCard');
    const bookmarkOverlay = document.getElementById('bookmarkOverlay');
    const closeBookmark = document.getElementById('closeBookmark');
    const bookmarkList = document.getElementById('bookmarkList');

    // Open Bookmark Modal
    if (bookmarkCard) {
        bookmarkCard.addEventListener('click', (e) => {
            e.preventDefault();
            if (typeof IS_LOGGED_IN !== 'undefined' && !IS_LOGGED_IN) {
                alert('Please log in to view your bookmarks.');
                const loginModal = document.getElementById('modaloverlay');
                if (loginModal) loginModal.classList.add('active');
                return;
            }
            fetchUserBookmarks();
            if (bookmarkOverlay) bookmarkOverlay.classList.add('active');
        });
    }

    // Close Modal
    if (closeBookmark) {
        closeBookmark.addEventListener('click', () => {
            if (bookmarkOverlay) bookmarkOverlay.classList.remove('active');
        });
    }

    // Fetch and Render Bookmarks
    async function fetchUserBookmarks() {
        if (!bookmarkList) return;
        bookmarkList.innerHTML = '<p class="empty-bookmark-msg">Loading bookmarks...</p>';

        try {
            const res = await fetch('bookmark.php?action=list');
            const data = await res.json();

            if (data.success) {
                renderBookmarks(data.bookmarks);
            } else {
                bookmarkList.innerHTML = `<p class="empty-bookmark-msg">${data.message}</p>`;
            }
        } catch (err) {
            console.error('Error fetching bookmarks:', err);
            bookmarkList.innerHTML = '<p class="empty-bookmark-msg">Failed to load bookmarks.</p>';
        }
    }

    function renderBookmarks(bookmarks) {
        if (!bookmarks || bookmarks.length === 0) {
            bookmarkList.innerHTML = '<p class="empty-bookmark-msg">No bookmarks saved yet.</p>';
            return;
        }

        bookmarkList.innerHTML = bookmarks.map(item => `
            <div class="bookmark-card-item" id="bm-item-${item.id}">
                <div class="bookmark-info">
                    <h4>${escapeHTML(item.title)}</h4>
                    <p><strong>Subject:</strong> ${escapeHTML(item.subject)} ${item.subject_code ? `(${escapeHTML(item.subject_code)})` : ''}</p>
                </div>
                <div class="bookmark-actions">
                    <a href="${escapeHTML(item.file_path)}" target="_blank" class="btn-download">
                        <i class="fa-solid fa-download"></i> Download
                    </a>
                    <button type="button" class="btn-remove-bookmark" data-id="${item.id}" title="Remove Bookmark">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');

        bookmarkList.querySelectorAll('.btn-remove-bookmark').forEach(btn => {
            btn.addEventListener('click', function() {
                const resourceId = this.getAttribute('data-id');
                removeBookmark(resourceId);
            });
        });
    }

    // Remove Bookmark 
    async function removeBookmark(resourceId) {
        const formData = new FormData();
        formData.append('resource_id', resourceId);

        try {
            const res = await fetch('bookmark.php?action=toggle', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                const el = document.getElementById(`bm-item-${resourceId}`);
                if (el) el.remove();
                
                if (bookmarkList.children.length === 0) {
                    bookmarkList.innerHTML = '<p class="empty-bookmark-msg">No bookmarks saved yet.</p>';
                }
            } else {
                alert(data.message || 'Error removing bookmark.');
            }
        } catch (err) {
            console.error('Error toggling bookmark:', err);
        }
    }

    function escapeHTML(str) {
        if (!str) return '';
        return str.replace(/[&<>'"]/g, 
            tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
        );
    }
});
