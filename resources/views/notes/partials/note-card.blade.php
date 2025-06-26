<div class="note-card bg-white rounded-lg shadow overflow-hidden fade-in" style="background-color: {{ $note->color }};">
    <div class="p-6">
        <div class="flex justify-between items-start mb-2">
            <!-- Title with toggle button -->
            <div class="flex items-center space-x-2">
                <h3 class="text-xl font-semibold break-words">{{ $note->title }}</h3>
                <button type="button" onclick="toggleNotePreview(this)"
                    class="text-gray-500 hover:text-blue-500 text-sm">
                    <span class="preview-text">Show preview</span>
                    <span class="full-text hidden">Hide preview</span>
                </button>
            </div>

            <form action="{{ route('notes.pin', $note) }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-yellow-500">
                    <i class="fas fa-thumbtack {{ $note->pinned ? 'text-yellow-500' : '' }}"></i>
                </button>
            </form>
        </div>

        <!-- Content area (hidden by default) -->
        <div class="note-content hidden">
            <p class="text-gray-700 mb-4 whitespace-pre-wrap break-words">{{ $note->content }}</p>

            <div class="flex justify-between items-center text-sm text-gray-500">
                <span>{{ $note->updated_at->diffForHumans() }}</span>
                <div class="flex space-x-2">
                    <!-- Edit Button -->
                    <button onclick="openEditModal({{ $note->id }})"
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
</div>

<script>
    function toggleNotePreview(button) {
        // Only toggle the content for the clicked card
        const noteCard = button.closest('.note-card');
        const content = noteCard.querySelector('.note-content');
        const previewText = button.querySelector('.preview-text');
        const fullText = button.querySelector('.full-text');

        content.classList.toggle('hidden');
        previewText.classList.toggle('hidden');
        fullText.classList.toggle('hidden');
    }
</script>

<style>
    .hidden {
        display: none;
    }

    .note-content {
        transition: all 0.3s ease;
    }
</style>