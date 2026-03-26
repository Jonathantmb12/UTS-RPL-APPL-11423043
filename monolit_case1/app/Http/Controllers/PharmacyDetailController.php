<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PharmacyInventory;
use App\Models\PrescriptionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PharmacyDetailController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);

            $pharmacies = User::where('role', 'pharmacist')
                ->where('is_active', true)
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Pharmacies retrieved successfully',
                'data' => $pharmacies,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving pharmacies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $pharmacy = User::where('id', $id)
                ->where('role', 'pharmacist')
                ->firstOrFail();

            $inventory = PharmacyInventory::where('pharmacy_id', $id)
                ->where('is_active', true)
                ->get();

            $orders = PrescriptionOrder::where('pharmacy_id', $id)->count();

            // ✅ FIXED HERE
            $lowStock = PharmacyInventory::where('pharmacy_id', $id)
                ->whereColumn('stock_quantity', '<', 'reorder_level')
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Pharmacy details retrieved',
                'data' => [
                    'pharmacy' => $pharmacy,
                    'inventory_count' => $inventory->count(),
                    'total_orders' => $orders,
                    'low_stock_items' => $lowStock,
                    'inventory' => $inventory,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pharmacy not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

 public function getInventory()
{
    try {
        $pharmacyId = auth()->user()->id;

        $inventory = PharmacyInventory::where('pharmacy_id', $pharmacyId)
            ->where('is_active', true)
            ->orderBy('medication_name')
            ->get();

        // 🔥 UBAH KE VIEW
        return view('pharmacy.inventory', compact('inventory'));

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}

public function getLowStock()
{
    try {
        $pharmacyId = auth()->user()->id;

        $items = PharmacyInventory::where('pharmacy_id', $pharmacyId)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->get();

        return view('pharmacy.low-stock', compact('items'));

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
    public function search(Request $request)
    {
        try {
            $search = $request->input('q', '');
            $pharmacyId = $request->input('pharmacy_id');

            $query = PharmacyInventory::where('is_active', true);

            if ($pharmacyId) {
                $query->where('pharmacy_id', $pharmacyId);
            }

            // ✅ FIXED QUERY LOGIC
            $medications = $query->where(function ($q) use ($search) {
                $q->where('medication_name', 'like', "%$search%")
                  ->orWhere('generic_name', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%");
            })->get();

            return response()->json([
                'success' => true,
                'message' => 'Search results retrieved',
                'data' => $medications,
                'count' => $medications->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching medications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}