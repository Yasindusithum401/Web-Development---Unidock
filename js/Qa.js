(function () {
    'use strict';

    const QA_ENDPOINT = 'Qa.php';
    const POLL_INTERVAL_MS = 5000;

    const qaCard        = document.getElementById('qaCard');
    const qaOverlay      = document.getElementById('qaOverlay');
    const closeQA        = document.getElementById('closeQA');
    const qaFeed         = document.getElementById('qaFeed');
    const qaFormError    = document.getElementById('qaFormError');
    const qaPostForm     = document.getElementById('qaPostForm');
    const qaPostBody     = document.getElementById('qaPostBody');
    const qaSendBtn      = document.getElementById('qaSendBtn');

    if (!qaOverlay || !qaFeed || !qaPostForm) {
        return;
    }

    const isLoggedIn = typeof IS_LOGGED_IN !== 'undefined' && IS_LOGGED_IN;
    const currentUserId = typeof CURRENT_USER_ID !== 'undefined' ? Number(CURRENT_USER_ID) : 0;

    let pollTimer = null;
    const threadState = {};

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : String(str);
        return div.innerHTML;
    }

    function timeAgo(dateStr) {
        const then = new Date(dateStr.replace(' ', 'T'));
        if (isNaN(then.getTime())) return dateStr;
        const diffSec = Math.floor((Date.now() - then.getTime()) / 1000);
        if (diffSec < 5) return 'just now';
        if (diffSec < 60) return diffSec + 's ago';
        const diffMin = Math.floor(diffSec / 60);
        if (diffMin < 60) return diffMin + 'm ago';
        const diffHr = Math.floor(diffMin / 60);
        if (diffHr < 24) return diffHr + 'h ago';
        const diffDay = Math.floor(diffHr / 24);
        if (diffDay < 7) return diffDay + 'd ago';
        return then.toLocaleDateString();
    }

    function autoGrow(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    function setFormError(msg) {
        qaFormError.textContent = msg || '';
    }

    async function apiCall(action, { method = 'GET', params = null, body = null } = {}) {
        let url = QA_ENDPOINT + '?action=' + encodeURIComponent(action);
        const opts = { method, headers: {} };

        if (method === 'GET' && params) {
            const qs = new URLSearchParams(params).toString();
            if (qs) url += '&' + qs;
        }
        if (method === 'POST') {
            const form = new URLSearchParams(body || {});
            opts.body = form;
            opts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        const res = await fetch(url, opts);
        let data;
        try {
            data = await res.json();
        } catch (e) {
            throw new Error('Server returned an invalid response.');
        }
        return data;
    }

    function renderFeed(questions) {
        if (!questions || questions.length === 0) {
            qaFeed.innerHTML = '<p class="qaEmptyState">No messages yet. Start the conversation!</p>';
            return;
        }

        const frag = document.createDocumentFragment();
        questions.forEach((q) => {
            frag.appendChild(renderMessage(q));
        });
        qaFeed.innerHTML = '';
        qaFeed.appendChild(frag);
    }

    function renderMessage(q) {
        const isOwner = isLoggedIn && Number(q.user_id) === currentUserId;
        const state = threadState[q.id] || { open: false, loaded: false };
        threadState[q.id] = state;

        const wrap = document.createElement('div');
        wrap.className = 'qaMsg';
        wrap.dataset.questionId = q.id;

        wrap.innerHTML = `
            <div class="qaMsgHead">
                <span class="qaAuthor">${escapeHtml(q.author)}</span>
                <span class="qaTime">${escapeHtml(timeAgo(q.created_at))}</span>
            </div>
            <div class="qaMsgBody"></div>
            <div class="qaMsgFooter">
                <button type="button" class="qaReplyToggle" data-action="toggle-replies">
                    <i class="fa-regular fa-comment-dots"></i> ${Number(q.reply_count) || 0} repl${Number(q.reply_count) === 1 ? 'y' : 'ies'}
                </button>
                ${isOwner ? '<button type="button" class="qaDeleteBtn" data-action="delete-question"><i class="fa-solid fa-trash"></i> Delete</button>' : ''}
            </div>
            <div class="qaRepliesBox${state.open ? ' open' : ''}">
                <div class="qaRepliesList"></div>
                ${isLoggedIn
                    ? `<form class="qaReplyForm" data-action="reply-form">
                           <textarea placeholder="Write a reply..." rows="1" required></textarea>
                           <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                       </form>`
                    : '<p class="qaLoginNotice">Log in to reply.</p>'}
            </div>
        `;

        wrap.querySelector('.qaMsgBody').textContent = q.body;

        if (state.open) {
            loadReplies(q.id, wrap.querySelector('.qaRepliesList'));
        }

        return wrap;
    }

    function renderReplies(container, replies) {
        if (!replies || replies.length === 0) {
            container.innerHTML = '<p class="qaEmptyState" style="margin:0;">No replies yet.</p>';
            return;
        }
        container.innerHTML = '';
        replies.forEach((r) => {
            const isOwner = isLoggedIn && Number(r.user_id) === currentUserId;
            const el = document.createElement('div');
            el.className = 'qaReply';
            el.dataset.replyId = r.id;
            el.innerHTML = `
                <div class="qaMsgHead">
                    <span class="qaAuthor">${escapeHtml(r.author)}</span>
                    <span class="qaTime">${escapeHtml(timeAgo(r.created_at))}</span>
                </div>
                <div class="qaMsgBody"></div>
                ${isOwner ? '<button type="button" class="qaDeleteBtn" data-action="delete-reply"><i class="fa-solid fa-trash"></i> Delete</button>' : ''}
            `;
            el.querySelector('.qaMsgBody').textContent = r.body;
            container.appendChild(el);
        });
    }

    async function loadFeed() {
        try {
            const data = await apiCall('list');
            if (data.success) {
                renderFeed(data.questions);
            } else {
                qaFeed.innerHTML = '<p class="qaEmptyState">' + escapeHtml(data.message || 'Could not load messages.') + '</p>';
            }
        } catch (e) {
            qaFeed.innerHTML = '<p class="qaEmptyState">Could not reach the server. Please try again.</p>';
        }
    }

    async function loadReplies(questionId, container) {
        container.innerHTML = '<p class="qaEmptyState" style="margin:0;">Loading replies...</p>';
        try {
            const data = await apiCall('view', { params: { id: questionId } });
            if (data.success) {
                renderReplies(container, data.replies);
                threadState[questionId].loaded = true;
            } else {
                container.innerHTML = '<p class="qaEmptyState" style="margin:0;">Could not load replies.</p>';
            }
        } catch (e) {
            container.innerHTML = '<p class="qaEmptyState" style="margin:0;">Could not reach the server.</p>';
        }
    }


    async function postQuestion(body) {
        return apiCall('ask', { method: 'POST', body: { body } });
    }

    async function postReply(questionId, body) {
        return apiCall('reply', { method: 'POST', body: { question_id: questionId, body } });
    }

    async function deleteQuestion(id) {
        return apiCall('delete_question', { method: 'POST', body: { id } });
    }

    async function deleteReply(id) {
        return apiCall('delete_reply', { method: 'POST', body: { id } });
    }


    qaFeed.addEventListener('click', async (e) => {
        const toggleBtn = e.target.closest('[data-action="toggle-replies"]');
        const delQBtn   = e.target.closest('[data-action="delete-question"]');
        const delRBtn   = e.target.closest('[data-action="delete-reply"]');

        if (toggleBtn) {
            const msgEl = toggleBtn.closest('.qaMsg');
            const questionId = msgEl.dataset.questionId;
            const box = msgEl.querySelector('.qaRepliesBox');
            const state = threadState[questionId] || { open: false, loaded: false };
            state.open = !state.open;
            threadState[questionId] = state;
            box.classList.toggle('open', state.open);
            if (state.open && !state.loaded) {
                loadReplies(questionId, box.querySelector('.qaRepliesList'));
            }
            return;
        }

        if (delQBtn) {
            if (!confirm('Delete this message?')) return;
            const msgEl = delQBtn.closest('.qaMsg');
            const questionId = msgEl.dataset.questionId;
            const data = await deleteQuestion(questionId);
            if (data.success) {
                delete threadState[questionId];
                msgEl.remove();
                if (!qaFeed.querySelector('.qaMsg')) {
                    qaFeed.innerHTML = '<p class="qaEmptyState">No messages yet. Start the conversation!</p>';
                }
            } else {
                alert(data.message || 'Could not delete this message.');
            }
            return;
        }

        if (delRBtn) {
            if (!confirm('Delete this reply?')) return;
            const replyEl = delRBtn.closest('.qaReply');
            const msgEl = delRBtn.closest('.qaMsg');
            const replyId = replyEl.dataset.replyId;
            const data = await deleteReply(replyId);
            if (data.success) {
                replyEl.remove();
                const countBtn = msgEl.querySelector('[data-action="toggle-replies"]');
                const list = msgEl.querySelector('.qaRepliesList');
                const remaining = list.querySelectorAll('.qaReply').length;
                countBtn.innerHTML = `<i class="fa-regular fa-comment-dots"></i> ${remaining} repl${remaining === 1 ? 'y' : 'ies'}`;
                if (remaining === 0) {
                    list.innerHTML = '<p class="qaEmptyState" style="margin:0;">No replies yet.</p>';
                }
            } else {
                alert(data.message || 'Could not delete this reply.');
            }
        }
    });

    qaFeed.addEventListener('submit', async (e) => {
        const form = e.target.closest('[data-action="reply-form"]');
        if (!form) return;
        e.preventDefault();

        const msgEl = form.closest('.qaMsg');
        const questionId = msgEl.dataset.questionId;
        const textarea = form.querySelector('textarea');
        const body = textarea.value.trim();
        if (body === '') return;

        const btn = form.querySelector('button');
        btn.disabled = true;

        try {
            const data = await postReply(questionId, body);
            if (data.success) {
                textarea.value = '';
                const list = msgEl.querySelector('.qaRepliesList');
                const emptyMsg = list.querySelector('.qaEmptyState');
                if (emptyMsg) emptyMsg.remove();
                await loadReplies(questionId, list);
                const countBtn = msgEl.querySelector('[data-action="toggle-replies"]');
                const remaining = list.querySelectorAll('.qaReply').length;
                countBtn.innerHTML = `<i class="fa-regular fa-comment-dots"></i> ${remaining} repl${remaining === 1 ? 'y' : 'ies'}`;
            } else {
                alert(data.message || 'Could not post your reply.');
            }
        } catch (err) {
            alert('Could not reach the server.');
        } finally {
            btn.disabled = false;
        }
    });

    qaPostForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        setFormError('');

        if (!isLoggedIn) {
            setFormError('Please log in to post a message.');
            return;
        }

        const body = qaPostBody.value.trim();
        if (body === '') return;

        qaSendBtn.disabled = true;
        try {
            const data = await postQuestion(body);
            if (data.success) {
                qaPostBody.value = '';
                qaPostBody.style.height = 'auto';
                await loadFeed();
                qaFeed.scrollTop = 0;
            } else {
                setFormError(data.message || 'Could not post your message.');
            }
        } catch (err) {
            setFormError('Could not reach the server. Please try again.');
        } finally {
            qaSendBtn.disabled = false;
        }
    });

    qaFeed.addEventListener('input', (e) => {
        if (e.target.tagName === 'TEXTAREA') autoGrow(e.target);
    });

    qaPostBody.addEventListener('input', () => autoGrow(qaPostBody));

    qaPostBody.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            qaPostForm.requestSubmit ? qaPostForm.requestSubmit() : qaPostForm.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    });
    /* ---------- Open / close ---------- */
    function openQA() {
        qaOverlay.classList.add('active');
        document.body.classList.add('qa-modal-open');
        setFormError('');
        loadFeed();
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(loadFeed, POLL_INTERVAL_MS);
        if (!isLoggedIn) {
            qaPostBody.disabled = true;
            qaPostBody.placeholder = 'Log in to join the conversation...';
            setFormError('You are viewing as a guest. Log in to post.');
        }
    }

    function closeQAOverlay() {
        qaOverlay.classList.remove('active');
        document.body.classList.remove('qa-modal-open');
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    if (qaCard) {
        qaCard.addEventListener('click', (e) => {
            e.preventDefault();
            openQA();
        });
    }

    if (closeQA) {
        closeQA.addEventListener('click', closeQAOverlay);
    }

    qaOverlay.addEventListener('click', (e) => {
        if (e.target === qaOverlay) closeQAOverlay();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && qaOverlay.classList.contains('active')) {
            closeQAOverlay();
        }
    });
})();