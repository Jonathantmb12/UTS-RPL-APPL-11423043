<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\InsuranceClaim;
use App\Models\PrescriptionOrder;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get payment history
     */
    public function history(Request $request)
    {
        $query = Payment::where('patient_id', $request->user()->id);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(20);

        return response()->json($payments);
    }

    /**
     * Create payment for prescription order
     */
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'prescription_order_id' => 'required|exists:prescription_orders,id',
            'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,insurance,cash',
            'insurance_coverage' => 'numeric|min:0',
        ]);

        $order = PrescriptionOrder::findOrFail($validated['prescription_order_id']);

        if ($order->patient_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $insuranceCoverage = $validated['insurance_coverage'] ?? 0;
        $patientPayment = $order->total_price - $insuranceCoverage;

        $payment = Payment::create([
            'patient_id' => $request->user()->id,
            'payable_type' => PrescriptionOrder::class,
            'payable_id' => $order->id,
            'transaction_id' => 'TXN-' . uniqid(),
            'amount' => $order->total_price,
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'insurance_coverage' => $insuranceCoverage,
            'patient_payment' => $patientPayment,
        ]);

        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment,
        ], 201);
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, Payment $payment)
    {
        if ($payment->patient_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Simulate payment gateway integration
        $validated = $request->validate([
            'card_number' => 'required_if:payment_method,credit_card|digits:16',
            'card_expiry' => 'required_if:payment_method,credit_card',
            'cvv' => 'required_if:payment_method,credit_card|digits:3',
        ]);

        // Simulate payment processing (in production, integrate with payment gateway like Stripe)
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'payment_details' => [
                'gateway' => 'simulated',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);

        // Update prescription order status
        if ($payment->payable_type === PrescriptionOrder::class) {
            $payment->payable->update(['status' => 'confirmed']);
        }

        return response()->json([
            'message' => 'Payment processed successfully',
            'payment' => $payment,
        ]);
    }

    /**
     * Create insurance claim
     */
    public function createClaim(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'insurance_provider' => 'required|string',
            'policy_number' => 'required|string',
        ]);

        $payment = Payment::findOrFail($validated['payment_id']);

        if ($payment->patient_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $claim = InsuranceClaim::create([
            'patient_id' => $request->user()->id,
            'payment_id' => $payment->id,
            'insurance_provider' => $validated['insurance_provider'],
            'policy_number' => $validated['policy_number'],
            'claim_number' => 'CLM-' . uniqid(),
            'claim_amount' => $payment->amount,
            'status' => 'submitted',
            'submitted_date' => now(),
        ]);

        return response()->json([
            'message' => 'Insurance claim created successfully',
            'claim' => $claim,
        ], 201);
    }

    /**
     * Get insurance claims
     */
    public function getClaims(Request $request)
    {
        $claims = InsuranceClaim::where('patient_id', $request->user()->id)
            ->paginate(20);

        return response()->json($claims);
    }

    /**
     * Get claim details
     */
    public function getClaimDetails(InsuranceClaim $claim, Request $request)
    {
        if ($claim->patient_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($claim->load('payment'));
    }
}
