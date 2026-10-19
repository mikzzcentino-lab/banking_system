<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    // Display the transaction creation page
    public function create()
    {
        $accounts = Account::all();

        return view('transactions.create', compact('accounts'));
    }

    // Display all transactions
    public function index()
    {
        $transactions = Transaction::with('account')
            ->latest()
            ->get();

        return response()->json($transactions);
    }

    // Validate and save a transaction
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'type' => ['required', Rule::in(['deposit', 'withdrawal'])],
            'amount' => ['required', 'numeric', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $transaction = DB::transaction(function () use ($validated) {
            $account = Account::whereKey($validated['account_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $amount = (float) $validated['amount'];

            if (
                $validated['type'] === 'withdrawal'
                && ($account->balance - $amount)
                    < $account->minimum_balance
            ) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient funds. Minimum balance must be maintained.',
                ]);
            }

            return $account->transactions()->create($validated);
        });

        $account = Account::findOrFail($validated['account_id']);

        return response()->json([
            'message' => 'Transaction successful.',
            'transaction' => $transaction,
            'account_balance' => $account->balance,
        ], 201);
    }
}
