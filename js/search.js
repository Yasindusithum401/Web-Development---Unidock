(function () {
    'use strict';
    var LOGGED_IN = typeof IS_LOGGED_IN !== 'undefined' ? IS_LOGGED_IN : false;
    var searchCard    = document.getElementById('searchCard');
    var searchOverlay = document.getElementById('searchOverlay');
    var closeSearch   = document.getElementById('closeSearch');
    var searchInput   = document.getElementById('searchInput');
    var runSearchBtn  = document.getElementById('runSearchBtn');
    var searchResults = document.getElementById('searchResults');
    var modalOverlay  = document.getElementById('modaloverlay');

    if (!searchOverlay) return;

    function openSearchOverlay() {
        searchOverlay.classList.add('active');
        if (searchInput) searchInput.focus();
    }

    function closeSearchOverlay() {
        searchOverlay.classList.remove('active');
    }

    if (searchCard) {
        searchCard.addEventListener('click', function (e) {
            e.preventDefault();
            openSearchOverlay();
        });
    }
    if (closeSearch) closeSearch.addEventListener('click', closeSearchOverlay);
    searchOverlay.addEventListener('click', function (e) {
        if (e.target === searchOverlay) closeSearchOverlay();
    });

    function el(tag, className) {
        var e = document.createElement(tag);
        if (className) e.className = className;
        return e;
    }

    function renderResults(results) {
        searchResults.innerHTML = '';

        if (!results || results.length === 0) {
            searchResults.innerHTML = '<p style="text-align:center;color:#888;">No resources matched your search.</p>';
            return;
        }

        results.forEach(function (r) {
            var card = el('div', 'search-result-card');

            var info = el('div', 'search-result-info');
            var h4 = el('h4');
            h4.textContent = r.title;
            var subj = el('p');
            subj.textContent = 'Subject: ' + r.subject + (r.subject_code ? ' (' + r.subject_code + ')' : '');
            info.appendChild(h4);
            info.appendChild(subj);
            if (r.description) {
                var desc = el('p');
                desc.textContent = r.description;
                info.appendChild(desc);
            }

            var actions = el('div', 'search-result-actions');

            var viewLink = el('a', 'btn-view');
            viewLink.href = r.file_path;
            viewLink.target = '_blank';
            viewLink.innerHTML = '<i class="fa-solid fa-eye"></i> View';

            var downloadLink = el('a', 'btn-download');
            downloadLink.href = r.file_path;
            downloadLink.setAttribute('download', '');
            downloadLink.innerHTML = '<i class="fa-solid fa-download"></i> Download';

            var bookmarkBtn = el('button', 'bookmark-btn' + (r.bookmarked ? ' active' : ''));
            bookmarkBtn.type = 'button';
            bookmarkBtn.dataset.id = r.id;
            bookmarkBtn.innerHTML = '<i class="fa-solid fa-bookmark"></i>';
            bookmarkBtn.addEventListener('click', function () {
                toggleBookmark(r.id, bookmarkBtn);
            });

            actions.appendChild(viewLink);
            actions.appendChild(downloadLink);
            actions.appendChild(bookmarkBtn);

            card.appendChild(info);
            card.appendChild(actions);
            searchResults.appendChild(card);
        });
    }

    function toggleBookmark(id, btn) {
        if (!LOGGED_IN) {
            alert('Please log in to bookmark resources.');
            if (modalOverlay) modalOverlay.classList.add('active');
            return;
        }
        var fd = new FormData();
        fd.append('resource_id', id);
        fetch('bookmark.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    btn.classList.toggle('active', data.bookmarked);
                } else {
                    alert(data.message || 'Could not update bookmark.');
                }
            })
            .catch(function () {
                alert('Network error — please try again.');
            });
    }

    function runSearch() {
        var q = (searchInput.value || '').trim();
        if (!q) {
            searchResults.innerHTML = '<p style="text-align:center;color:#888;">Type something to search.</p>';
            return;
        }
        searchResults.innerHTML = '<p style="text-align:center;color:#888;">Searching...</p>';

        fetch('search.php?q=' + encodeURIComponent(q))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    renderResults(data.results);
                } else {
                    searchResults.innerHTML = '<p style="text-align:center;color:#888;">' +
                        (data.message || 'Search failed.') + '</p>';
                }
            })
            .catch(function () {
                searchResults.innerHTML = '<p style="text-align:center;color:#888;">Network error — please try again.</p>';
            });
    }

    if (runSearchBtn) runSearchBtn.addEventListener('click', runSearch);
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                runSearch();
            }
        });
    }

    if (typeof AUTO_OPEN_SEARCH !== 'undefined' && AUTO_OPEN_SEARCH) {
        openSearchOverlay();
    }
})();