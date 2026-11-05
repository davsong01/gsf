@if($post->allow_comments && !($isPreview ?? false))
<section id="comments-section" style="margin-top:64px;padding-top:32px;border-top:1px solid var(--line)">
    <div class="eyebrow"><span></span> Join the conversation</div>
    <h2 style="margin:12px 0 24px">Comments (<span data-comment-count>{{ number_format($post->approved_comments_count ?? 0) }}</span>)</h2>
    @include('includes.alerts')
    @if($approvedComments->isNotEmpty())
        <div class="blog-comments-list" aria-label="Approved comments">
            @foreach($approvedComments as $approvedComment)
                <article class="blog-comment">
                    <div class="blog-comment__byline"><span>{{ $approvedComment->name ?: 'GSF reader' }}</span>@if($approvedComment->chapter)<span class="blog-comment__campus">{{ $approvedComment->chapter->name }}</span>@endif<span class="blog-comment__date">{{ $approvedComment->created_at?->format('d M Y') }}</span></div>
                    <p class="blog-comment__body">{{ $approvedComment->body }}</p>
                </article>
            @endforeach
        </div>
        @if(($post->approved_comments_count ?? 0) > $approvedComments->count())<p class="blog-comments-empty">Showing the latest {{ $approvedComments->count() }} comments.</p>@endif
    @else
        <p class="blog-comments-empty" id="blog-comments-empty">No comments yet. Be the first to join the conversation.</p>
    @endif
    <h3 class="blog-comment-form-heading" id="comment-form-heading">Add a comment</h3>
    <style>
        #blog-comment-form { width:100%; max-width:760px !important; margin-inline:0; }
        .blog-comment-form__grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px 20px; }
        .blog-comment-form__field { display:flex; flex-direction:column; align-items:stretch; gap:7px; min-width:0; }
        .blog-comment-form__field label { display:block; margin:0; font-size:16px; font-weight:600; }
        #blog-comment-form .blog-comment-form__field input, #blog-comment-form .blog-comment-form__field textarea, #blog-comment-form .blog-comment-form__field select { display:block; width:100%; max-width:100%; box-sizing:border-box; }
        #blog-comment-form input.form-control, #blog-comment-form select.form-control { height:44px; min-height:44px; padding:9px 12px; font-size:14px; }
        #blog-comment-form textarea#comment-body { height:112px; min-height:112px; max-height:190px; padding:11px 12px; font-size:14px; resize:vertical; }
        .blog-comment-form__wide { grid-column:1 / -1; }
        .blog-comment-form .select2-container { width:100% !important; }
        .blog-comment-form .select2-selection--single { height:44px; border:1px solid #ced4da; border-radius:6px; }
        .blog-comment-form .select2-selection__rendered { line-height:42px !important; padding-left:12px !important; }
        .blog-comment-form .select2-selection__arrow { height:42px !important; }
        @media (max-width:600px) { #blog-comment-form { max-width:100% !important; } .blog-comment-form__grid { grid-template-columns:1fr; gap:14px; } .blog-comment-form__wide { grid-column:auto; } }
    </style>
    <form id="blog-comment-form" class="blog-comment-form" method="POST" action="{{ route('blog.comments.store', $post) }}">
        @csrf
        <input type="hidden" name="math_token" value="{{ $mathToken }}">
        <div class="blog-comment-form__grid">
            <div class="blog-comment-form__field">
                <label for="comment-name">Name <span aria-hidden="true">*</span></label>
                <input class="form-control" id="comment-name" name="name" value="{{ old('name') }}" autocomplete="name" required>
                @error('name')<small class="text-danger" data-comment-error="name">{{ $message }}</small>@enderror
            </div>
            <div class="blog-comment-form__field">
                <label for="comment-email">Email <span class="text-muted">(optional)</span></label>
                <input class="form-control" id="comment-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email">
                @error('email')<small class="text-danger" data-comment-error="email">{{ $message }}</small>@enderror
            </div>
            <div class="blog-comment-form__field blog-comment-form__wide">
                <label for="comment-campus">Campus <span class="text-muted">(optional)</span></label>
                <select class="form-control blog-campus-select" id="comment-campus" name="chapter_id" data-placeholder="Search for your campus">
                    <option value="">Search for your campus</option>
                    @foreach($campuses as $campus)<option value="{{ $campus->id }}" @selected(old('chapter_id') == $campus->id)>{{ $campus->name }}</option>@endforeach
                </select>
                @error('chapter_id')<small class="text-danger" data-comment-error="chapter_id">{{ $message }}</small>@enderror
            </div>
            <div class="blog-comment-form__field blog-comment-form__wide">
                <label for="comment-body">Comment</label>
                <textarea class="form-control" id="comment-body" name="body" rows="5" required>{{ old('body') }}</textarea>
                @error('body')<small class="text-danger" data-comment-error="body">{{ $message }}</small>@enderror
            </div>
            <div class="blog-comment-form__field">
                <label id="comment-math-label" for="comment-math">Solve this: {{ $mathQuestion }} = ?</label>
                <input class="form-control" id="comment-math" type="number" name="math_answer" inputmode="numeric" required>
                @error('math_answer')<small class="text-danger" data-comment-error="math_answer">{{ $message }}</small>@enderror
            </div>
        </div>
        <p id="blog-comment-feedback" class="blog-comment-feedback" role="status" aria-live="polite" hidden></p>
        <button id="blog-comment-submit" class="button button--gold" type="submit" style="margin-top:20px">Submit comment</button>
    </form>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.blog-comments-link').forEach(function (link) {
        link.addEventListener('click', function (event) {
            var commentField = document.getElementById('comment-body');
            if (!commentField) return;
            event.preventDefault();
            commentField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            commentField.focus({ preventScroll: true });
            history.replaceState(null, '', '#comment-body');
        });
    });

    if (window.jQuery && window.jQuery.fn.select2) {
        window.jQuery('.blog-campus-select').select2({
            width: '100%',
            placeholder: 'Search for your campus',
            allowClear: true
        });
    }

    var commentForm = document.getElementById('blog-comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            var submit = document.getElementById('blog-comment-submit');
            var feedback = document.getElementById('blog-comment-feedback');
            var mathLabel = document.getElementById('comment-math-label');
            var mathInput = document.getElementById('comment-math');
            var tokenInput = commentForm.querySelector('input[name="math_token"]');
            commentForm.querySelectorAll('[data-comment-error]').forEach(function (error) { error.remove(); });
            feedback.hidden = true;
            submit.disabled = true;
            submit.textContent = 'Submitting…';

            try {
                var response = await fetch(commentForm.action, {
                    method: 'POST',
                    body: new FormData(commentForm),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': commentForm.querySelector('input[name="_token"]').value
                    },
                    credentials: 'same-origin'
                });
                var result = await response.json();

                if (result.challenge) {
                    tokenInput.value = result.challenge.token;
                    mathLabel.textContent = 'Solve this: ' + result.challenge.question + ' = ?';
                    mathInput.value = '';
                }

                if (!response.ok) {
                    Object.keys(result.errors || {}).forEach(function (field) {
                        var control = commentForm.querySelector('[name="' + field + '"]');
                        if (!control) return;
                        var message = document.createElement('small');
                        message.className = 'text-danger';
                        message.dataset.commentError = field;
                        message.textContent = result.errors[field][0];
                        control.closest('.blog-comment-form__field').appendChild(message);
                    });
                    feedback.textContent = result.message || 'Please check the highlighted fields and try again.';
                    feedback.hidden = false;
                    feedback.classList.add('is-error');
                    return;
                }

                feedback.textContent = result.message;
                feedback.hidden = false;
                feedback.classList.remove('is-error');
                commentForm.reset();
                if (window.jQuery && window.jQuery.fn.select2) window.jQuery('#comment-campus').val('').trigger('change');
                tokenInput.value = result.challenge.token;
                mathLabel.textContent = 'Solve this: ' + result.challenge.question + ' = ?';
                mathInput.value = '';

                document.querySelectorAll('[data-comment-count]').forEach(function (count) {
                    count.textContent = Number(result.approved_comments_count).toLocaleString();
                });

                if (result.status === 'approved') {
                    var list = document.querySelector('.blog-comments-list');
                    if (!list) {
                        list = document.createElement('div');
                        list.className = 'blog-comments-list';
                        list.setAttribute('aria-label', 'Approved comments');
                        var empty = document.getElementById('blog-comments-empty');
                        if (empty) empty.replaceWith(list);
                        else document.getElementById('comment-form-heading').before(list);
                    }
                    var item = document.createElement('article');
                    item.className = 'blog-comment';
                    var byline = document.createElement('div');
                    byline.className = 'blog-comment__byline';
                    var name = document.createElement('span');
                    name.textContent = result.comment.name;
                    byline.appendChild(name);
                    if (result.comment.campus) {
                        var campus = document.createElement('span');
                        campus.className = 'blog-comment__campus';
                        campus.textContent = result.comment.campus;
                        byline.appendChild(campus);
                    }
                    var date = document.createElement('span');
                    date.className = 'blog-comment__date';
                    date.textContent = result.comment.date;
                    byline.appendChild(date);
                    var body = document.createElement('p');
                    body.className = 'blog-comment__body';
                    body.textContent = result.comment.body;
                    item.append(byline, body);
                    list.prepend(item);
                }
            } catch (error) {
                feedback.textContent = 'We could not submit your comment. Please try again.';
                feedback.hidden = false;
                feedback.classList.add('is-error');
            } finally {
                submit.disabled = false;
                submit.textContent = 'Submit comment';
            }
        });
    }
});
</script>
@endpush
@endif
