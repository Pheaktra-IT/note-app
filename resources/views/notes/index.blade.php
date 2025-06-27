@extends('layouts.app')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold mb-6 text-blue-700 ">QuickNotes</h1>
    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('notes.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-500 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Search notes...">
            </div>

            <div class="">
                <label for="color" class="block text-sm font-medium text-gray-500 mb-1">Color</label>
                <select name="color" id="color" class="px-8 py-2  border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" >colors</option>
                    <option value="#ffffff" {{ request('color') == '#ffffff' ? 'selected' : '' }}>White</option>
                    <option value="#ffdddd" {{ request('color') == '#ffdddd' ? 'selected' : '' }}>Red</option>
                    <option value="#ddffdd" {{ request('color') == '#ddffdd' ? 'selected' : '' }}>Green</option>
                    <option value="#ddddff" {{ request('color') == '#ddddff' ? 'selected' : '' }}>Blue</option>
                    <option value="#ffffdd" {{ request('color') == '#ffffdd' ? 'selected' : '' }}>Yellow</option>
                </select>
            </div>

            <div>
                <label for="sort" class="block text-sm font-medium text-gray-500 mb-1">Sort By</label>
                <select name="sort" id="sort" class="px-8 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title (A-Z)</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Apply
            </button>

            <a href="{{ route('notes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Create Note Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6 fade-in">
        <h2 class="text-xl font-semibold mb-4">Create New Note</h2>
        <form action="{{ route('notes.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <input type="text" name="title" placeholder="Title" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <textarea name="content" rows="3" placeholder="Content" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <div class="flex items-center">
                    @foreach(['#ffffff', '#ffdddd', '#ddffdd', '#ddddff', '#ffffdd'] as $color)
                    <label class="color-option {{ $loop->first ? 'selected' : '' }}"
                        style="background-color: {{ $color }};">
                        <input type="radio" name="color" value="{{ $color }}"
                            {{ $loop->first ? 'checked' : '' }} class="hidden">
                    </label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Save Note
            </button>
        </form>
    </div>

    <!-- Notes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($notes->where('pinned', true) as $note)
        @include('notes.partials.note-card', ['note' => $note])
        @endforeach

        @foreach($notes->where('pinned', false) as $note)
        @include('notes.partials.note-card', ['note' => $note])
        @endforeach
    </div>

    @if($notes->isEmpty())
    <div class="text-center py-12">
        <p class="text-gray-500">No notes found. Create your first note above!</p>
    </div>
    @endif
</div>

<script>
    // Color selection (keep this part the same)
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
            this.querySelector('input').checked = true;
        });
    });

    // Auto-save draft 
    const form = document.getElementById('note-form');
    const titleInput = form.querySelector('input[name="title"]');
    const contentInput = form.querySelector('textarea[name="content"]');
    const colorInput = form.querySelector('input[name="color"]:checked');

    // Rest of your JavaScript remains the same...
    function saveDraft() {
        const draft = {
            title: titleInput.value,
            content: contentInput.value,
            color: colorInput.value
        };
        localStorage.setItem('noteDraft', JSON.stringify(draft));
    }

    function loadDraft() {
        const draft = JSON.parse(localStorage.getItem('noteDraft'));
        if (draft) {
            titleInput.value = draft.title || '';
            contentInput.value = draft.content || '';
            document.querySelector(`input[name="color"][value="${draft.color || '#ffffff'}"]`).checked = true;
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.toggle('selected', opt.querySelector('input').value === (draft.color || '#ffffff'));
            });
        }
    }

    function clearDraft() {
        localStorage.removeItem('noteDraft');
        titleInput.value = '';
        contentInput.value = '';
        document.querySelector('input[name="color"][value="#ffffff"]').checked = true;
        document.querySelectorAll('.color-option').forEach(opt => {
            opt.classList.toggle('selected', opt.querySelector('input').value === '#ffffff');
        });
    }

    titleInput.addEventListener('input', saveDraft);
    contentInput.addEventListener('input', saveDraft);
    form.querySelectorAll('input[name="color"]').forEach(input => {
        input.addEventListener('change', saveDraft);
    });

    document.addEventListener('DOMContentLoaded', loadDraft);

    form.addEventListener('submit', function() {
        setTimeout(clearDraft, 1000);
    });

    const clearButton = document.createElement('button');
    clearButton.type = 'button';
    clearButton.className = 'px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition ml-3';
    clearButton.textContent = 'Clear Draft';
    clearButton.addEventListener('click', clearDraft);
    form.querySelector('button[type="submit"]').insertAdjacentElement('afterend', clearButton);
</script>
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            timer: 1800,
            showConfirmButton: false
        });
    });
</script>
@endif
@endsection