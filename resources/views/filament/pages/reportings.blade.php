<x-filament-panels::page>
    <style>
        /* =========================================
           ADVANCED REPORTING DASHBOARD CSS
           ========================================= */
        
        /* !!! FIX FOR GIANT ICONS !!! */
        .kpi-icon svg { width: 28px !important; height: 28px !important; }
        .dashboard-filters svg { width: 24px !important; height: 24px !important; }
        .empty-state svg { width: 48px !important; height: 48px !important; }

        /* !!! FIX FOR STACKING BUTTONS !!! */
        .dashboard-filters .action-buttons {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 10px !important;
        }
        .dashboard-filters .action-buttons button {
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
        }

        /* !!! FORCE SINGLE LINE FORM (INCLUDING FROM/TO) !!! */
        .dashboard-filter-form {
            flex: 1;
            width: 100%;
            overflow-x: auto; /* Allow horizontal scroll if it gets too tight */
            padding-bottom: 5px; /* Space for scrollbar */
        }
        
        /* 1. Force the main form wrapper to be a single row */
        .dashboard-filter-form .fi-fo {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important; /* Strictly one line */
            gap: 12px !important;
            align-items: flex-end !important;
            width: max-content !important; /* Let it expand past 100% if needed */
            min-width: 100% !important;
        }

        /* 2. Force all direct field wrappers to share space equally */
        .dashboard-filter-form .fi-fo > div {
            flex: 1 1 0% !important;
            min-width: 130px !important; /* Prevent inputs from becoming too narrow */
            width: auto !important;
        }

        /* 3. Target the nested Date Range grid specifically (From / To) */
        .dashboard-filter-form .fi-fo > .fi-fo-grid {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            gap: 12px !important;
            flex: 1 1 0% !important; /* Make the grid itself share space */
        }
        
        /* 4. Ensure the From/To fields inside the grid share space equally */
        .dashboard-filter-form .fi-fo > .fi-fo-grid > div {
            flex: 1 1 0% !important;
            min-width: 120px !important;
            width: auto !important;
        }

        /* 5. Make the 'Use a date range' toggle compact and inline */
        .dashboard-filter-form .fi-fo-field-wrp:has(.fi-fo-toggle) {
            flex: 0 0 auto !important; /* Don't stretch the toggle */
            min-width: unset !important;
            padding-bottom: 8px; /* Align with inputs */
        }

        /* 6. Hide the form section header and description to save vertical space */
        .dashboard-filter-form .fi-fo-section-header,
        .dashboard-filter-form .fi-fo-section-description {
            display: none !important;
        }

        /* Filter Bar Overrides - INCREASED FONT SIZE */
        .dashboard-filters .fi-fo-field-wrp-label { 
            font-size: 14px !important; 
            font-weight: 700 !important; 
            color: #374151 !important; 
            text-transform: uppercase; 
            letter-spacing: 0.025em;
            margin-bottom: 6px;
            white-space: nowrap !important;
        }
        .dark .dashboard-filters .fi-fo-field-wrp-label {
            color: #d1d5db !important;
        }

        /* Force larger font in inputs, selects, and dropdown options */
        .dashboard-filters .fi-input-wrp,
        .dashboard-filters .fi-fo-field-wrp input,
        .dashboard-filters .fi-fo-field-wrp select,
        .dashboard-filters .fi-select-input-btn,
        .dashboard-filters .fi-select-input-option,
        .dashboard-filters .fi-fo-field-wrp option {
            font-size: 16px !important; 
            font-weight: 500 !important;
            color: #111827 !important;
        }
        .dark .dashboard-filters .fi-input-wrp,
        .dark .dashboard-filters .fi-fo-field-wrp input,
        .dark .dashboard-filters .fi-fo-field-wrp select,
        .dark .dashboard-filters .fi-select-input-btn,
        .dark .dashboard-filters .fi-select-input-option,
        .dark .dashboard-filters .fi-fo-field-wrp option {
            color: #f9fafb !important;
        }

        .dashboard-filters .fi-input-wrp { 
            background-color: #f9fafb; 
            border-color: #e5e7eb; 
            border-radius: 6px; 
            padding: 4px 8px; 
        }
        .dark .dashboard-filters .fi-input-wrp {
            background-color: #1f2937;
            border-color: #374151;
        }

        /* KPI Cards - Force single row */
        .kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 16px !important;
            width: 100% !important;
        }
        @media (max-width: 1280px) { .kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; } }
        @media (max-width: 768px) { .kpi-grid { grid-template-columns: 1fr !important; } }

        .kpi-card {
            display: flex;
            flex-direction: row !important;
            align-items: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            gap: 16px;
            width: 100%;
        }
        .dark .kpi-card {
            background: #111827;
            border-color: #1f2937;
        }
        .kpi-icon {
            padding: 14px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .kpi-icon.blue { background-color: #eff6ff; color: #2563eb; }
        .kpi-icon.indigo { background-color: #eef2ff; color: #4f46e5; }
        .kpi-icon.emerald { background-color: #ecfdf5; color: #059669; }
        .kpi-icon.amber { background-color: #fffbeb; color: #d97706; }
        .kpi-icon.red { background-color: #fef2f2; color: #dc2626; }
        
        .kpi-title { font-size: 13px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; margin-bottom: 4px; }
        .kpi-value { font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.2; }
        .dark .kpi-value { color: #f9fafb; }

        /* Main Data Table */
        .dashboard-table-wrapper {
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .dark .dashboard-table-wrapper {
            background: #111827;
            border-color: #1f2937;
        }
        .dashboard-table-scroll {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 650px;
        }
        .dashboard-table {
            width: 100%;
            font-size: 14px;
            line-height: 1.4;
            border-collapse: collapse;
            text-align: left;
            font-family: 'SF Mono', 'Fira Code', ui-monospace, monospace;
        }
        .dashboard-table th, 
        .dashboard-table td {
            padding: 12px 16px;
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        .dark .dashboard-table th,
        .dark .dashboard-table td {
            border-right-color: #374151;
            border-bottom-color: #374151;
        }
        .dashboard-table th:last-child,
        .dashboard-table td:last-child { border-right: none; }

        .th-main { 
            background-color: #f9fafb; 
            color: #4b5563; 
            font-weight: 700; 
            font-size: 12px; 
            text-transform: uppercase; 
            position: sticky;
            top: 0;
            z-index: 10;
            letter-spacing: 0.05em;
        }
        .dark .th-main {
            background-color: #1f2937;
            color: #d1d5db;
        }

        .row-data { background-color: #ffffff; color: #374151; }
        .row-data:hover { background-color: #f8fafc; }
        .dark .row-data { background-color: #111827; color: #d1d5db; }
        .dark .row-data:hover { background-color: #1f2937; }
        .row-data:nth-child(even) { background-color: #f9fafb; }
        .dark .row-data:nth-child(even) { background-color: #1a2332; }

        .text-right { text-align: right; }
        .tabular-nums { font-variant-numeric: tabular-nums; }

        .empty-state {
            padding: 64px 24px;
            text-align: center;
            color: #6b7280;
            font-size: 16px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px dashed #d1d5db;
            margin: 16px;
        }
        .dark .empty-state {
            background: #1f2937;
            border-color: #374151;
            color: #9ca3af;
        }
    </style>

    <form wire:submit="getReportRows" class="space-y-6">
        
        @php
            $rows = $this->getReportRows();
            $reportName = \App\Services\ReportService::REPORTS[$this->data['report'] ?? 'all_kpi'] ?? 'Report';
            $windowLabel = $this->getWindowLabel();
        @endphp

        <!-- 1. Advanced Filter Bar (Single Line) -->
        <div class="dashboard-filters flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-white p-5 rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            
            <!-- Form container with forced one-line class -->
            <div class="flex-1 w-full dashboard-filter-form">
                {{ $this->form }}
            </div>
            
            <!-- Buttons in one line -->
            <div class="action-buttons flex-shrink-0">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-3 px-5 rounded-lg transition-colors shadow-sm">
                    <x-heroicon-o-arrow-path /> Generate
                </button>
                
                <button type="button" wire:click="exportToExcel" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 text-sm font-semibold py-3 px-5 rounded-lg transition-colors shadow-sm dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                    <x-heroicon-o-arrow-down-tray /> Export
                </button>
            </div>
        </div>

        <!-- 2. Summary KPI Cards (Single Line) -->
        <div class="kpi-grid">
            <!-- Report Name Card -->
            <div class="kpi-card">
                <div class="kpi-icon indigo">
                    <x-heroicon-o-document-text />
                </div>
                <div>
                    <p class="kpi-title">Selected Report</p>
                    <p class="kpi-value">{{ $reportName }}</p>
                </div>
            </div>

            <!-- Period Card -->
            <div class="kpi-card">
                <div class="kpi-icon blue">
                    <x-heroicon-o-calendar />
                </div>
                <div>
                    <p class="kpi-title">Reporting Period</p>
                    <p class="kpi-value">{{ $windowLabel }}</p>
                </div>
            </div>

            <!-- Total Branches Card -->
            <div class="kpi-card">
                <div class="kpi-icon emerald">
                    <x-heroicon-o-building-office-2 />
                </div>
                <div>
                    <p class="kpi-title">Total Branches</p>
                    <p class="kpi-value">{{ count($rows) }}</p>
                </div>
            </div>

            <!-- Placeholder KPI 4 -->
            <div class="kpi-card">
                <div class="kpi-icon amber">
                    <x-heroicon-o-exclamation-circle />
                </div>
                <div>
                    <p class="kpi-title">Status</p>
                    <p class="kpi-value">Active</p>
                </div>
            </div>

            <!-- Placeholder KPI 5 -->
            <div class="kpi-card">
                <div class="kpi-icon red">
                    <x-heroicon-o-clock />
                </div>
                <div>
                    <p class="kpi-title">Last Sync</p>
                    <p class="kpi-value">Just now</p>
                </div>
            </div>
        </div>

        <!-- 3. Advanced Data Table -->
        <div class="dashboard-table-wrapper">
            @if (empty($rows) || count($rows) === 0)
                <div class="empty-state">
                    <x-heroicon-o-inbox class="mx-auto mb-4 text-gray-300 dark:text-gray-600" />
                    <p class="font-bold text-gray-900 dark:text-white text-lg">No data available</p>
                    <p class="text-base mt-2">There are no records for the selected report and period.</p>
                </div>
            @else
                @php
                    $headings = array_keys($rows[0]);
                @endphp
                <div class="dashboard-table-scroll">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                @foreach ($headings as $heading)
                                    <th class="th-main">
                                        {{ $heading }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="row-data">
                                    @foreach ($row as $value)
                                        <td class="whitespace-nowrap {{ is_numeric($value) ? 'text-right tabular-nums' : '' }}">
                                            {{ is_numeric($value) ? number_format((float) $value, 2) : $value }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Table Footer / Legend -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <span>Showing {{ count($rows) }} branches</span>
                    <span class="font-mono">Report generated on {{ now()->format('d M Y H:i') }}</span>
                </div>
            @endif
        </div>
    </form>
</x-filament-panels::page>