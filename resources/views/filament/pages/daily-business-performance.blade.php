<x-filament-panels::page>
    <style>
        /* =========================================
           PURE CSS DASHBOARD LAYOUT (NO TAILWIND REQUIRED)
           ========================================= */

        /* Global Layout */
        .pure-dash-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            box-sizing: border-box;
        }
        .pure-dash-container * {
            box-sizing: border-box;
        }

        /* Header */
        .pure-dash-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            flex-wrap: wrap;
            gap: 10px;
        }
        .pure-dash-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #111827;
        }
        .pure-dash-header p {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }
        .pure-dash-header-right {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 13px;
            color: #6b7280;
        }
        .pure-dash-user {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f3f4f6;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            color: #374151;
        }

        /* Filter Bar - Forced One Line */
        .pure-filter-bar {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 16px;
            background: white;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            width: 100%;
            overflow-x: auto;
        }
        
        /* Force Filament Form into a Single Row */
        .pure-filter-bar .fi-fo {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            gap: 12px !important;
            align-items: flex-end !important;
            width: 100% !important;
            flex: 1 !important;
        }
        .pure-filter-bar .fi-fo > div {
            flex: 1 1 0% !important;
            min-width: 150px !important;
            width: auto !important;
        }
        .pure-filter-bar .fi-fo-grid {
            display: contents !important;
        }
        .pure-filter-bar .fi-fo-field-wrp-label {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #6b7280 !important;
            margin-bottom: 4px !important;
            white-space: nowrap !important;
        }
        .pure-filter-bar .fi-input-wrp {
            background-color: #f9fafb !important;
            border-color: #e5e7eb !important;
            border-radius: 6px !important;
            font-size: 14px !important;
        }

        /* Action Buttons */
        .pure-action-buttons {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        .pure-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            white-space: nowrap;
            transition: background-color 0.2s;
        }
        .pure-btn-primary {
            background-color: #2563eb;
            color: white;
        }
        .pure-btn-primary:hover { background-color: #1d4ed8; }
        .pure-btn-outline {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .pure-btn-outline:hover { background-color: #f9fafb; }
        .pure-btn svg { width: 16px !important; height: 16px !important; }

        /* KPI Grid */
        .pure-kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
            width: 100%;
        }
        @media (max-width: 1280px) { .pure-kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (max-width: 768px) { .pure-kpi-grid { grid-template-columns: 1fr; } }

        .pure-kpi-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .pure-kpi-icon {
            padding: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pure-kpi-icon svg { width: 24px !important; height: 24px !important; }
        .pure-kpi-icon.blue { background-color: #eff6ff; color: #2563eb; }
        .pure-kpi-icon.green { background-color: #f0fdf4; color: #16a34a; }
        .pure-kpi-icon.amber { background-color: #fffbeb; color: #d97706; }
        .pure-kpi-icon.red { background-color: #fef2f2; color: #dc2626; }
        .pure-kpi-icon.indigo { background-color: #eef2ff; color: #4f46e5; }

        .pure-kpi-title { font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; margin: 0 0 2px 0; }
        .pure-kpi-value { font-size: 20px; font-weight: 700; color: #111827; line-height: 1.2; margin: 0; }
        .pure-kpi-badge { font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 4px; }
        .pure-kpi-badge.green { background-color: #fffbeb; color: #d97706; }
        .pure-kpi-badge.red { background-color: #fef2f2; color: #dc2626; }

        /* Attention Widget */
        .pure-attention-widget {
            background: white;
            border-radius: 8px;
            border: 1px solid #fecaca;
            padding: 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .pure-attention-title {
            font-size: 13px; font-weight: 700; color: #dc2626;
            display: flex; align-items: center; gap: 6px; margin: 0 0 12px 0;
        }
        .pure-attention-title svg { width: 16px !important; height: 16px !important; }
        .pure-attention-table { width: 100%; font-size: 11px; text-align: left; border-collapse: collapse; }
        .pure-attention-table th { color: #6b7280; font-weight: 600; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb; }
        .pure-attention-table td { padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #374151; }
        .pure-attention-table td:last-child { color: #dc2626; font-weight: 500; }

        /* Main Data Table */
        .pure-table-wrapper {
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .pure-table-scroll {
            overflow-x: auto;
            width: 100%;
        }
        .pure-table {
            width: 100%;
            font-size: 12px;
            line-height: 1.2;
            border-collapse: collapse;
            text-align: center;
        font-family: "Inter", "Segoe UI", Arial, sans-serif;
        }
        .pure-table th, .pure-table td {
            padding: 6px 8px;
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        .pure-table th:last-child, .pure-table td:last-child { border-right: none; }

        /* Header Colors */
        .pure-th-main { background-color: #f9fafb; color: #4b5563; font-weight: 700; font-size: 10px; text-transform: uppercase; padding: 8px 6px; }
        .pure-th-sub { background-color: #f3f4f6; color: #6b7280; font-weight: 600; font-size: 9px; padding: 4px 6px; }
        .pure-th-deposit { background-color: #f0f7ff !important; color: #1e3a8a !important; }
        .pure-th-accounts { background-color: #f0fdf4 !important; color: #166534 !important; }
        .pure-th-mobile { background-color: #faf5ff !important; color: #6b21a8 !important; }
        .pure-th-atm { background-color: #fff7ed !important; color: #9a3412 !important; }
        .pure-th-loans { background-color: #fff1f2 !important; color: #9f1239 !important; }
        .pure-th-overall { background-color: #f3f4f6 !important; color: #374151 !important; }

        /* Rows */
        .pure-row-district { background-color: #f8fafc; font-weight: 700; color: #1e293b; }
        .pure-row-district td { border-bottom: 1px solid #e2e8f0; }
        .pure-row-branch { background-color: #ffffff; color: #475569; }
        .pure-row-branch:hover { background-color: #f8fafc; }
        .pure-row-branch td { border-bottom: 1px solid #f1f5f9; }
        .pure-row-grand-total { background-color: #1e3a8a; color: #ffffff; font-weight: 700; font-size: 11px; }
        .pure-row-grand-total td { border-right: 1px solid #3b5998; border-bottom: none; padding: 8px 6px; }
        .pure-row-grand-total td:last-child { border-right: none; }

        /* Text Utilities */
        .pure-text-red { color: #dc2626 !important; }
        .pure-text-green { color: #16a34a !important; }
        .pure-text-amber { color: #d97706 !important; }
        .pure-text-muted { color: #9ca3af !important; }
        .pure-font-bold { font-weight: 700; }

        /* Sticky Columns */
        .pure-sticky-col {
            position: sticky;
            left: 0;
            z-index: 10;
            text-align: left;
            padding-left: 12px !important;
        }
        .pure-row-district .pure-sticky-col { background-color: #f8fafc; }
        .pure-row-branch .pure-sticky-col { background-color: #ffffff; }
        .pure-row-grand-total .pure-sticky-col { background-color: #1e3a8a; color: #ffffff; }
        .pure-th-main.pure-sticky-col { background-color: #f9fafb; z-index: 20; }
        .pure-th-sub.pure-sticky-col { background-color: #f3f4f6; z-index: 20; }

        /* Table Legend */
        .pure-table-legend {
            padding: 10px 16px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            font-size: 10px;
            color: #6b7280;
            gap: 10px;
        }
        .pure-legend-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }
        .pure-legend-dot.green { background-color: #22c55e; }
        .pure-legend-dot.amber { background-color: #f59e0b; }
        .pure-legend-dot.red { background-color: #ef4444; }
        .pure-table-legend svg { width: 14px !important; height: 14px !important; }
    </style>

    <div class="pure-dash-container">
        
        <!-- Header -->
        <!-- <div class="pure-dash-header">
            <div>
                <h1>Daily Business Performance</h1>
                <p>Track branch performance, identify gaps and take action.</p>
            </div>
            <div class="pure-dash-header-right">
                <span>Last Updated: 01 Oct 2026 08:15</span>
                <div class="pure-dash-user">
                    <x-heroicon-o-user-circle style="width: 16px; height: 16px;" />
                    Admin
                </div>
            </div>
        </div> -->

        <!-- Filters Section - FORCED ONE LINE -->
        <div class="pure-filter-bar">
            <div class="fi-fo" style="flex: 1;">
                {{ $this->form }}
            </div>
            <div class="pure-action-buttons">
                <button type="button" class="pure-btn pure-btn-primary">
                    <x-heroicon-o-arrow-path /> Refresh
                </button>
                <button type="button" class="pure-btn pure-btn-outline">
                    <x-heroicon-o-arrow-down-tray /> Export Excel
                </button>
            </div>
        </div>

        <!-- KPI Cards & Attention Widget Grid -->
        <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 16px; width: 100%;">
            <!-- KPI Cards -->
            <div class="pure-kpi-grid">
                <div class="pure-kpi-card">
                    <div class="pure-kpi-icon blue"><x-heroicon-o-building-office-2 /></div>
                    <div>
                        <p class="pure-kpi-title">Total Branches</p>
                        <p class="pure-kpi-value">1,024</p>
                    </div>
                </div>
                <div class="pure-kpi-card">
                    <div class="pure-kpi-icon green"><x-heroicon-o-check-circle /></div>
                    <div>
                        <p class="pure-kpi-title">On Target</p>
                        <div style="display: flex; align-items: baseline; gap: 8px;">
                            <p class="pure-kpi-value">687</p>
                            <span class="pure-kpi-badge green">67.1%</span>
                        </div>
                    </div>
                </div>
                <div class="pure-kpi-card">
                    <div class="pure-kpi-icon amber"><x-heroicon-o-exclamation-circle /></div>
                    <div>
                        <p class="pure-kpi-title">Near Target</p>
                        <div style="display: flex; align-items: baseline; gap: 8px;">
                            <p class="pure-kpi-value">126</p>
                            <span class="pure-kpi-badge amber">12.3%</span>
                        </div>
                    </div>
                </div>
                <div class="pure-kpi-card" style="border-color: #fecaca;">
                    <div class="pure-kpi-icon red"><x-heroicon-o-exclamation-triangle /></div>
                    <div>
                        <p class="pure-kpi-title">Attention Required</p>
                        <div style="display: flex; align-items: baseline; gap: 8px;">
                            <p class="pure-kpi-value">211</p>
                            <span class="pure-kpi-badge red">20.6%</span>
                        </div>
                    </div>
                </div>
                <div class="pure-kpi-card">
                    <div class="pure-kpi-icon indigo"><x-heroicon-o-chart-bar /></div>
                    <div>
                        <p class="pure-kpi-title">Avg Achievement</p>
                        <div style="display: flex; align-items: baseline; gap: 8px;">
                            <p class="pure-kpi-value">94.6%</p>
                            <span style="font-size: 10px; color: #16a34a; font-weight: 500; display: flex; align-items: center; gap: 2px;">
                                <x-heroicon-s-arrow-trending-up style="width: 12px; height: 12px;" /> 2.8%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attention Branches Widget -->
            <div class="pure-attention-widget">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h3 class="pure-attention-title" style="margin: 0;">
                        <x-heroicon-s-exclamation-triangle /> Top Attention Branches
                    </h3>
                    <a href="#" style="font-size: 10px; color: #2563eb; text-decoration: none; font-weight: 500;">View All</a>
                </div>
                <table class="pure-attention-table">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>District</th>
                            <th>Issues</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Agaro</td><td>Jimma</td><td>4 KPIs below target</td></tr>
                        <tr><td>Bahir Dar</td><td>Bahir Dar</td><td>3 KPIs below target</td></tr>
                        <tr><td>Hawassa</td><td>SNNP</td><td>2 KPIs below target</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Main Data Table -->
        <div class="pure-table-wrapper">
            <div class="pure-table-scroll">
                <table class="pure-table">
                    <thead>
                        <tr>
                            <th class="pure-th-main pure-sticky-col" style="width: 40px;">#</th>
                            <th class="pure-th-main pure-sticky-col" style="left: 40px; width: 80px;">District</th>
                            <th class="pure-th-main pure-sticky-col" style="left: 120px; width: 120px;">Branch</th>
                            <th colspan="5" class="pure-th-main pure-th-deposit">DEPOSIT</th>
                            <th colspan="5" class="pure-th-main pure-th-accounts">NEW ACCOUNTS</th>
                            <th colspan="5" class="pure-th-main pure-th-mobile">MOBILE ACTIVATION</th>
                            <th colspan="5" class="pure-th-main pure-th-atm">ATM TRANSACTIONS</th>
                            <th colspan="5" class="pure-th-main pure-th-loans">LOANS</th>
                            <th colspan="2" class="pure-th-main pure-th-overall">Overall</th>
                        </tr>
                        <tr>
                            <th class="pure-th-sub pure-sticky-col" style="left: 0; width: 40px;"></th>
                            <th class="pure-th-sub pure-sticky-col" style="left: 40px; width: 80px;"></th>
                            <th class="pure-th-sub pure-sticky-col" style="left: 120px; width: 120px;"></th>
                            @for ($i = 0; $i < 5; $i++)
                                <th class="pure-th-sub">A</th><th class="pure-th-sub">T</th><th class="pure-th-sub">V</th><th class="pure-th-sub">G%</th><th class="pure-th-sub">Tr</th>
                            @endfor
                            <th class="pure-th-sub">Score</th>
                            <th class="pure-th-sub">Trend</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <!-- JIMMA DISTRICT -->
                        <tr class="pure-row-district">
                            <td class="pure-sticky-col" style="width: 40px;"><x-heroicon-s-chevron-down style="width: 12px; height: 12px; color: #9ca3af;" /></td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">JIMMA DISTRICT</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>299M</td><td>310M</td><td class="pure-text-red">-11M</td><td>96%</td><td>↑</td>
                            <td>52</td><td>52</td><td>0</td><td>100%</td><td>↑</td>
                            <td>39</td><td>50</td><td class="pure-text-red">-11</td><td>78%</td><td>↓</td>
                            <td>2,900</td><td>3,100</td><td class="pure-text-red">-200</td><td>94%</td><td>↑</td>
                            <td>180M</td><td>200M</td><td class="pure-text-red">-20M</td><td>90%</td><td>↓</td>
                            <td>94.2%</td><td class="pure-text-red">▼</td>
                        </tr>
                        <tr class="pure-row-branch">
                            <td class="pure-sticky-col" style="width: 40px;">1.</td>
                            <td class="pure-sticky-col" style="left: 40px;">Jimma Main</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>125M</td><td>120M</td><td class="pure-text-green">+5M</td><td>104%</td><td>↑</td>
                            <td>24</td><td>20</td><td class="pure-text-green">+4</td><td>120%</td><td>↑</td>
                            <td>18</td><td>25</td><td class="pure-text-red">-7</td><td>72%</td><td>↓</td>
                            <td>1,245</td><td>1,300</td><td class="pure-text-red">-55</td><td>96%</td><td>↑</td>
                            <td>95M</td><td>100M</td><td class="pure-text-red">-5M</td><td>95%</td><td>↑</td>
                            <td>97.8%</td><td class="pure-text-green">▲</td>
                        </tr>
                        <tr class="pure-row-branch">
                            <td class="pure-sticky-col" style="width: 40px;">2.</td>
                            <td class="pure-sticky-col" style="left: 40px;">Agaro</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>98M</td><td>110M</td><td class="pure-text-red">-12M</td><td>89%</td><td>↓</td>
                            <td>17</td><td>20</td><td class="pure-text-red">-3</td><td>85%</td><td>↓</td>
                            <td>12</td><td>15</td><td class="pure-text-red">-3</td><td>80%</td><td>↓</td>
                            <td>934</td><td>1,050</td><td class="pure-text-red">-116</td><td>89%</td><td>↓</td>
                            <td>52M</td><td>60M</td><td class="pure-text-red">-8M</td><td>87%</td><td>↓</td>
                            <td>87.2%</td><td class="pure-text-red">▼</td>
                        </tr>
                        <tr class="pure-row-branch">
                            <td class="pure-sticky-col" style="width: 40px;">3.</td>
                            <td class="pure-sticky-col" style="left: 40px;">Bedele</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>76M</td><td>80M</td><td class="pure-text-red">-4M</td><td>95%</td><td>→</td>
                            <td>11</td><td>12</td><td class="pure-text-red">-1</td><td>92%</td><td>→</td>
                            <td>9</td><td>10</td><td class="pure-text-red">-1</td><td>90%</td><td>→</td>
                            <td>721</td><td>750</td><td class="pure-text-red">-29</td><td>96%</td><td>↑</td>
                            <td>33M</td><td>40M</td><td class="pure-text-red">-7M</td><td>83%</td><td>↓</td>
                            <td>91.4%</td><td class="pure-text-amber">→</td>
                        </tr>
                        <tr class="pure-row-district">
                            <td class="pure-sticky-col" style="width: 40px;">4.</td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">DISTRICT TOTAL</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>299M</td><td>310M</td><td class="pure-text-red">-11M</td><td>96%</td><td>→</td>
                            <td>52</td><td>52</td><td>0</td><td>100%</td><td>↑</td>
                            <td>39</td><td>50</td><td class="pure-text-red">-11</td><td>78%</td><td>↓</td>
                            <td>2,900</td><td>3,100</td><td class="pure-text-red">-200</td><td>94%</td><td>↑</td>
                            <td>180M</td><td>200M</td><td class="pure-text-red">-20M</td><td>90%</td><td>↓</td>
                            <td>94.2%</td><td class="pure-text-red">▼</td>
                        </tr>
                        <!-- BAHIR DAR DISTRICT -->
                        <tr class="pure-row-district">
                            <td class="pure-sticky-col" style="width: 40px;"><x-heroicon-s-chevron-down style="width: 12px; height: 12px; color: #9ca3af;" /></td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">BAHIR DAR DISTRICT</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>410M</td><td>420M</td><td class="pure-text-red">-10M</td><td>98%</td><td>↓</td>
                            <td>76</td><td>80</td><td class="pure-text-red">-4</td><td>95%</td><td>→</td>
                            <td>62</td><td>70</td><td class="pure-text-red">-8</td><td>89%</td><td>↓</td>
                            <td>3,800</td><td>4,000</td><td class="pure-text-red">-200</td><td>95%</td><td>↑</td>
                            <td>250M</td><td>270M</td><td class="pure-text-red">-20M</td><td>93%</td><td>↓</td>
                            <td>93.6%</td><td class="pure-text-red">▼</td>
                        </tr>
                        <tr class="pure-row-branch">
                            <td class="pure-sticky-col" style="width: 40px;">5.</td>
                            <td class="pure-sticky-col" style="left: 40px;">Bahir Dar 1</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>160M</td><td>170M</td><td class="pure-text-red">-10M</td><td>94%</td><td>↓</td>
                            <td>30</td><td>34</td><td class="pure-text-red">-4</td><td>88%</td><td>↓</td>
                            <td>25</td><td>30</td><td class="pure-text-red">-5</td><td>83%</td><td>↓</td>
                            <td>1,450</td><td>1,600</td><td class="pure-text-red">-150</td><td>91%</td><td>↓</td>
                            <td>100M</td><td>110M</td><td class="pure-text-red">-10M</td><td>91%</td><td>↓</td>
                            <td>90.2%</td><td class="pure-text-red">▼</td>
                        </tr>
                        <tr class="pure-row-branch">
                            <td class="pure-sticky-col" style="width: 40px;">6.</td>
                            <td class="pure-sticky-col" style="left: 40px;">Bahir Dar 2</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>140M</td><td>135M</td><td class="pure-text-green">+5M</td><td>104%</td><td>↑</td>
                            <td>26</td><td>28</td><td class="pure-text-red">-2</td><td>93%</td><td>→</td>
                            <td>22</td><td>26</td><td class="pure-text-red">-4</td><td>85%</td><td>↓</td>
                            <td>1,250</td><td>1,300</td><td class="pure-text-red">-50</td><td>96%</td><td>↑</td>
                            <td>90M</td><td>95M</td><td class="pure-text-red">-5M</td><td>95%</td><td>↑</td>
                            <td>94.8%</td><td class="pure-text-green">▲</td>
                        </tr>
                        <tr class="pure-row-branch">
                            <td class="pure-sticky-col" style="width: 40px;">7.</td>
                            <td class="pure-sticky-col" style="left: 40px;">Bahir Dar 3</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>110M</td><td>115M</td><td class="pure-text-red">-5M</td><td>96%</td><td>→</td>
                            <td>20</td><td>18</td><td class="pure-text-green">+2</td><td>111%</td><td>↑</td>
                            <td>15</td><td>14</td><td class="pure-text-green">+1</td><td>107%</td><td>↑</td>
                            <td>1,100</td><td>1,100</td><td>0</td><td>100%</td><td>↑</td>
                            <td>60M</td><td>65M</td><td class="pure-text-red">-5M</td><td>92%</td><td>↓</td>
                            <td>96.7%</td><td class="pure-text-green">▲</td>
                        </tr>
                        <tr class="pure-row-district">
                            <td class="pure-sticky-col" style="width: 40px;">8.</td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">DISTRICT TOTAL</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>410M</td><td>420M</td><td class="pure-text-red">-10M</td><td>98%</td><td>↓</td>
                            <td>76</td><td>80</td><td class="pure-text-red">-4</td><td>95%</td><td>→</td>
                            <td>62</td><td>70</td><td class="pure-text-red">-8</td><td>89%</td><td>↓</td>
                            <td>3,800</td><td>4,000</td><td class="pure-text-red">-200</td><td>95%</td><td>↑</td>
                            <td>250M</td><td>270M</td><td class="pure-text-red">-20M</td><td>93%</td><td>↓</td>
                            <td>93.6%</td><td class="pure-text-red">▼</td>
                        </tr>
                        <!-- SNNP DISTRICT -->
                        <tr class="pure-row-district">
                            <td class="pure-sticky-col" style="width: 40px;"><x-heroicon-s-chevron-down style="width: 12px; height: 12px; color: #9ca3af;" /></td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">SNNP DISTRICT</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>320M</td><td>340M</td><td class="pure-text-red">-20M</td><td>94%</td><td>↓</td>
                            <td>58</td><td>60</td><td class="pure-text-red">-2</td><td>97%</td><td>↓</td>
                            <td>48</td><td>55</td><td class="pure-text-red">-7</td><td>87%</td><td>↓</td>
                            <td>2,450</td><td>2,700</td><td class="pure-text-red">-250</td><td>91%</td><td>↓</td>
                            <td>160M</td><td>180M</td><td class="pure-text-red">-20M</td><td>89%</td><td>↓</td>
                            <td>91.8%</td><td class="pure-text-red">▼</td>
                        </tr>
                        <tr class="pure-row-branch">
                            <td class="pure-sticky-col" style="width: 40px;">9.</td>
                            <td class="pure-sticky-col" style="left: 40px;">Hawassa</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>130M</td><td>140M</td><td class="pure-text-red">-10M</td><td>93%</td><td>↓</td>
                            <td>24</td><td>26</td><td class="pure-text-red">-2</td><td>92%</td><td>↓</td>
                            <td>20</td><td>25</td><td class="pure-text-red">-5</td><td>80%</td><td>↓</td>
                            <td>1,100</td><td>1,200</td><td class="pure-text-red">-100</td><td>92%</td><td>↓</td>
                            <td>85M</td><td>95M</td><td class="pure-text-red">-10M</td><td>89%</td><td>↓</td>
                            <td>89.5%</td><td class="pure-text-red">▼</td>
                        </tr>
                        <tr class="pure-row-branch">
                            <td class="pure-sticky-col" style="width: 40px;">10.</td>
                            <td class="pure-sticky-col" style="left: 40px;">Wolayta Sodo</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>110M</td><td>120M</td><td class="pure-text-red">-10M</td><td>92%</td><td>↓</td>
                            <td>18</td><td>20</td><td class="pure-text-red">-2</td><td>90%</td><td>↓</td>
                            <td>16</td><td>20</td><td class="pure-text-red">-4</td><td>80%</td><td>↓</td>
                            <td>950</td><td>1,100</td><td class="pure-text-red">-150</td><td>86%</td><td>↓</td>
                            <td>50M</td><td>60M</td><td class="pure-text-red">-10M</td><td>83%</td><td>↓</td>
                            <td>85.6%</td><td class="pure-text-red">▼</td>
                        </tr>
                        <tr class="pure-row-branch">
                            <td class="pure-sticky-col" style="width: 40px;">11.</td>
                            <td class="pure-sticky-col" style="left: 40px;">Dilla</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>80M</td><td>80M</td><td>0</td><td>100%</td><td>↑</td>
                            <td>16</td><td>14</td><td class="pure-text-green">+2</td><td>114%</td><td>↑</td>
                            <td>12</td><td>10</td><td class="pure-text-green">+2</td><td>120%</td><td>↑</td>
                            <td>400</td><td>400</td><td>0</td><td>100%</td><td>↑</td>
                            <td>25M</td><td>25M</td><td>0</td><td>100%</td><td>↑</td>
                            <td>98.4%</td><td class="pure-text-green">▲</td>
                        </tr>
                        <tr class="pure-row-district">
                            <td class="pure-sticky-col" style="width: 40px;">12.</td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">DISTRICT TOTAL</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>320M</td><td>340M</td><td class="pure-text-red">-20M</td><td>94%</td><td>↓</td>
                            <td>58</td><td>60</td><td class="pure-text-red">-2</td><td>97%</td><td>↓</td>
                            <td>48</td><td>55</td><td class="pure-text-red">-7</td><td>87%</td><td>↓</td>
                            <td>2,450</td><td>2,700</td><td class="pure-text-red">-250</td><td>91%</td><td>↓</td>
                            <td>160M</td><td>180M</td><td class="pure-text-red">-20M</td><td>89%</td><td>↓</td>
                            <td>91.8%</td><td class="pure-text-red">▼</td>
                        </tr>
                    </tbody>

                    <!-- Grand Total Footer -->
                    <tfoot class="pure-row-grand-total">
                        <tr>
                            <td class="pure-sticky-col" style="width: 40px;"></td>
                            <td class="pure-sticky-col" style="left: 40px;">GRAND TOTAL</td>
                            <td class="pure-sticky-col" style="left: 120px;"></td>
                            <td>1,029M</td><td>1,070M</td><td class="pure-text-red">-41M</td><td>96%</td><td>↑</td>
                            <td>186</td><td>192</td><td class="pure-text-red">-6</td><td>97%</td><td>↑</td>
                            <td>149</td><td>175</td><td class="pure-text-red">-26</td><td>85%</td><td>↓</td>
                            <td>9,150</td><td>9,800</td><td class="pure-text-red">-650</td><td>93%</td><td>↓</td>
                            <td>590M</td><td>650M</td><td class="pure-text-red">-60M</td><td>91%</td><td>↓</td>
                            <td>93.7%</td><td class="pure-text-red">▼</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Table Legend -->
            <div class="pure-table-legend">
                <div style="display: flex; gap: 16px;">
                    <span style="display: flex; align-items: center;"><span class="pure-legend-dot green"></span> On Target (≥ 100%)</span>
                    <span style="display: flex; align-items: center;"><span class="pure-legend-dot amber"></span> Near Target (90-99%)</span>
                    <span style="display: flex; align-items: center;"><span class="pure-legend-dot red"></span> Below Target (< 90%)</span>
                </div>
                <div style="display: flex; gap: 16px;">
                    <span style="color: #16a34a; display: flex; align-items: center; gap: 2px;"><x-heroicon-s-arrow-up /> Improving</span>
                    <span style="color: #6b7280; display: flex; align-items: center; gap: 2px;"><x-heroicon-s-arrow-right /> Stable</span>
                    <span style="color: #dc2626; display: flex; align-items: center; gap: 2px;"><x-heroicon-s-arrow-down /> Declining</span>
                </div>
                <div style="display: flex; gap: 12px; font-family: monospace; font-size: 9px;">
                    <span>A = Actual</span>
                    <span>T = Target</span>
                    <span>V = Variance</span>
                    <span>G% = Achievement %</span>
                    <span>Tr = Previous Day %</span>
                    <span>St = Status</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>