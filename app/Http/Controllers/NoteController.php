<?php

// app/Http/Controllers/NoteController.php
namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- Add this line

class NoteController extends Controller
{
    use AuthorizesRequests; // <-- Add this line

    public function index(Request $request)
    {
        // Get the authenticated user's notes
        $query = Auth::user()->notes();

        // Search functionality
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by color
        if ($request->has('color')) {
            $query->where('color', $request->color);
        }

        // Sort functionality
        if ($request->has('sort')) {
            if ($request->sort == 'oldest') {
                $query->oldest();
            } elseif ($request->sort == 'title') {
                $query->orderBy('title');
            }
        } else {
            // Default sorting (newest first)
            $query->latest();
        }

        // Get pinned notes first, then others
        $notes = $query->get();

        return view('notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'required|string',
        ]);

        Auth::user()->notes()->create($request->all());

        return redirect()->route('notes.index')
            ->with('success', 'Note created successfully.');
    }

    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'required|string',
        ]);

        $note->update($request->all());

        return redirect()->route('notes.index')
            ->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);

        $note->delete();

        return redirect()->route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }

    public function pin(Note $note)
    {
        $this->authorize('update', $note);

        $note->update(['pinned' => !$note->pinned]);

        return back()->with('success', 'Note pin status updated.');
    }
}
