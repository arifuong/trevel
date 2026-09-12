<?php

namespace App\Http\Controllers\Admin;

use App\Models\Airline;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AirlineController extends Controller
{
    public function index(Request $request)
    {
        $query = Airline::withCount('variants')->latest();

        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $airlines = $query->paginate(10)->withQueryString();

        return view('admin.airlines.index', compact('airlines'));
    }

    public function create()
    {
        return view('admin.airlines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('airlines/logos', 'public');
        }

        $airline = Airline::create($validated);

        return redirect()->route('admin.airlines.index')
            ->with('success', "Maskapai '{$airline->name}' berhasil ditambahkan.");
    }

    public function edit(Airline $airline)
    {
        return view('admin.airlines.edit', compact('airline'));
    }

    public function update(Request $request, Airline $airline)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($airline->logo && Storage::disk('public')->exists($airline->logo)) {
                Storage::disk('public')->delete($airline->logo);
            }
            $validated['logo'] = $request->file('logo')->store('airlines/logos', 'public');
        }

        $airline->update($validated);

        return redirect()->route('admin.airlines.index')
            ->with('success', "Maskapai '{$airline->name}' berhasil diperbarui.");
    }

    public function destroy(Airline $airline)
    {
        if ($airline->is_in_use) {
            return back()->with('error', 'Maskapai tidak dapat dihapus karena sedang digunakan oleh sub-paket aktif.');
        }

        if ($airline->logo && Storage::disk('public')->exists($airline->logo)) {
            Storage::disk('public')->delete($airline->logo);
        }

        $airline->delete();

        return redirect()->route('admin.airlines.index')
            ->with('success', "Maskapai '{$airline->name}' berhasil dihapus.");
    }

    /**
     * API endpoint for multi-select search in variant forms.
     */
    public function search(Request $request)
    {
        $query = Airline::active()->orderBy('sort_order');

        if ($search = $request->q) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $airlines = $query->limit(20)->get()->map(function ($airline) {
            return [
                'id' => $airline->id,
                'name' => $airline->name,
                'code' => $airline->code,
                'display_name' => $airline->display_name,
                'logo' => $airline->logo ? Storage::url($airline->logo) : null,
            ];
        });

        return response()->json($airlines);
    }
}
