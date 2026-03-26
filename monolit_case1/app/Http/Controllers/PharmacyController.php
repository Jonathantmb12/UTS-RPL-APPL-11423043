<?php

namespace App\Http\Controllers;

use App\Models\PharmacyInventory;
use App\Models\PrescriptionOrder;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    /**
     * Get pharmacy inventory
     */
    public function inventory(Request $request)
    {
        if ($request->user()->role !== 'pharmacist') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = PharmacyInventory::where('pharmacy_id', $request->user()->id);

        if ($request->status === 'low-stock') {
            $query->lowStock();
        }

        $inventory = $query->paginate(20);

        return response()->json($inventory);
    }

    /**
     * Add medication to inventory
     */
    public function addMedication(Request $request)
    {
        if ($request->user()->role !== 'pharmacist') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'medication_name' => 'required|string',
            'generic_name' => 'string',
            'sku' => 'required|unique:pharmacy_inventory',
            'stock_quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'integer|min:0',
            'batch_number' => 'string',
            'expiration_date' => 'date',
            'manufacturer' => 'string',
        ]);

        $medication = PharmacyInventory::create([
            'pharmacy_id' => $request->user()->id,
            'medication_name' => $validated['medication_name'],
            'generic_name' => $validated['generic_name'] ?? null,
            'sku' => $validated['sku'],
            'stock_quantity' => $validated['stock_quantity'],
            'unit_price' => $validated['unit_price'],
            'reorder_level' => $validated['reorder_level'] ?? 10,
            'batch_number' => $validated['batch_number'] ?? null,
            'expiration_date' => $validated['expiration_date'] ?? null,
            'manufacturer' => $validated['manufacturer'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Medication added successfully',
            'medication' => $medication,
        ], 201);
    }

    /**
     * Update medication stock
     */
    public function updateStock(Request $request, PharmacyInventory $medication)
    {
        if ($request->user()->id !== $medication->pharmacy_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'reason' => 'string',
        ]);

        $medication->update(['stock_quantity' => $validated['stock_quantity']]);

        return response()->json([
            'message' => 'Stock updated successfully',
            'medication' => $medication,
        ]);
    }

    /**
     * Get prescription orders for pharmacy
     */
    public function prescriptionOrders(Request $request)
    {
        if ($request->user()->role !== 'pharmacist') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = PrescriptionOrder::where('pharmacy_id', $request->user()->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->with(['prescription', 'patient'])
            ->paginate(20);

        return response()->json($orders);
    }

    /**
     * Update prescription order status
     */
    public function updateOrderStatus(Request $request, PrescriptionOrder $order)
    {
        if ($request->user()->id !== $order->pharmacy_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,ready,picked_up,cancelled',
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'ready') {
            $updateData['ready_date'] = now();
        } elseif ($validated['status'] === 'picked_up') {
            $updateData['picked_up_date'] = now();
        }

        $order->update($updateData);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order,
        ]);
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts(Request $request)
    {
        if ($request->user()->role !== 'pharmacist') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $alerts = PharmacyInventory::where('pharmacy_id', $request->user()->id)
            ->lowStock()
            ->get();

        return response()->json($alerts);
    }
}
