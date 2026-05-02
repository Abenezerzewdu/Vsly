<?php

namespace App\Http\Controllers;

use App\Models\Take;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TakeController extends Controller
{
   

    //  List
    public function index()
    {
        $takes = Take::query()
            ->with('user:id,name')
            ->latest()
            ->paginate(10)
            ->through(fn ($take) => [
                'id' => $take->id,
                'content' => $take->content,
                'created_at' => $take->created_at->diffForHumans(),
                'user' => [
                    'id' => $take->user->id,
                    'name' => $take->user->name,
                ],
            ]);

        return Inertia::render('Takes/Index', [
            'takes' => $takes
        ]);
    }

    //  Create page (optional)
    public function create()
    {
        return Inertia::render('Takes/Create');
    }

    //  Store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:280']
        ]);

        Take::create([
            'user_id' => $request->user()->id,
            'content' => $validated['content']
        ]);

        return redirect()
            ->route('takes.index')
            ->with('success', 'Take posted.');
    }

    // Show single take
    public function show(Take $take)
    {
        $take->load('user:id,name', 'duels');

        return Inertia::render('Takes/Show', [
            'take' => [
                'id' => $take->id,
                'content' => $take->content,
                'created_at' => $take->created_at->diffForHumans(),
                'user' => $take->user,
                'duels_count' => $take->duels->count()
            ]
        ]);
    }

    //  Edit page
    public function edit(Take $take)
    {
        $this->authorize('update', $take);

        return Inertia::render('Takes/Edit', [
            'take' => $take
        ]);
    }

    //  Update
    public function update(Request $request, Take $take)
    {
        $this->authorize('update', $take);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:280']
        ]);

        $take->update($validated);

        return redirect()
            ->route('takes.show', $take)
            ->with('success', 'Take updated.');
    }

    // Delete
    public function destroy(Take $take)
    {
        $this->authorize('delete', $take);

        $take->delete();

        return redirect()
            ->route('takes.index')
            ->with('success', 'Take deleted.');
    }
}
