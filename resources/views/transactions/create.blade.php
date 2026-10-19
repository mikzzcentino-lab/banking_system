
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Transaction</title>
</head>
<body>
    <h1>Create Transaction</h1>

    <p>
        <a href="/dashboard">Back to Dashboard</a>
        |
        <a href="/transactions">View Transactions (JSON)</a>
    </p>

    @if ($accounts->isEmpty())
        <p>No accounts available.</p>
    @else
        <form action="/transactions" method="POST">
            @csrf

            <div>
                <label for="account_id">Select Account:</label>
                <select name="account_id" id="account_id" required>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->account_number }}
                            - Balance: ₱{{ number_format($account->balance, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <br>

            <div>
                <label for="type">Transaction Type:</label>
                <select name="type" id="type" required>
                    <option value="deposit">Deposit</option>
                    <option value="withdrawal">Withdrawal</option>
                </select>
            </div>

            <br>

            <div>
                <label for="amount">Amount:</label>
                <input
                    type="number"
                    name="amount"
                    id="amount"
                    min="1"
                    step="0.01"
                    required
                >
            </div>

            <br>

            <div>
                <label for="reference_number">Reference Number:</label>
                <input
                    type="text"
                    name="reference_number"
                    id="reference_number"
                >
            </div>

            <br>

            <div>
                <label for="notes">Notes:</label>
                <textarea name="notes" id="notes"></textarea>
            </div>

            <br>

            <button type="submit">Submit Transaction</button>
        </form>
    @endif
</body>
</html>
