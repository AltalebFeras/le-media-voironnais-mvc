const EventInteractions = (function() {
    let eventUiid = null;
    let isLoggedIn = false;
    let interactionData = null;
    let currentUserUiid = null; // Changed from currentUserId to currentUserUiid
    let openRepliesSections = new Set(); // Track which reply sections are open

    const SVG_ICONS = {
        likeActive: `<svg stroke="currentColor" fill="#0053f9ff" stroke-width="1" viewBox="0 0 1024 1024" height="32px" width="32px" xmlns="http://www.w3.org/2000/svg"><path d="M885.9 533.7c16.8-22.2 26.1-49.4 26.1-77.7 0-44.9-25.1-87.4-65.5-111.1a67.67 67.67 0 0 0-34.3-9.3H572.4l6-122.9c1.4-29.7-9.1-57.9-29.5-79.4A106.62 106.62 0 0 0 471 99.9c-52 0-98 35-111.8 85.1l-85.9 311h-.3v428h472.3c9.2 0 18.2-1.8 26.5-5.4 47.6-20.3 78.3-66.8 78.3-118.4 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7-.2-12.6-2-25.1-5.6-37.1zM112 528v364c0 17.7 14.3 32 32 32h65V496h-65c-17.7 0-32 14.3-32 32z"></path></svg>`,
        likeInactive: `<svg stroke="currentColor" fill="#a99a9aff" stroke-width="1" viewBox="0 0 1024 1024" height="32px" width="32px" xmlns="http://www.w3.org/2000/svg"><path d="M885.9 533.7c16.8-22.2 26.1-49.4 26.1-77.7 0-44.9-25.1-87.4-65.5-111.1a67.67 67.67 0 0 0-34.3-9.3H572.4l6-122.9c1.4-29.7-9.1-57.9-29.5-79.4A106.62 106.62 0 0 0 471 99.9c-52 0-98 35-111.8 85.1l-85.9 311h-.3v428h472.3c9.2 0 18.2-1.8 26.5-5.4 47.6-20.3 78.3-66.8 78.3-118.4 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7-.2-12.6-2-25.1-5.6-37.1zM112 528v364c0 17.7 14.3 32 32 32h65V496h-65c-17.7 0-32 14.3-32 32z"></path></svg>`,
        favouriteActive: `<svg width="32px" height="32px" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="48" fill="#fff" stroke="black" stroke-width="2"/><polygon points="50,20 61,39 82,42 67,57 71,78 50,67 29,78 33,57 18,42 39,39" fill="#FFD700"/></svg>`,
        favouriteInactive: `<svg width="32px" height="32px" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="48" fill="#a99a9aff" stroke="black" stroke-width="2"/><polygon points="50,20 61,39 82,42 67,57 71,78 50,67 29,78 33,57 18,42 39,39" fill="#fff"/></svg>`,
        commentLike: `<svg stroke="currentColor" fill="#0053f9ff" stroke-width="1" viewBox="0 0 1024 1024" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg"><path d="M885.9 533.7c16.8-22.2 26.1-49.4 26.1-77.7 0-44.9-25.1-87.4-65.5-111.1a67.67 67.67 0 0 0-34.3-9.3H572.4l6-122.9c1.4-29.7-9.1-57.9-29.5-79.4A106.62 106.62 0 0 0 471 99.9c-52 0-98 35-111.8 85.1l-85.9 311h-.3v428h472.3c9.2 0 18.2-1.8 26.5-5.4 47.6-20.3 78.3-66.8 78.3-118.4 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7-.2-12.6-2-25.1-5.6-37.1zM112 528v364c0 17.7 14.3 32 32 32h65V496h-65c-17.7 0-32 14.3-32 32z"></path></svg>`,
        commentReply: `<svg stroke="currentColor" fill="#0053f9ff" stroke-width="0" viewBox="0 0 512 512" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg"><path d="M256 32C114.62 32 0 125.12 0 240c0 49.56 21.41 95 57 130.74C44.46 421.05 2.7 466 2.2 466.5A8 8 0 0 0 8 480c66.26 0 116-31.75 140.6-51.38A304.66 304.66 0 0 0 256 448c141.39 0 256-93.12 256-208S397.39 32 256 32zm96 232a8 8 0 0 1-8 8h-56v56a8 8 0 0 1-8 8h-48a8 8 0 0 1-8-8v-56h-56a8 8 0 0 1-8-8v-48a8 8 0 0 1 8-8h56v-56a8 8 0 0 1 8-8h48a8 8 0 0 1 8 8v56h56a8 8 0 0 1 8 8z"></path></svg>`,
        commentDelete: `<svg stroke="currentColor" fill="#ff0000" stroke-width="0" viewBox="0 0 1024 1024" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg"><path d="M864 256H736v-80c0-35.3-28.7-64-64-64H352c-35.3 0-64 28.7-64 64v80H160c-17.7 0-32 14.3-32 32v32c0 4.4 3.6 8 8 8h60.4l24.7 523c1.6 34.1 29.8 61 63.9 61h454c34.2 0 62.3-26.8 63.9-61l24.7-523H888c4.4 0 8-3.6 8-8v-32c0-17.7-14.3-32-32-32zm-200 0H360v-72h304v72z"></path></svg>`,
        commentReport: `<svg stroke="" fill="#fff" viewBox="0 0 24 24" height="20px" width="20px" style="vertical-align:middle;" xmlns="http://www.w3.org/2000/svg"><path stroke="black" stroke-width="0.4" d="M9.836 3.244c.963-1.665 3.365-1.665 4.328 0l8.967 15.504c.963 1.667-.24 3.752-2.165 3.752H3.034c-1.926 0-3.128-2.085-2.165-3.752Z"/><path d="M12 8.5a.75.75 0 0 0-.75.75v4.5a.75.75 0 0 0 1.5 0v-4.5A.75.75 0 0 0 12 8.5Zm1 9a1 1 0 1 0-2 0 1 1 0 0 0 2 0Z" fill="#ff0000"/></svg>`
    };

    async function loadInteractions() {
        try {
            const response = await fetch(`/evenement/interactions?uiid=${eventUiid}`);
            const data = await response.json();
            
            if (data.success) {
                interactionData = data;
                updateUI();
            } else {
                console.error('Error loading interactions:', data.error);
            }
        } catch (error) {
            console.error('Error fetching interactions:', error);
        }
    }

    function updateUI() {
        // Update likes count and icon
        const likesCountEl = document.getElementById('event-likes-count');
        if (likesCountEl) {
            const count = interactionData.likesCount;
            likesCountEl.textContent = `${count} personne${count > 1 ? 's' : ''} aime${count > 1 ? 'nt' : ''} cet événement`;
        }

        const likeIconEl = document.getElementById('like-icon');
        if (likeIconEl) {
            likeIconEl.innerHTML = interactionData.userHasLiked ? SVG_ICONS.likeActive : SVG_ICONS.likeInactive;
        }

        // Update favourite icon
        const favouriteIconEl = document.getElementById('favourite-icon');
        if (favouriteIconEl) {
            favouriteIconEl.innerHTML = interactionData.userHasFavourited ? SVG_ICONS.favouriteActive : SVG_ICONS.favouriteInactive;
        }

        // Update comments count
        const commentsCountEl = document.getElementById('event-comments-count');
        if (commentsCountEl) {
            const count = interactionData.commentsCount;
            commentsCountEl.textContent = `${count} commentaire${count > 1 ? 's' : ''}`;
        }

        // Render comments
        renderComments();
    }

    function renderComments() {
        const commentsList = document.getElementById('comments-list');
        if (!commentsList) return;

        const { comments, replies } = interactionData;

        // Group replies by parentId
        const repliesByParent = {};
        replies.forEach(reply => {
            if (!repliesByParent[reply.parentId]) {
                repliesByParent[reply.parentId] = [];
            }
            repliesByParent[reply.parentId].push(reply);
        });

        let html = '';
        comments.forEach(comment => {
            if (comment.parentId) return; // Skip replies, they'll be nested

            html += renderComment(comment, repliesByParent[comment.idEventComment] || []);
        });

        commentsList.innerHTML = html || '<p>Aucun commentaire pour le moment.</p>';

        // Attach event listeners
        attachCommentEventListeners();
    }

    function formatCommentDate(dateString) {
        const commentDate = new Date(dateString);
        const now = new Date();
        const diffMs = now - commentDate;
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        const diffWeeks = Math.floor(diffDays / 7);

        // Format hour
        const hours = commentDate.getHours().toString().padStart(2, '0');
        const minutes = commentDate.getMinutes().toString().padStart(2, '0');
        const timeString = `${hours}:${minutes}`;

        // Today - show only time
        if (diffDays === 0) {
            return `${timeString}`;
        }
        
        // Yesterday
        if (diffDays === 1) {
            return `Hier à ${timeString}`;
        }
        
        // 2-6 days ago
        if (diffDays >= 2 && diffDays <= 6) {
            return `Il y a ${diffDays} jours`;
        }
        
        // 1 week ago
        if (diffWeeks === 1) {
            return `Il y a 1 semaine`;
        }
        
        // Multiple weeks ago
        if (diffWeeks > 1 && diffWeeks < 4) {
            return `Il y a ${diffWeeks} semaines`;
        }
        
        // More than 4 weeks - show full date
        const day = commentDate.getDate().toString().padStart(2, '0');
        const month = (commentDate.getMonth() + 1).toString().padStart(2, '0');
        const year = commentDate.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function renderComment(comment, replies = []) {
        const hasReplies = replies.length > 0;
        const formattedDate = formatCommentDate(comment.createdAt);
        const canDelete = isLoggedIn && currentUserUiid && comment.userUiid == currentUserUiid;
        const isOpen = openRepliesSections.has(comment.uiid);
        
        let html = `
            <div class="comment" data-uiid="${comment.uiid}">
                <b>${escapeHtml(comment.firstName)} ${escapeHtml(comment.lastName)}</b>
                <small style="color:#999;margin-left:8px;">${formattedDate}</small>
                <p>${escapeHtml(comment.content).replace(/\n/g, '<br>')}</p>
                <div>
                    <span>${comment.likesCount > 0 ? comment.likesCount : ''}</span>
                    ${isLoggedIn ? `
                        <button class="like-comment-btn" data-uiid="${comment.uiid}" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                            ${SVG_ICONS.commentLike}
                        </button>
                        <button class="reply-comment-btn" data-uiid="${comment.uiid}" data-parent="${comment.uiid}" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                            ${SVG_ICONS.commentReply}
                        </button>
                        ${canDelete ? `
                            <button class="delete-comment-btn" data-uiid="${comment.uiid}" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                                ${SVG_ICONS.commentDelete}
                            </button>
                        ` : ''}
                        <button class="report-comment-btn" data-uiid="${comment.uiid}" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                            ${SVG_ICONS.commentReport}
                        </button>
                    ` : ''}
                </div>`;

        if (hasReplies) {
            html += `<div class="replies" id="replies-${comment.uiid}" style="margin-left:2em;display:${isOpen ? 'block' : 'none'};">`;
            replies.forEach(reply => {
                const replyFormattedDate = formatCommentDate(reply.createdAt);
                const canDeleteReply = isLoggedIn && currentUserUiid && reply.userUiid == currentUserUiid; // Changed from reply.idUser
                
                html += `
                    <div class="comment reply" data-uiid="${reply.uiid}">
                        <b>${escapeHtml(reply.firstName)} ${escapeHtml(reply.lastName)}</b>
                        <small style="color:#999;margin-left:8px;">${replyFormattedDate}</small>
                        <p>${escapeHtml(reply.content).replace(/\n/g, '<br>')}</p>
                        <div>
                            <span>${reply.likesCount > 0 ? reply.likesCount : ''}</span>
                            ${isLoggedIn ? `
                                <button class="like-comment-btn" data-uiid="${reply.uiid}" data-parent="${comment.uiid}" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                                    ${SVG_ICONS.commentLike}
                                </button>
                                <button class="reply-comment-btn" data-uiid="${comment.uiid}" data-parent="${comment.uiid}" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                                    ${SVG_ICONS.commentReply}
                                </button>
                                ${canDeleteReply ? `
                                    <button class="delete-comment-btn" data-uiid="${reply.uiid}" data-parent="${comment.uiid}" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                                        ${SVG_ICONS.commentDelete}
                                    </button>
                                ` : ''}
                                <button class="report-comment-btn" data-uiid="${reply.uiid}" style="background:none;border:none;vertical-align:middle;cursor:pointer;">
                                    ${SVG_ICONS.commentReport}
                                </button>
                            ` : ''}
                        </div>
                    </div>`;
            });
            html += `</div>
                <button class="show-replies-btn" data-uiid="${comment.uiid}" style="cursor:pointer;">
                    ${isOpen ? 'Masquer les réponses' : `Voir toutes les ${replies.length} réponse${replies.length > 1 ? 's' : ''}`}
                </button>`;
        }

        html += `
            <form class="reply-form" data-parent="${comment.uiid}" style="display:none;margin-left:2em;">
                <textarea name="content" required></textarea>
                <input type="hidden" name="eventUiid" value="${eventUiid}">
                <input type="hidden" name="parentUiid" value="${comment.uiid}">
                <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="32px" width="32px" xmlns="http://www.w3.org/2000/svg"><path d="m476.59 227.05-.16-.07L49.35 49.84A23.56 23.56 0 0 0 27.14 52 24.65 24.65 0 0 0 16 72.59v113.29a24 24 0 0 0 19.52 23.57l232.93 43.07a4 4 0 0 1 0 7.86L35.53 303.45A24 24 0 0 0 16 327v113.31A23.57 23.57 0 0 0 26.59 460a23.94 23.94 0 0 0 13.22 4 24.55 24.55 0 0 0 9.52-1.93L476.4 285.94l.19-.09a32 32 0 0 0 0-58.8z"></path></svg>
                </button>
            </form>
        </div>`;

        return html;
    }

    function attachCommentEventListeners() {
        // Like button
        document.getElementById('like-btn')?.addEventListener('click', handleEventLike);
        
        // Favourite button
        document.getElementById('favourite-btn')?.addEventListener('click', handleEventFavourite);

        // Comment like buttons
        document.querySelectorAll('.like-comment-btn').forEach(btn => {
            btn.addEventListener('click', handleCommentLike);
        });

        // Reply buttons
        document.querySelectorAll('.reply-comment-btn').forEach(btn => {
            btn.addEventListener('click', handleReplyToggle);
        });

        // Delete buttons
        document.querySelectorAll('.delete-comment-btn').forEach(btn => {
            btn.addEventListener('click', handleCommentDelete);
        });

        // Report buttons
        document.querySelectorAll('.report-comment-btn').forEach(btn => {
            btn.addEventListener('click', handleCommentReport);
        });

        // Show/hide replies
        document.querySelectorAll('.show-replies-btn').forEach(btn => {
            btn.addEventListener('click', handleToggleReplies);
        });

        // Reply forms
        document.querySelectorAll('.reply-form').forEach(form => {
            form.addEventListener('submit', handleReplySubmit);
        });

        // Main comment form
        document.getElementById('add-comment-form')?.addEventListener('submit', handleCommentSubmit);
    }

    async function handleEventLike() {
        const response = await fetch('/evenement/like', {
            method: 'POST',
            body: new URLSearchParams({ eventUiid: eventUiid })
        });
        const data = await response.json();
        await loadInteractions();
    }

    async function handleEventFavourite() {
        const response = await fetch('/evenement/favourite', {
            method: 'POST',
            body: new URLSearchParams({ eventUiid: eventUiid })
        });
        const data = await response.json();
        await loadInteractions();
    }

    async function handleCommentLike(e) {
        const commentUiid = this.dataset.uiid;
        const parentUiid = this.dataset.parent;
        
        // Track if parent replies section is open
        if (parentUiid) {
            const repliesDiv = document.getElementById(`replies-${parentUiid}`);
            if (repliesDiv && repliesDiv.style.display === 'block') {
                openRepliesSections.add(parentUiid);
            }
        }
        
        await fetch('/evenement/comment/like', {
            method: 'POST',
            body: new URLSearchParams({ commentUiid: commentUiid })
        });
        await loadInteractions();
    }

    function handleReplyToggle(e) {
        const parentUiid = this.dataset.parent;
        const commentDiv = document.querySelector(`.comment[data-uiid="${parentUiid}"]`);
        const form = commentDiv.querySelector(`.reply-form[data-parent="${parentUiid}"]`);
        const repliesDiv = document.getElementById(`replies-${parentUiid}`);
        
        // Show replies if hidden
        if (repliesDiv && repliesDiv.style.display === 'none') {
            repliesDiv.style.display = 'block';
            openRepliesSections.add(parentUiid);
            const showBtn = commentDiv.querySelector(`.show-replies-btn[data-uiid="${parentUiid}"]`);
            if (showBtn) {
                showBtn.textContent = 'Masquer les réponses';
            }
        }
        
        // Toggle form
        if (form) {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    }

    async function handleCommentDelete(e) {
        const commentUiid = this.dataset.uiid;
        const parentUiid = this.dataset.parent;
        
        // Track if parent replies section is open
        if (parentUiid) {
            const repliesDiv = document.getElementById(`replies-${parentUiid}`);
            if (repliesDiv && repliesDiv.style.display === 'block') {
                openRepliesSections.add(parentUiid);
            }
        }
        
        await fetch('/evenement/comment/delete', {
            method: 'POST',
            body: new URLSearchParams({ commentUiid: commentUiid })
        });
        await loadInteractions();
    }

    async function handleCommentReport(e) {
        const commentUiid = this.dataset.uiid;
        showReportModal(commentUiid);
    }

    function showReportModal(commentUiid) {
        // Create modal if it doesn't exist
        let modal = document.getElementById('report-comment-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'report-comment-modal';
            modal.className = 'popup';
            modal.style.display = 'none';
            modal.innerHTML = `
                <div class="card" style="max-width:500px;">
                    <h3>Signaler un commentaire</h3>
                    <button type="button" class="close-modal" style="position:absolute; right:10px; top:10px; background:none; border:none; font-size:24px; cursor:pointer;">×</button>
                    <div class="mt mb">
                        <p>Pourquoi signalez-vous ce commentaire ?</p>
                        <textarea id="report-reason" class="form-control" rows="4" placeholder="Expliquez la raison du signalement..." required></textarea>
                    </div>
                    <div class="flex-row justify-content-between" style="display:flex;gap:1em;margin-top:1em;">
                        <button type="button" class="btn cancel-report">Annuler</button>
                        <button type="button" class="btn btn-danger confirm-report">Signaler</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // Close modal handlers
            modal.querySelector('.close-modal').addEventListener('click', () => {
                modal.style.display = 'none';
                modal.querySelector('#report-reason').value = '';
            });

            modal.querySelector('.cancel-report').addEventListener('click', () => {
                modal.style.display = 'none';
                modal.querySelector('#report-reason').value = '';
            });

            // Confirm report handler
            modal.querySelector('.confirm-report').addEventListener('click', async () => {
                const reason = modal.querySelector('#report-reason').value.trim();
                const storedCommentUiid = modal.dataset.commentUiid;

                if (!reason) {
                    showErrorMessage('Veuillez expliquer la raison du signalement.');
                    return;
                }

                await fetch('/evenement/comment/report', {
                    method: 'POST',
                    body: new URLSearchParams({ commentUiid: storedCommentUiid, reason })
                });

                modal.style.display = 'none';
                modal.querySelector('#report-reason').value = '';
                
                // Show success message
                showSuccessMessage('Commentaire signalé avec succès');
            });
        }

        // Store comment ID and show modal
        modal.dataset.commentUiid = commentUiid;
        modal.style.display = 'flex';
    }

    function showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success';
        successDiv.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;padding:1em;background:#28a745;color:white;border-radius:4px;';
        successDiv.textContent = message;
        document.body.appendChild(successDiv);
        
        setTimeout(() => {
            successDiv.remove();
        }, 5000);
    }
    function showErrorMessage(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger';
        errorDiv.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;padding:1em;background:#dc3545;color:white;border-radius:4px;';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);

        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }

    async function handleCommentSubmit(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        const response = await fetch('/evenement/comment', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            this.reset();
            await loadInteractions();
        } else {
            showErrorMessage(data.error || "Erreur");
        }
    }

    async function handleReplySubmit(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const parentUiid = this.dataset.parent;

        // Track that this section should stay open
        if (parentUiid) {
            openRepliesSections.add(parentUiid);
        }

        const response = await fetch('/evenement/comment/reply', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            this.reset();
            this.style.display = 'none';
            await loadInteractions();
        } else {
            showErrorMessage(data.error || "Erreur");
        }
    }

    function handleToggleReplies(e) {
        const commentUiid = this.dataset.uiid;
        const repliesDiv = document.getElementById(`replies-${commentUiid}`);
        const count = repliesDiv.children.length;
        
        if (repliesDiv.style.display === 'none') {
            repliesDiv.style.display = 'block';
            openRepliesSections.add(commentUiid);
            this.textContent = "Masquer les réponses";
        } else {
            repliesDiv.style.display = 'none';
            openRepliesSections.delete(commentUiid);
            this.textContent = `Voir toutes les ${count} réponse${count > 1 ? 's' : ''}`;
        }
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    return {
        init: function(evUiid, loggedIn, userUiid = null) { // Changed parameter name
            eventUiid = evUiid;
            isLoggedIn = loggedIn;
            currentUserUiid = userUiid; // Store userUiid instead of userId
            loadInteractions();
        }
    };
})();
