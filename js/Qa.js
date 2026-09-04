(function () {
    'use strict';
    var LOGGED_IN   = typeof IS_LOGGED_IN !== 'undefined' ? IS_LOGGED_IN : false;
    var CURRENT_UID = typeof CURRENT_USER_ID !== 'undefined' ? CURRENT_USER_ID : 0;
    var qaCard              = document.getElementById('qaCard');
    var qaOverlay           = document.getElementById('qaOverlay');
    var closeQA             = document.getElementById('closeQA');
    var qaApp               = document.getElementById('qaApp');
    var qaChatList          = document.getElementById('qaChatList');
    var qaNewChatBtn        = document.getElementById('openAskQuestion');
    var qaThreadEmpty       = document.getElementById('qaThreadEmpty');
    var qaThreadActive      = document.getElementById('qaThreadActive');
    var qaBackBtn           = document.getElementById('qaBackBtn');
    var qaThreadAvatar      = document.getElementById('qaThreadAvatar');
    var qaThreadTitle       = document.getElementById('qaThreadTitle');
    var qaThreadAuthor      = document.getElementById('qaThreadAuthor');
    var qaDeleteQuestionBtn = document.getElementById('qaDeleteQuestionBtn');
    var qaMessages          = document.getElementById('qaMessages');
    var qaComposer           = document.getElementById('qaComposer');
    var qaReplyInput        = document.getElementById('qaReplyInput');
    var qaSendBtn            = document.getElementById('qaSendBtn');
    var askOverlay          = document.getElementById('askQuestionOverlay');
    var closeAskQuestion    = document.getElementById('closeAskQuestion');
    var qaTitleInput        = document.getElementById('qaTitle');
    var qaBodyInput         = document.getElementById('qaBody');
    var submitQuestionBtn   = document.getElementById('submitQuestion');
    var qaFormError         = document.getElementById('qaFormError');

    if (!qaOverlay) return; 

    var allQuestions = [];
    var activeQuestionId = null;
    var listPollTimer = null;
    var threadPollTimer = null;

    function esc(str) {
        return String(str == null ? '' : str);
    }

    function initials(name) {
        name = (name || '?').trim();
        if (!name) return '?';
        var parts = name.split(/\s+/);
        var out = parts[0].charAt(0);
        if (parts.length > 1) out += parts[parts.length - 1].charAt(0);
        return out.toUpperCase();
    }

    var AVATAR_COLORS = ['#f56a6a', '#f0932b', '#eb4d9e', '#6c5ce7', '#0984e3', '#00b894', '#e67e22', '#26a69a'];
    function avatarColor(name) {
        var str = name || '?';
        var hash = 0;
        for (var i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
        return AVATAR_COLORS[Math.abs(hash) % AVATAR_COLORS.length];
    }

    function parseDate(mysqlDate) {
        // "YYYY-MM-DD HH:MM:SS" -> Date
        return new Date(String(mysqlDate).replace(' ', 'T'));
    }

    function timeShort(mysqlDate) {
        var d = parseDate(mysqlDate);
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function listTimeLabel(mysqlDate) {
        var d = parseDate(mysqlDate);
        var now = new Date();
        var sameDay = d.toDateString() === now.toDateString();
        if (sameDay) return timeShort(mysqlDate);
        var yesterday = new Date(now);
        yesterday.setDate(now.getDate() - 1);
        if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
        return d.toLocaleDateString([], { day: '2-digit', month: 'short' });
    }

    function dateChipLabel(mysqlDate) {
        var d = parseDate(mysqlDate);
        var now = new Date();
        if (d.toDateString() === now.toDateString()) return 'Today';
        var yesterday = new Date(now);
        yesterday.setDate(now.getDate() - 1);
        if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
        return d.toLocaleDateString([], { day: 'numeric', month: 'long', year: 'numeric' });
    }

    function el(tag, className, text) {
        var e = document.createElement(tag);
        if (className) e.className = className;
        if (text !== undefined) e.textContent = text;
        return e;
    }

    function ensureLoggedIn() {
        if (LOGGED_IN) return true;
        var loginTrigger = document.getElementById('openModalBtn2') || document.getElementById('openModalBtn');
        if (loginTrigger) loginTrigger.click();
        return false;
    }

    function openQaOverlay() {
        qaOverlay.classList.add('active');
        loadQuestionList();
        listPollTimer = setInterval(loadQuestionList, 15000);
    }

    function closeQaOverlay() {
        qaOverlay.classList.remove('active');
        clearInterval(listPollTimer);
        clearInterval(threadPollTimer);
    }

    function openAskOverlay() {
        if (!ensureLoggedIn()) return;
        qaTitleInput.value = '';
        qaBodyInput.value = '';
        hideFormError();
        askOverlay.classList.add('active');
        qaTitleInput.focus();
    }

    function closeAskOverlay() {
        askOverlay.classList.remove('active');
    }

    function showFormError(msg) {
        qaFormError.textContent = msg;
        qaFormError.style.display = 'block';
    }
    function hideFormError() {
        qaFormError.style.display = 'none';
    }

    /*  chat list  */

    function loadQuestionList() {
        fetch('Qa.php?action=list')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) return;
                allQuestions = data.questions || [];
                renderQuestionList();
            })
            .catch(function () {
                qaChatList.innerHTML = '';
                qaChatList.appendChild(el('p', 'qaEmptyState', 'Could not load questions. Check your connection.'));
            });
    }

    function renderQuestionList() {
        qaChatList.innerHTML = '';

        if (allQuestions.length === 0) {
            qaChatList.appendChild(el('p', 'qaEmptyState', 'No questions yet. Tap the pencil to start one!'));
            return;
        }

        allQuestions.forEach(function (q) {
            var item = el('div', 'qaChatItem');
            item.dataset.id = q.id;
            if (String(q.id) === String(activeQuestionId)) item.classList.add('selected');

            var avatar = el('div', 'qaAvatar', initials(q.author));
            avatar.style.background = avatarColor(q.author);

            var body = el('div', 'qaChatItemBody');
            var top = el('div', 'qaChatItemTop');
            top.appendChild(el('span', 'qaChatItemTitle', q.title));
            top.appendChild(el('span', 'qaChatItemTime', listTimeLabel(q.created_at)));

            var previewRow = el('div', 'qaChatItemTop');
            var preview = el('span', 'qaChatItemPreview', q.author + ': ' + q.body);
            previewRow.appendChild(preview);
            if (parseInt(q.reply_count, 10) > 0) {
                previewRow.appendChild(el('span', 'qaReplyBadge', q.reply_count));
            }

            body.appendChild(top);
            body.appendChild(previewRow);

            item.appendChild(avatar);
            item.appendChild(body);
            item.addEventListener('click', function () { openThread(q.id); });

            qaChatList.appendChild(item);
        });
    }


    function openThread(id) {
        activeQuestionId = id;
        qaApp.classList.add('showThread');
        renderQuestionList(); 

        fetch('Qa.php?action=view&id=' + encodeURIComponent(id))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) {
                    qaMessages.innerHTML = '';
                    qaMessages.appendChild(el('p', 'qaEmptyState', data.message || 'Question not found.'));
                    return;
                }
                renderThread(data.question, data.replies || []);
            })
            .catch(function () {
                qaMessages.innerHTML = '';
                qaMessages.appendChild(el('p', 'qaEmptyState', 'Could not load this conversation.'));
            });

        clearInterval(threadPollTimer);
        threadPollTimer = setInterval(function () {
            if (activeQuestionId !== id) return;
            fetch('Qa.php?action=view&id=' + encodeURIComponent(id))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) renderThread(data.question, data.replies || [], true);
                })
                .catch(function () {});
        }, 6000);
    }

    function renderThread(question, replies, silent) {
        qaThreadEmpty.style.display = 'none';
        qaThreadActive.style.display = 'flex';

        qaThreadAvatar.textContent = initials(question.author);
        qaThreadAvatar.style.background = avatarColor(question.author);
        qaThreadTitle.textContent = question.title;
        qaThreadAuthor.textContent = replies.length + (replies.length === 1 ? ' reply' : ' replies');

        qaDeleteQuestionBtn.style.display = (LOGGED_IN && parseInt(question.user_id, 10) === CURRENT_UID) ? 'flex' : 'none';
        qaDeleteQuestionBtn.onclick = function () { deleteQuestion(question.id); };

        var wasAtBottom = !silent || (qaMessages.scrollHeight - qaMessages.scrollTop - qaMessages.clientHeight < 60);

        qaMessages.innerHTML = '';
        qaMessages.appendChild(makeDateChip(question.created_at));
        qaMessages.appendChild(makeBubble({
            id: question.id,
            body: question.body,
            author: question.author,
            user_id: question.user_id,
            created_at: question.created_at
        }, true));

        var lastDay = new Date(question.created_at.replace(' ', 'T')).toDateString();
        replies.forEach(function (r) {
            var day = new Date(r.created_at.replace(' ', 'T')).toDateString();
            if (day !== lastDay) {
                qaMessages.appendChild(makeDateChip(r.created_at));
                lastDay = day;
            }
            qaMessages.appendChild(makeBubble(r, false));
        });

        if (wasAtBottom) qaMessages.scrollTop = qaMessages.scrollHeight;

        qaComposer.style.display = LOGGED_IN ? 'flex' : 'none';
        var notice = document.getElementById('qaLoginNotice');
        if (!LOGGED_IN) {
            if (!notice) {
                notice = el('div', 'qaLoginNotice', 'Log in to join the conversation.');
                notice.id = 'qaLoginNotice';
                qaThreadActive.appendChild(notice);
            }
        } else if (notice) {
            notice.remove();
        }
    }

    function makeDateChip(dateStr) {
        return el('div', 'qaDateChip', dateChipLabel(dateStr));
    }

    function makeBubble(msg, isQuestion) {
        var mine = LOGGED_IN && parseInt(msg.user_id, 10) === CURRENT_UID;
        var row = el('div', 'qaBubbleRow ' + (mine ? 'mine' : 'theirs'));
        var bubble = el('div', 'qaBubble');

        if (isQuestion) {
            bubble.appendChild(el('span', 'qaBubbleTag', 'QUESTION'));
        }
        if (!mine) {
            bubble.appendChild(el('span', 'qaBubbleAuthor', msg.author));
        }
        bubble.appendChild(el('div', 'qaBubbleText', msg.body));

        var meta = el('div', 'qaBubbleMeta');
        meta.appendChild(document.createTextNode(timeShort(msg.created_at)));
        if (mine) {
            var check = el('i', 'fa-solid fa-check');
            meta.appendChild(check);
        }
        bubble.appendChild(meta);

        if (mine) {
            var del = el('button', 'qaBubbleDelete');
            del.type = 'button';
            del.innerHTML = '<i class="fa-solid fa-trash"></i>';
            del.title = 'Delete';
            del.addEventListener('click', function (e) {
                e.stopPropagation();
                if (isQuestion) deleteQuestion(msg.id);
                else deleteReply(msg.id);
            });
            bubble.appendChild(del);
        }

        row.appendChild(bubble);
        return row;
    }

    function backToList() {
        qaApp.classList.remove('showThread');
    }

    /* actions  */

    function postReply(e) {
        e.preventDefault();
        if (!ensureLoggedIn() || !activeQuestionId) return;
        var body = qaReplyInput.value.trim();
        if (!body) return;

        qaSendBtn.disabled = true;
        var fd = new FormData();
        fd.append('action', 'reply');
        fd.append('question_id', activeQuestionId);
        fd.append('body', body);

        fetch('Qa.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                qaSendBtn.disabled = false;
                if (data.success) {
                    qaReplyInput.value = '';
                    qaReplyInput.style.height = 'auto';
                    openThread(activeQuestionId);
                    loadQuestionList();
                } else {
                    alert(data.message || 'Could not send your reply.');
                }
            })
            .catch(function () {
                qaSendBtn.disabled = false;
                alert('Network error — please try again.');
            });
    }

    function submitQuestion() {
        if (!ensureLoggedIn()) return;
        var title = qaTitleInput.value.trim();
        var body = qaBodyInput.value.trim();
        hideFormError();
        if (!title || !body) {
            showFormError('Please fill in both the title and the details.');
            return;
        }

        submitQuestionBtn.disabled = true;
        var fd = new FormData();
        fd.append('action', 'ask');
        fd.append('title', title);
        fd.append('body', body);

        fetch('Qa.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                submitQuestionBtn.disabled = false;
                if (data.success) {
                    closeAskOverlay();
                    loadQuestionList();
                    openThread(data.id);
                } else {
                    showFormError(data.message || 'Could not post your question.');
                }
            })
            .catch(function () {
                submitQuestionBtn.disabled = false;
                showFormError('Network error — please try again.');
            });
    }

    function deleteQuestion(id) {
        if (!confirm('Delete this question and all its replies?')) return;
        var fd = new FormData();
        fd.append('action', 'delete_question');
        fd.append('id', id);
        fetch('Qa.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function () {
                activeQuestionId = null;
                qaThreadActive.style.display = 'none';
                qaThreadEmpty.style.display = 'flex';
                backToList();
                loadQuestionList();
            });
    }

    function deleteReply(id) {
        if (!confirm('Delete this reply?')) return;
        var fd = new FormData();
        fd.append('action', 'delete_reply');
        fd.append('id', id);
        fetch('Qa.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function () {
                if (activeQuestionId) openThread(activeQuestionId);
                loadQuestionList();
            });
    }

    /*  wire up events */

    if (qaCard) {
        qaCard.addEventListener('click', function (e) {
            e.preventDefault();
            openQaOverlay();
        });
    }
    if (closeQA) closeQA.addEventListener('click', closeQaOverlay);
    qaOverlay.addEventListener('click', function (e) {
        if (e.target === qaOverlay) closeQaOverlay();
    });

    if (qaBackBtn) qaBackBtn.addEventListener('click', backToList);

    if (qaNewChatBtn) qaNewChatBtn.addEventListener('click', openAskOverlay);
    if (closeAskQuestion) closeAskQuestion.addEventListener('click', closeAskOverlay);
    if (askOverlay) askOverlay.addEventListener('click', function (e) {
        if (e.target === askOverlay) closeAskOverlay();
    });
    if (submitQuestionBtn) submitQuestionBtn.addEventListener('click', submitQuestion);

    if (qaComposer) qaComposer.addEventListener('submit', postReply);
    if (qaReplyInput) {
        qaReplyInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                postReply(e);
            }
        });
        qaReplyInput.addEventListener('input', function () {
            qaReplyInput.style.height = 'auto';
            qaReplyInput.style.height = Math.min(qaReplyInput.scrollHeight, 100) + 'px';
        });
    }
})();