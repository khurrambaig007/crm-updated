<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataTables\StampsDataTable;
use App\Http\Requests\Stamp\StoreStampRequest;
use App\Http\Requests\Stamp\UpdateStampRequest;
use App\Models\Stamp;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StampController extends Controller
{
    /**
     * Directory on the public disk where stamp images are stored.
     */
    private const IMAGE_DIRECTORY = 'stamps';

    public function index(Request $request, StampsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('stamps.index');
    }

    public function create(): View
    {
        return view('stamps.create');
    }

    public function store(StoreStampRequest $request): RedirectResponse
    {
        $stamp = new Stamp($request->safe()->only('name'));
        $stamp->image = $request->file('image')->store(self::IMAGE_DIRECTORY, 'public');
        $stamp->save();

        return redirect()->route('stamps.index')->with('status', 'Stamp created successfully.');
    }

    public function edit(Stamp $stamp): View
    {
        return view('stamps.edit', [
            'stamp' => $stamp,
        ]);
    }

    public function update(UpdateStampRequest $request, Stamp $stamp): RedirectResponse
    {
        $stamp->name = $request->validated('name');

        $this->syncImage($request, $stamp);
        $stamp->save();

        return redirect()->route('stamps.index')->with('status', 'Stamp updated successfully.');
    }

    public function destroy(Request $request, Stamp $stamp): RedirectResponse
    {
        try {
            $this->deleteImageFile($stamp->image);
            $stamp->delete();
        } catch (QueryException $e) {
            return redirect()->route('stamps.index')->with('error', 'This stamp is in use and cannot be deleted.');
        }

        return redirect()->route('stamps.index')->with('status', 'Stamp deleted successfully.');
    }

    /**
     * Apply the uploaded image, or the remove flag, to the stamp.
     *
     * A new upload always wins over the remove flag: replacing a file and
     * clearing it in the same request is contradictory, and keeping the new
     * image is the less destructive reading.
     */
    private function syncImage(UpdateStampRequest $request, Stamp $stamp): void
    {
        if ($request->hasFile('image')) {
            $this->deleteImageFile($stamp->image);
            $stamp->image = $request->file('image')->store(self::IMAGE_DIRECTORY, 'public');

            return;
        }

        if ($request->boolean('remove_image')) {
            $this->deleteImageFile($stamp->image);
            $stamp->image = null;
        }
    }

    /**
     * Delete a stored image from the public disk, ignoring a missing file so a
     * stale row never blocks the save.
     */
    private function deleteImageFile(?string $path): void
    {
        if (filled($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
