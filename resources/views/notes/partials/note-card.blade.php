<style>
    .hidden {
        display: none;
    }

    .note-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07), 0 1.5px 6px rgba(0, 0, 0, 0.03);
        transition: box-shadow 0.3s, transform 0.3s, max-height 0.3s, padding 0.3s;
        max-height: 120px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        padding: 0;
        position: relative;
    }

    .note-card:hover {
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.13), 0 3px 12px rgba(0, 0, 0, 0.07);
        transform: translateY(-2px) scale(1.01);
    }

    .note-card.expanded {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
        transform: scale(1.03);
        max-height: 900px;
        padding-bottom: 1rem;
        z-index: 10;
        overflow: visible;
    }

    .note-content {
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .color-option {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border 0.2s;
    }

    .color-option.selected {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px #93c5fd;
    }

    /* Button styles */
    .note-card button,
    .note-card form button {
        outline: none;
        border: none;
        background: none;
        cursor: pointer;
        transition: color 0.2s;
    }

    .note-card .text-blue-600:hover,
    .note-card .text-blue-600:focus {
        color: #2563eb;
    }

    .note-card .text-red-600:hover,
    .note-card .text-red-600:focus {
        color: #dc2626;
    }

    /* Responsive padding for card content */
    .note-card .p-6 {
        padding: 1.5rem;
    }

    .preview-toggle-btn {
        background: none;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 0;
    }

    .preview-toggle-btn:hover {
        background: #e0edff;
    }

    .chevron-icon {
        color: #2563eb;
        transform: rotate(0deg);
    }

    .note-card.expanded .chevron-icon {
        transform: rotate(180deg);
    }
</style>
<div class="note-card bg-white rounded-lg shadow overflow-hidden fade-in" style="background-color: {{ $note->color }};">
    <div class="p-5">
        <div class="flex justify-between items-center mb-2">
            <!-- Title with toggle button -->
            <div class="flex items-center space-x-2">
                <h3 class="text-xl font-semibold break-words">{{ $note->title }}</h3>
                <button type="button" onclick="toggleNotePreview(this)"
                    class="preview-toggle-btn flex items-center justify-center w-8 h-8 rounded-full transition hover:bg-blue-50">
                    <svg class="chevron-icon transition-transform duration-200" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('notes.pin', $note) }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-yellow-500">
                    <i class="fas fa-thumbtack {{ $note->pinned ? 'text-yellow-500' : '' }}"></i>
                </button>
            </form>
        </div>

        <!-- Only the content part will be toggled -->
        <div class="note-content hidden">
            <p class="text-gray-700 mb-4 whitespace-pre-wrap break-words">{{ $note->content }}</p>
        </div>

        <!-- These buttons remain always visible -->
        <div class="flex justify-between items-center text-sm text-gray-500">
            <span>{{ $note->updated_at->diffForHumans() }}</span>
            <div class="flex space-x-2">
                <!-- Edit Button -->
                <button onclick="openEditModal('editModal{{ $note->id }}')"
                    class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-edit"></i>
                </button>

                <!-- Delete Button -->
                <form action="{{ route('notes.destroy', $note) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this note?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal for this specific note -->
<div id="editModal{{ $note->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Edit Note</h3>
            <button onclick="closeEditModal('editModal{{ $note->id }}')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('notes.update', $note) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <input type="text" name="title" value="{{ $note->title }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <textarea name="content" rows="5" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $note->content }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <div class="flex items-center">
                    @foreach(['#ffffff', '#ffdddd', '#ddffdd', '#ddddff', '#ffffdd'] as $color)
                    <label class="color-option {{ $note->color == $color ? 'selected' : '' }}"
                        style="background-color: {{ $color }};">
                        <input type="radio" name="color" value="{{ $color }}"
                            {{ $note->color == $color ? 'checked' : '' }} class="hidden">
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal('editModal{{ $note->id }}')"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleNotePreview(button) {
        const noteCard = button.closest('.note-card');
        const content = noteCard.querySelector('.note-content');
        content.classList.toggle('hidden');
        noteCard.classList.toggle('expanded');
    }

    function openEditModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
    }

    function closeEditModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = ''; // Restore scrolling
    }
</script>