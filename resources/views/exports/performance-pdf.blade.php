<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #1F4E79;
            margin-bottom: 20px;
        }

        .bank-name {
            font-size: 22px;
            font-weight: bold;
            color: #1F4E79;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }

        .date-range {
            font-size: 11px;
            color: #777;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1F4E79;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #1F4E79;
            color: #fff;
        }

        th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #eee;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #777;
        }

        .page-break {
            page-break-after: always;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="bank-name">BPMS - Branch Performance Management System</div>
        <div class="report-title">
            @if($reportType === 'deposit')
                Daily Deposit Performance Report
            @elseif($reportType === 'account')
                Daily Account Performance Report
            @else
                Full Performance Report
            @endif
        </div>
        <div class="date-range">Date Range: {{ $dateRange }}</div>
    </div>

    @if($reportType === 'deposit' || $reportType === 'full')
        <div class="section-title">Deposit Performance</div>
        @if($deposits && $deposits->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Branch Code</th>
                        <th>Branch Name</th>
                        <th>District</th>
                        <th>Business Day</th>
                        <th class="text-right">Total Deposit</th>
                        <th class="text-right">New Deposit</th>
                        <th class="text-right">Inflow</th>
                        <th class="text-right">Outflow</th>
                        <th class="text-right">Net Change</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deposits as $deposit)
                        <tr>
                            <td>{{ $deposit->branch?->code ?? 'N/A' }}</td>
                            <td>{{ $deposit->branch?->name ?? 'N/A' }}</td>
                            <td>{{ $deposit->branch?->district?->name ?? 'N/A' }}</td>
                            <td>{{ $deposit->business_day?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format((float) $deposit->total_deposit_amount, 2) }}</td>
                            <td class="text-right">{{ number_format((float) $deposit->new_deposit_amount, 2) }}</td>
                            <td class="text-right">{{ number_format((float) $deposit->deposit_inflow_amount, 2) }}</td>
                            <td class="text-right">{{ number_format((float) $deposit->deposit_outflow_amount, 2) }}</td>
                            <td class="text-right">{{ number_format((float) $deposit->net_deposit_change, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No deposit performance data available.</div>
        @endif
    @endif

    @if($reportType === 'account' || $reportType === 'full')
        @if($reportType === 'full')
            <div class="page-break"></div>
        @endif
        <div class="section-title">Account Performance</div>
        @if($accounts && $accounts->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Branch Code</th>
                        <th>Branch Name</th>
                        <th>District</th>
                        <th>Business Day</th>
                        <th class="text-right">Total Accounts</th>
                        <th class="text-right">Active Accounts</th>
                        <th class="text-right">New Accounts</th>
                        <th class="text-right">Dormant Accounts</th>
                        <th class="text-right">Reactivated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                        <tr>
                            <td>{{ $account->branch?->code ?? 'N/A' }}</td>
                            <td>{{ $account->branch?->name ?? 'N/A' }}</td>
                            <td>{{ $account->branch?->district?->name ?? 'N/A' }}</td>
                            <td>{{ $account->business_day?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format((int) $account->total_accounts) }}</td>
                            <td class="text-right">{{ number_format((int) $account->active_accounts) }}</td>
                            <td class="text-right">{{ number_format((int) $account->new_accounts) }}</td>
                            <td class="text-right">{{ number_format((int) $account->dormant_accounts) }}</td>
                            <td class="text-right">{{ number_format((int) $account->reactivated_accounts) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No account performance data available.</div>
        @endif
    @endif

    <div class="footer">
        Page <span class="page-number"></span> | Generated on {{ date('Y-m-d H:i:s') }} | BPMS
    </div>
</body>
</html>
