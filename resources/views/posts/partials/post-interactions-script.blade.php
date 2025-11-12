@once('post-interactions-script')
<script>
(function(){
	const iconMap = {
		like: 'thumbs-up',
		love: 'heart',
		haha: 'laugh',
		wow: 'surprise',
		sad: 'sad-tear',
		angry: 'angry'
	};

	function escapeHtml(string) {
		const str = String(string ?? '');
		const entityMap = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#39;'
		};
		return str.replace(/[&<>"']/g, char => entityMap[char] || char);
	}

	function truncate(str, length = 50) {
		if (!str) return '';
		return str.length > length ? str.substring(0, length) + '…' : str;
	}

	function showToast(message, type = 'success') {
		if (!message) return;
		let container = document.getElementById('toastContainer');
		if (!container) {
			container = document.createElement('div');
			container.id = 'toastContainer';
			container.style.position = 'fixed';
			container.style.top = '1rem';
			container.style.right = '1rem';
			container.style.zIndex = '9999';
			container.style.display = 'flex';
			container.style.flexDirection = 'column';
			container.style.gap = '0.5rem';
			container.style.pointerEvents = 'none';
			document.body.appendChild(container);
		}

		const toast = document.createElement('div');
		toast.className = type === 'error'
			? 'px-4 py-3 rounded-lg bg-red-600/90 border border-red-400 text-white text-sm shadow-lg transition'
			: 'px-4 py-3 rounded-lg bg-purple-600/90 border border-purple-300 text-white text-sm shadow-lg transition';
		toast.textContent = message;
		toast.style.opacity = '1';
		container.appendChild(toast);

		setTimeout(() => {
			toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
			toast.style.opacity = '0';
			toast.style.transform = 'translateY(-10px)';
			setTimeout(() => toast.remove(), 400);
		}, 2500);
	}

	function updateReactionButtons(postId, userReaction) {
		document.querySelectorAll(`.reaction-btn[data-post-id="${postId}"]`).forEach(btn => {
			const activeClass = btn.dataset.activeClass;
			const defaultClass = btn.dataset.defaultClass;
			btn.className = userReaction === btn.dataset.reactionType ? activeClass : defaultClass;
		});
	}

	function updateReactionSummary(postId, byType = {}) {
		const summary = document.querySelector(`[data-reaction-summary="${postId}"]`);
		if (!summary) return;
		const entries = Object.entries(byType);
		summary.innerHTML = '';
		entries.forEach(([type, count]) => {
			const span = document.createElement('span');
			span.className = 'mr-2';
			span.dataset.reactionSummaryItem = type;
			const icon = iconMap[type] || 'thumbs-up';
			span.innerHTML = `${count} <i class="fas fa-${icon}"></i>`;
			summary.appendChild(span);
		});
	}

	function updateReactionTotals(postId, total) {
		document.querySelectorAll(`[data-reaction-total="${postId}"]`).forEach(el => {
			el.textContent = total;
		});
	}

	function updateCommentCounts(postId, count) {
		document.querySelectorAll(`[data-comment-count="${postId}"]`).forEach(el => {
			el.textContent = count;
		});
		document.querySelectorAll(`[data-comment-label="${postId}"]`).forEach(el => {
			const singular = el.dataset.singular || 'comentario';
			const plural = el.dataset.plural || 'comentarios';
			el.textContent = count === 1 ? singular : plural;
		});
	}

	function toggleViewAll(postId, count) {
		document.querySelectorAll(`[data-view-all="${postId}"]`).forEach(el => {
			if (count > parseInt(el.dataset.viewAllThreshold || '3', 10)) {
				el.classList.remove('hidden');
			} else {
				el.classList.add('hidden');
			}
		});
	}

	function addCommentToList(postId, comment, canDelete, limit) {
		const list = document.querySelector(`[data-comments-list="${postId}"]`);
		if (!list) return;
		const layout = list.dataset.commentLayout || 'compact';
		const avatar = comment.user.avatar
			? `<img src="${comment.user.avatar}" class="w-full h-full object-cover" alt="${escapeHtml(comment.user.name)}">`
			: escapeHtml(comment.user.name.charAt(0).toUpperCase());
		const deleteButton = canDelete
			? `<button type="button" onclick="openDeleteCommentModal(${comment.id}, ${postId}, this)" data-comment-preview="${escapeHtml(truncate(comment.content))}" class="text-red-400 hover:text-red-300 text-xs delete-comment-btn" title="Eliminar">
				<i class="fas fa-trash-alt"></i>
			</button>`
			: '';

		let html = '';
		if (layout === 'full') {
			html = `
				<div class="mb-4 pb-4 border-b border-purple-500/30 last:border-0 last:mb-0 last:pb-0" data-comment-item>
					<div class="flex items-start justify-between mb-2">
						<div class="flex items-center gap-3 flex-1">
							<a href="${comment.user.profile_url}" class="flex items-center gap-2 hover:opacity-80 transition">
								<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold border border-purple-400 overflow-hidden">
									${avatar}
								</div>
								<div>
									<div class="font-semibold text-purple-200 text-sm">${escapeHtml(comment.user.name)}</div>
									<div class="text-xs text-purple-400">${comment.created_at_diff}</div>
								</div>
							</a>
						</div>
						${deleteButton ? `<div class="flex gap-2">${deleteButton}</div>` : ''}
					</div>
					<div class="text-purple-100 ml-14">${escapeHtml(comment.content)}</div>
				</div>
			`;
		} else {
			html = `
				<div class="flex items-start gap-2 text-sm" data-comment-item>
					<a href="${comment.user.profile_url}" class="flex-shrink-0">
						<div class="w-6 h-6 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-xs border border-purple-400 overflow-hidden">
							${avatar}
						</div>
					</a>
					<div class="flex-1 min-w-0">
						<div>
							<a href="${comment.user.profile_url}" class="font-semibold text-purple-200 hover:text-purple-100 text-xs">
								${escapeHtml(comment.user.name)}
							</a>
							<span class="text-purple-300 ml-2" data-comment-content>${escapeHtml(comment.content)}</span>
						</div>
						<div class="text-xs text-purple-400 mt-0.5" data-comment-date>${comment.created_at_diff}</div>
					</div>
					${deleteButton}
				</div>
			`;
		}

		const temp = document.createElement('div');
		temp.innerHTML = html.trim();
		const newCommentElement = temp.firstElementChild;
		if (!newCommentElement) return;

		const insertMode = list.dataset.insertMode || 'prepend';
		if (insertMode === 'append' && list.lastElementChild) {
			list.appendChild(newCommentElement);
		} else {
			list.prepend(newCommentElement);
		}

		const limitFromDataset = parseInt(list.dataset.commentsLimit || limit || '0', 10);
		if (limitFromDataset > 0) {
			const items = list.querySelectorAll('[data-comment-item]');
			items.forEach((item, index) => {
				if (index >= limitFromDataset) {
					item.remove();
				}
			});
		}

		const emptyState = document.querySelector(`[data-no-comments="${postId}"]`);
		if (emptyState) {
			emptyState.remove();
		}
	}

	function handleReactionForm(form) {
		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			const postId = form.dataset.postId;
			const formData = new FormData(form);
			try {
				const response = await fetch(form.action, {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				});
				const data = await response.json();
				if (!response.ok) {
					throw data;
				}
				updateReactionButtons(postId, data.userReaction || null);
				updateReactionSummary(postId, data.counts?.byType || {});
				updateReactionTotals(postId, data.counts?.total ?? 0);
				showToast(data.message || 'Reacción registrada');
			} catch (error) {
				const message = error?.message || 'No se pudo actualizar la reacción.';
				showToast(message, 'error');
			}
		});
	}

	function handleCommentForm(form) {
		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			const postId = form.dataset.postId;
			const formData = new FormData(form);
			const errorElement = document.querySelector(`[data-comment-error="${postId}"]`);
			try {
				const response = await fetch(form.action, {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				});
				const data = await response.json();
				if (!response.ok) {
					throw data;
				}
				if (errorElement) {
					errorElement.classList.add('hidden');
					errorElement.textContent = '';
				}
				const input = form.querySelector('[data-comment-input]');
				if (input) {
					if (input.tagName === 'TEXTAREA') {
						input.value = '';
						input.style.height = '';
					} else {
						input.value = '';
					}
				}
				addCommentToList(postId, data.comment, data.can_delete, parseInt(form.dataset.commentsLimit || '0', 10));
				updateCommentCounts(postId, data.post?.comments_count ?? 0);
				toggleViewAll(postId, data.post?.comments_count ?? 0);
				showToast(data.message || 'Comentario publicado');
			} catch (error) {
				const message = error?.errors?.content?.[0] || error?.message || 'No se pudo publicar el comentario.';
				if (errorElement) {
					errorElement.textContent = message;
					errorElement.classList.remove('hidden');
				}
				showToast(message, 'error');
			}
		});
	}

	function initPostInteractions() {
		document.querySelectorAll('.reaction-form').forEach(handleReactionForm);
		document.querySelectorAll('.comment-form').forEach(handleCommentForm);
	}

	document.addEventListener('DOMContentLoaded', initPostInteractions);
})();
</script>
@endonce

