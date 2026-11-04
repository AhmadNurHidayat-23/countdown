<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountdownRequest;
use App\Models\Countdown;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CountdownController extends Controller
{
    public function index(): View
    {
        return view('countdowns.index', ['countdowns' => Countdown::latest('id')->paginate(10)]);
    }

    public function store(CountdownRequest $request): RedirectResponse
    {
        Countdown::create($request->countdownData());

        return to_route('countdowns.index')->with('status', 'Countdown berhasil dibuat. Siap menantikan momenmu!');
    }

    public function show(Countdown $countdown): View
    {
        return view('countdowns.show', compact('countdown'));
    }

    public function edit(Countdown $countdown): View
    {
        return view('countdowns.edit', compact('countdown'));
    }

    public function update(CountdownRequest $request, Countdown $countdown): RedirectResponse
    {
        $countdown->update($request->countdownData());

        return to_route('countdowns.index')->with('status', 'Countdown berhasil diperbarui.');
    }

    public function destroy(Countdown $countdown): RedirectResponse
    {
        $countdown->delete();

        return to_route('countdowns.index')->with('status', 'Countdown telah dihapus.');
    }
}
