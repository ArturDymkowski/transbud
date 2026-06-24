<?php

namespace App\Http\Controllers\Page;

use App\Enums\CountriesEnum;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.drivers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver $driver)
    {
        return view('pages.drivers.edit', ['driver' => $driver]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'pesel' => 'required|string|size:11|unique:drivers,pesel,' . ($driver->id),
            'country' => ['nullable', new Enum(CountriesEnum::class)],
            'region' => 'nullable|string|max:100',
            'zipcode' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:100',
            'street_nr' => 'nullable|string|max:20',
            'home_nr' => 'nullable|string|max:20',
            'extra_info' => 'nullable|string',
            'driving_license_number' => 'required|string|unique:drivers,driving_license_number,' . ($driver->id),
            'driving_license_expiry_date' => 'required|date',
            'medical_exam_expiry_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.index')->with('success', 'Kierowca zaktualizowany!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
