<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CargoCompany;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CargoCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cargoCompanies = CargoCompany::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.cargo-companies.index', compact('cargoCompanies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cargo-companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:cargo_companies,code',
            'api_url' => 'nullable|url',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'description' => 'nullable|string'
        ]);

        CargoCompany::create($request->all());

        return redirect()->route('admin.cargo-companies.index')
            ->with('success', 'Kargo şirketi başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CargoCompany $cargoCompany)
    {
        $cargoCompany->load(['orders' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.cargo-companies.show', compact('cargoCompany'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CargoCompany $cargoCompany)
    {
        return view('admin.cargo-companies.edit', compact('cargoCompany'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CargoCompany $cargoCompany)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('cargo_companies', 'code')->ignore($cargoCompany->id)],
            'api_url' => 'nullable|url',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'description' => 'nullable|string'
        ]);

        $cargoCompany->update($request->all());

        return redirect()->route('admin.cargo-companies.index')
            ->with('success', 'Kargo şirketi başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CargoCompany $cargoCompany)
    {
        // Kargo şirketinin siparişleri varsa silme
        if ($cargoCompany->orders()->count() > 0) {
            return back()->with('error', 'Bu kargo şirketinin siparişleri bulunduğu için silinemez.');
        }

        $cargoCompany->delete();

        return redirect()->route('admin.cargo-companies.index')
            ->with('success', 'Kargo şirketi başarıyla silindi.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(CargoCompany $cargoCompany)
    {
        $cargoCompany->update(['is_active' => !$cargoCompany->is_active]);
        
        $status = $cargoCompany->is_active ? 'aktif' : 'pasif';
        return back()->with('success', "Kargo şirketi {$status} olarak işaretlendi.");
    }
}
