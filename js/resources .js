document.addEventListener('DOMContentLoaded', () => {
    const listContainer = document.getElementById('resourcesList');
    if (!listContainer) return;

    // Resources Render
    function renderResources(items) {
        if (!items || items.length === 0) {
            listContainer.innerHTML = '<p style="text-align:center; width:100%; color:#666; padding: 20px;">No resources found.</p>';
            return;
        }

        listContainer.innerHTML = items.map(res => `
            <div class="resource-card" data-category="${escapeHtml(res.subject)}">
                <div class="resource-info">
                    <h3>${escapeHtml(res.title)}</h3>
                    <p><strong>Subject:</strong> ${escapeHtml(res.subject)} ${res.subject_code ? `(${escapeHtml(res.subject_code)})` : ''}</p>
                    ${res.description ? `<p>${escapeHtml(res.description)}</p>` : ''}
                </div>
                <div class="resource-actions">
                    <a href="${escapeHtml(res.file_path)}" target="_blank" class="btn-download">
                        <i class="fa-solid fa-download"></i> Download
                    </a>
                    <button class="bookmark-btn ${res.bookmarked ? 'active' : ''}" data-id="${res.id}">
                        <i class="fa-solid fa-bookmark"></i>
                    </button>
                </div>
            </div>
        `).join('');

        attachBookmarkEvents();
    }

    // Bookmark Toggle 
    function attachBookmarkEvents() {
        document.querySelectorAll('.bookmark-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (typeof IS_LOGGED_IN !== 'undefined' && !IS_LOGGED_IN) {
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
                        alert(data.message || 'Something went wrong.');
                    }
                } catch (err) {
                    console.error('Error toggling bookmark:', err);
                }
            });
        });
    }

    // slid bar 
    const categoryLinks = document.querySelectorAll('#categoryList a[data-category]');
    categoryLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();

            // Active 
            document.querySelectorAll('#categoryList li').forEach(li => li.classList.remove('active'));
            link.parentElement.classList.add('active');

            const selectedCategory = link.getAttribute('data-category');

            if (typeof RESOURCES_DATA === 'undefined') return;

            if (selectedCategory === 'all') {
                renderResources(RESOURCES_DATA);
            } else {
                const filtered = RESOURCES_DATA.filter(r => 
                    r.subject && r.subject.toLowerCase() === selectedCategory.toLowerCase()
                );
                renderResources(filtered);
            }
        });
    });

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    if (typeof RESOURCES_DATA !== 'undefined') {
        renderResources(RESOURCES_DATA);
    }
});