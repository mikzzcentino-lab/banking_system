
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking Dashboard</title>
</head>
<body>
    <h1>Simple Banking Dashboard</h1>

    <p>Total Accounts: <strong id="totalAccounts">Loading...</strong></p>

    <p>Total Money in Bank: ₱<strong id="totalMoney">Loading...</strong></p>

    <p>Transactions Today: <strong id="totalTransactions">Loading...</strong></p>

    <p id="status">Loading statistics...</p>

    <script>
        async function loadStats() {
            const status = document.getElementById('status');

            try {
                const response = await fetch('/api/dashboard/stats');

                if (!response.ok) {
                    throw new Error('Failed to load statistics');
                }

                const data = await response.json();

                document.getElementById('totalAccounts').textContent =
                    data.total_accounts;

                document.getElementById('totalMoney').textContent =
                    Number(data.total_money_in_bank).toFixed(2);

                document.getElementById('totalTransactions').textContent =
                    data.total_transactions_today;

                status.textContent = 'Statistics updated successfully.';
            } catch (error) {
                status.textContent = 'Unable to load statistics.';
                console.error(error);
            }
        }

        loadStats();
        setInterval(loadStats, 30000);
    </script>
</body>
</html>
