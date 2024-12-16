<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    //
    public function index()
    {
        return response()->json(Invoice::all(), 200);
    }
    

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'number' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        // Create the invoice
        $invoice = Invoice::create($validatedData);

        // Return the response
        return response()->json($invoice, 201);
    }

    public function search(Request $request)
    {
        $query = Invoice::query();

        // Filter by customer_name if provided
        if ($request->has('customer_name')) {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }

        // Filter by date if provided
        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        // Return the results
        return response()->json($query->get(), 200);
    }

    public function  markAsPaid(Request $request, $id){
        $invoice = Invoice::findOrFail($id);

        if(!$invoice){
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $invoice->is_paid = true;
        $invoice->payment_date = now();
        $invoice->save();
        return response()->json(['message' => 'Invoice marked as paid'], 200);
    }
}

