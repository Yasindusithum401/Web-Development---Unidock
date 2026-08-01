const resourcesData = [
    {
        title: "Web Development HTML Note TEST 1",
        subject: "Web Development",
        description: "Beginner friendly HTML basics note.",
        pdf_url: "assets/pdfs/web-dev-html-note.pdf"
    },
    {
        title: "Human-Computer Interaction Note TEST 2",
        subject: "Web Development",
        description: "HCI principles and design guidelines.",
        pdf_url: "assets/pdfs/hci-note.pdf"
    },
    {
        title: "Mathematics For Technology TEST 3",
        subject: "Mathematics",
        description: "Core math concepts for tech students.",
        pdf_url: "assets/pdfs/math-for-tech.pdf"
    },
    {
        title: "Operating Systems Complete Notes TEST 4",
        subject: "Computer Science",
        description: "Full OS module notes and past paper answers.",
        pdf_url: "assets/pdfs/operating-systems-notes.pdf"
    }
];

let allResources = resourcesData;   // phhp connect karama edit karanna one 
let activeCategory = "all";

const resourcesListEl = document.getElementById("resourcesList");
const categoryListEl = document.getElementById("categoryList");
function getBookmarks() {
    return JSON.parse(localStorage.getItem("unidockBookmarks") || "[]");
}
function saveBookmarks(list) {
    localStorage.setItem("unidockBookmarks", JSON.stringify(list));
}

function showMessage(message) {
    resourcesListEl.innerHTML = `<p style="text-align:center;color:#94a3b8;padding:30px;">${message}</p>`;
}

// Card ekak render karana function eka
function createResourceCard(resource) {
    const bookmarks = getBookmarks();
    const isBookmarked = bookmarks.includes(resource.title);

    const card = document.createElement("div");
    card.className = "resource-card";

    card.innerHTML = `
        <a href="${resource.pdf_url}" target="_blank" class="pdf-icon" title="Open PDF">
            <i class="fa-solid fa-file-pdf"></i>
            <span>PDF</span>
        </a>
        <div class="resource-info">
            <h3>${resource.title}</h3>
            <p class="category-tag">${resource.subject}</p>
            <p class="description">${resource.description}</p>
        </div>
        <button class="bookmark-icon" title="Bookmark" data-title="${resource.title}">
            <i class="fa-${isBookmarked ? "solid" : "regular"} fa-bookmark"></i>
        </button>
    `;
    return card;
}
function renderResources() {
    resourcesListEl.innerHTML = "";

    const filtered = activeCategory === "all"
        ? allResources
        : allResources.filter(r => r.subject === activeCategory);

    if (filtered.length === 0) {
        showMessage("No resources found for this category.");
        return;
    }

    filtered.forEach(resource => {
        resourcesListEl.appendChild(createResourceCard(resource));
    });
}

// Bookmark button (not done 100% yet )
resourcesListEl.addEventListener("click", function (e) {
    const btn = e.target.closest(".bookmark-icon");
    if (!btn) return;

    const title = btn.dataset.title;
    let bookmarks = getBookmarks();

    if (bookmarks.includes(title)) {
        bookmarks = bookmarks.filter(t => t !== title);
    } else {
        bookmarks.push(title);
    }
    saveBookmarks(bookmarks);

    const icon = btn.querySelector("i");
    icon.classList.toggle("fa-solid");
    icon.classList.toggle("fa-regular");
});
//filter eka 
if (categoryListEl) {
    categoryListEl.addEventListener("click", function (e) {
        const link = e.target.closest("a[data-category]");
        if (!link) return;
        e.preventDefault();

        categoryListEl.querySelectorAll("li").forEach(li => li.classList.remove("active"));
        link.closest("li").classList.add("active");

        activeCategory = link.dataset.category;
        renderResources();
    });
}
renderResources();