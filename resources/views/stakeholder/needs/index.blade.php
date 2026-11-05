@extends('layouts.stakeholderdashboard')
@section('title', 'Campus Needs')
@section('active')<li class="breadcrumb-item">Campus Needs</li>@endsection
@section('content')
<div class="content-body">
    <section>
        <div class="card"><div class="card-header"><h4 class="mb-0">Campus needs</h4></div>
            <div class="card-body">
                <p class="text-muted">Add needs your campus would like to share publicly. Active needs appear on your campus page.</p>
                <div id="need-feedback" class="alert d-none" role="status"></div>
                <form id="need-form" class="border rounded p-2 mb-2" data-store-url="{{ route('stakeholders.needs.store') }}" data-update-template="{{ route('stakeholders.needs.update', ['need' => '__ID__']) }}" data-delete-template="{{ route('stakeholders.needs.destroy', ['need' => '__ID__']) }}" data-token="{{ csrf_token() }}">
                    <input type="hidden" id="need-id">
                    <div class="row">
                        <div class="form-group col-md-6"><label for="need-title">Need title</label><input id="need-title" class="form-control" maxlength="160" required></div>
                        <div class="form-group col-md-6"><label for="need-amount">Estimated amount (optional)</label><input id="need-amount" class="form-control" type="number" min="0" step="0.01" placeholder="0.00"></div>
                        <div class="form-group col-12"><label for="need-description">Details</label><textarea id="need-description" class="form-control" rows="3" maxlength="3000" required></textarea></div>
                        <div class="form-group col-12"><label class="d-flex align-items-center"><input id="need-active" type="checkbox" checked class="mr-1"> Show this need on the public campus page</label></div>
                    </div>
                    <button class="btn btn-primary" type="submit" id="need-submit">Add need</button>
                    <button class="btn btn-outline-secondary d-none" type="button" id="need-cancel">Cancel edit</button>
                </form>
                <div id="needs-list" class="list-group">
                    @forelse($needs as $need)
                        <article class="list-group-item" data-need='@json($need)'>
                            <div class="d-flex justify-content-between align-items-start flex-wrap"><div><h5 class="need-title mb-1">{{ $need->title }}</h5><p class="need-description mb-1">{{ $need->description }}</p><small class="text-muted">@if($need->amount !== null)₦{{ number_format((float)$need->amount, 2) }} · @endif{{ $need->is_active ? 'Published' : 'Hidden' }}</small></div><div><button type="button" class="btn btn-sm btn-outline-primary need-edit">Edit</button> <button type="button" class="btn btn-sm btn-outline-danger need-delete">Delete</button></div></div>
                        </article>
                    @empty
                        <p class="text-muted" id="needs-empty">No campus needs have been added yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
<script>
(() => {
    const form = document.getElementById('need-form');
    const feedback = document.getElementById('need-feedback');
    const list = document.getElementById('needs-list');
    const idInput = document.getElementById('need-id');
    const title = document.getElementById('need-title');
    const description = document.getElementById('need-description');
    const amount = document.getElementById('need-amount');
    const active = document.getElementById('need-active');
    const submit = document.getElementById('need-submit');
    const cancel = document.getElementById('need-cancel');

    function show(message, error = false) {
        feedback.textContent = message;
        feedback.className = `alert ${error ? 'alert-danger' : 'alert-success'}`;
    }
    function reset() {
        form.reset(); idInput.value = ''; active.checked = true;
        submit.textContent = 'Add need'; cancel.classList.add('d-none');
    }
    function makeCard(need) {
        const article = document.createElement('article');
        article.className = 'list-group-item';
        article.dataset.need = JSON.stringify(need);
        const wrap = document.createElement('div'); wrap.className = 'd-flex justify-content-between align-items-start flex-wrap';
        const copy = document.createElement('div');
        const heading = document.createElement('h5'); heading.className = 'need-title mb-1'; heading.textContent = need.title;
        const details = document.createElement('p'); details.className = 'need-description mb-1'; details.textContent = need.description;
        const meta = document.createElement('small'); meta.className = 'text-muted'; meta.textContent = (need.amount ? `₦${Number(need.amount).toLocaleString()} · ` : '') + (need.is_active ? 'Published' : 'Hidden');
        copy.append(heading, details, meta);
        const actions = document.createElement('div');
        const edit = document.createElement('button'); edit.type = 'button'; edit.className = 'btn btn-sm btn-outline-primary need-edit'; edit.textContent = 'Edit';
        const del = document.createElement('button'); del.type = 'button'; del.className = 'btn btn-sm btn-outline-danger need-delete ml-1'; del.textContent = 'Delete';
        actions.append(edit, del); wrap.append(copy, actions); article.append(wrap); return article;
    }
    async function send(url, method, body) {
        const response = await fetch(url, {method, headers: {'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':form.dataset.token,'X-Requested-With':'XMLHttpRequest'}, body: body ? JSON.stringify(body) : undefined});
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || Object.values(data.errors || {}).flat()[0] || 'Could not save this need.');
        return data;
    }
    form.addEventListener('submit', async event => {
        event.preventDefault();
        const id = idInput.value;
        const body = {title:title.value.trim(), description:description.value.trim(), amount:amount.value || null, is_active:active.checked};
        try {
            const data = await send(id ? form.dataset.updateTemplate.replace('__ID__', id) : form.dataset.storeUrl, id ? 'PUT' : 'POST', body);
            const old = list.querySelector(`[data-need-id="${id}"]`);
            const card = makeCard(data.need); card.dataset.needId = data.need.id;
            if (old) old.replaceWith(card); else { document.getElementById('needs-empty')?.remove(); list.prepend(card); }
            show(data.message); reset();
        } catch (error) { show(error.message, true); }
    });
    cancel.addEventListener('click', reset);
    list.addEventListener('click', async event => {
        const card = event.target.closest('[data-need]'); if (!card) return;
        const need = JSON.parse(card.dataset.need);
        if (event.target.closest('.need-edit')) {
            idInput.value = need.id; title.value = need.title; description.value = need.description || ''; amount.value = need.amount || ''; active.checked = Boolean(need.is_active);
            submit.textContent = 'Save changes'; cancel.classList.remove('d-none'); title.focus(); return;
        }
        if (event.target.closest('.need-delete') && window.confirm('Delete this campus need?')) {
            try { const data = await send(form.dataset.deleteTemplate.replace('__ID__', need.id), 'DELETE'); card.remove(); show(data.message); }
            catch (error) { show(error.message, true); }
        }
    });
    list.querySelectorAll('[data-need]').forEach(card => { card.dataset.needId = JSON.parse(card.dataset.need).id; });
})();
</script>
@endsection
