<x-filament-panels::page>
    <style>
        .kpi-icon svg { width: 26px !important; height: 26px !important; }
        .dashboard-filters svg { width: 18px !important; height: 18px !important; }
        .empty-state svg { width: 52px !important; height: 52px !important; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .district-chevron { width: 14px !important; height: 14px !important; flex-shrink: 0; }
        .action-buttons button svg { width: 16px !important; height: 16px !important; }

        .dashboard-filters {
            background: white; padding: 10px 12px; border-radius: 8px;
            border: 1px solid #e5e7eb; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            display: flex; flex-direction: row; align-items: center;
            gap: 12px; width: 100%;
        }
        .dark .dashboard-filters { background: #111827; border-color: #1f2937; }

        .dashboard-filter-form { flex: 1; min-width: 0; width: 100%; overflow-x: auto; padding-bottom: 3px; -webkit-overflow-scrolling: touch; }
        .dashboard-filter-form .fi-fo {
            display: flex !important; flex-direction: row !important;
            flex-wrap: nowrap !important; gap: 8px !important;
            align-items: flex-end !important; width: max-content !important; min-width: 100% !important;
        }
        .dashboard-filter-form .fi-fo > div { flex: 1 1 0% !important; min-width: 120px !important; width: auto !important; }
        .dashboard-filter-form .fi-fo > .fi-fo-grid {
            display: flex !important; flex-direction: row !important;
            flex-wrap: nowrap !important; gap: 8px !important; flex: 1 1 0% !important;
        }
        .dashboard-filter-form .fi-fo > .fi-fo-grid > div { flex: 1 1 0% !important; min-width: 110px !important; width: auto !important; }
        .dashboard-filter-form .fi-fo-field-wrp:has(.fi-fo-toggle) { flex: 0 0 auto !important; min-width: unset !important; padding-bottom: 4px; }
        .dashboard-filter-form .fi-fo-section-header,
        .dashboard-filter-form .fi-fo-section-description { display: none !important; }

        .dashboard-filters .fi-fo-field-wrp-label {
            font-size: 10px !important; font-weight: 700 !important;
            color: #6b7280 !important; text-transform: uppercase;
            letter-spacing: 0.03em; margin-bottom: 1px; line-height: 1 !important;
            white-space: nowrap !important;
        }
        .dark .dashboard-filters .fi-fo-field-wrp-label { color: #9ca3af !important; }

        .dashboard-filters .fi-input-wrp,
        .dashboard-filters .fi-fo-field-wrp input,
        .dashboard-filters .fi-fo-field-wrp select,
        .dashboard-filters .fi-select-input-btn,
        .dashboard-filters .fi-select-input-option {
            font-size: 12px !important; font-weight: 500 !important; color: #111827 !important;
        }
        .dashboard-filters .fi-input-wrp,
        .dashboard-filters .fi-fo-field-wrp input,
        .dashboard-filters .fi-fo-field-wrp select,
        .dashboard-filters .fi-select-input-btn {
            min-height: 28px !important;
            height: 28px !important;
            padding: 2px 6px !important;
            line-height: 1.1 !important;
        }
        .dark .dashboard-filters .fi-input-wrp,
        .dark .dashboard-filters .fi-fo-field-wrp input,
        .dark .dashboard-filters .fi-fo-field-wrp select,
        .dark .dashboard-filters .fi-select-input-btn,
        .dark .dashboard-filters .fi-select-input-option { color: #f9fafb !important; }

        .dashboard-filters .fi-input-wrp {
            background-color: #f9fafb; border-color: #e5e7eb;
            border-radius: 5px; padding: 0 6px;
        }
        .dark .dashboard-filters .fi-input-wrp { background-color: #1f2937; border-color: #374151; }

        .dashboard-filters .fi-input-wrp svg,
        .dashboard-filters .fi-select-input-btn svg {
            width: 14px !important; height: 14px !important;
        }

        .action-buttons { display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; flex-shrink: 0 !important; }
        .action-buttons button {
            display: inline-flex !important; flex-direction: row !important;
            align-items: center !important; gap: 6px !important;
            white-space: nowrap !important; font-size: 12px !important;
            font-weight: 600 !important; padding: 2px 12px !important;
            height: 28px !important;
            border-radius: 6px !important; cursor: pointer !important;
            transition: background-color 0.2s !important; border: none !important;
        }
        .action-buttons button svg { width: 14px !important; height: 14px !important; }

        .kpi-grid { display: grid !important; grid-template-columns: repeat(5, minmax(0, 1fr)) !important; gap: 12px !important; width: 100% !important; }
        .kpi-card {
            display: flex; flex-direction: row !important; align-items: center;
            padding: 12px 14px; background: white; border-radius: 8px;
            border: 1px solid #e5e7eb; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            gap: 10px; width: 100%; min-width: 0; transition: box-shadow 0.2s;
        }
        .kpi-card:hover { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08); }
        .dark .kpi-card { background: #111827; border-color: #1f2937; }

        .kpi-icon { padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .kpi-icon.blue    { background-color: #eff6ff; color: #2563eb; }
        .kpi-icon.indigo  { background-color: #eef2ff; color: #4f46e5; }
        .kpi-icon.emerald { background-color: #ecfdf5; color: #059669; }
        .kpi-icon.amber   { background-color: #fffbeb; color: #d97706; }
        .kpi-icon.red     { background-color: #fef2f2; color: #dc2626; }

        .kpi-title {
            font-size: 10px; color: #6b7280; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em;
            margin: 0 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .kpi-value { font-size: 1.05rem; font-weight: 700; color: #111827; line-height: 1.2; margin: 0; word-break: break-word; }
        .dark .kpi-value { color: #f9fafb; }

        .dashboard-table-wrapper {
            background: white; border-radius: 8px; border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden;
            display: flex; flex-direction: column;
        }
        .dark .dashboard-table-wrapper { background: #111827; border-color: #1f2937; }

        .dashboard-table-scroll { overflow-x: auto; overflow-y: auto; max-height: 700px; -webkit-overflow-scrolling: touch; }
        .dashboard-table {
            width: 100%; font-size: 12px; line-height: 1.3;
            border-collapse: separate; border-spacing: 0; text-align: left;
            font-family: "Inter", "Segoe UI", Arial, sans-serif;
        }
        .dashboard-table th, .dashboard-table td {
            padding: 8px 12px;
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        .dark .dashboard-table th, .dark .dashboard-table td {
            border-right-color: #374151; border-bottom-color: #374151;
        }
        .dashboard-table th:last-child, .dashboard-table td:last-child { border-right: none; }

        .th-cat {
            background-color: #0f2557 !important; color: #ffffff !important;
            font-weight: 800; font-size: 11px; text-transform: uppercase;
            letter-spacing: 0.08em; padding: 8px 12px; text-align: center;
            position: sticky; top: 0; z-index: 11;
            border-bottom: 2px solid #1e3a8a !important;
        }
        .th-cat.cat-fixed      { background-color: #0a1a3f !important; text-align: left; z-index: 22; }
        .th-cat.cat-major      { box-shadow: inset 0 -3px 0 #3b82f6; }
        .th-cat.cat-digital    { box-shadow: inset 0 -3px 0 #8b5cf6; }
        .th-cat.cat-activation { box-shadow: inset 0 -3px 0 #10b981; }
        .th-cat.cat-other      { box-shadow: inset 0 -3px 0 #f59e0b; }

        .th-main {
            background-color: #1e3a8a !important; color: #ffffff !important;
            font-weight: 700; font-size: 10px; text-transform: uppercase;
            position: sticky; top: 33px; z-index: 10;
            letter-spacing: 0.05em; text-align: center;
            border-bottom: 1px solid #3b5998 !important;
        }
        .kpi-col-major      { box-shadow: inset 0 -3px 0 #3b82f6; }
        .kpi-col-digital    { box-shadow: inset 0 -3px 0 #8b5cf6; }
        .kpi-col-activation { box-shadow: inset 0 -3px 0 #10b981; }
        .kpi-col-other      { box-shadow: inset 0 -3px 0 #f59e0b; }

        .sticky-first {
            position: sticky; left: 0; z-index: 12;
            background-color: #ffffff; box-shadow: 2px 0 4px rgba(0, 0, 0, 0.04); text-align: left;
        }
        .th-main.sticky-first { background-color: #1e3a8a !important; z-index: 20; text-align: left; }
        .dark .sticky-first { background-color: #111827; }
        .dark .th-main.sticky-first { background-color: #1e3a8a !important; }

        .row-district {
            background-color: #eef2ff; color: #1e293b; font-weight: 700;
            cursor: pointer; user-select: none; transition: background-color 0.15s;
        }
        .row-district:hover { background-color: #e0e7ff; }
        .row-district td { border-bottom: 1px solid #c7d2fe; border-top: 1px solid #c7d2fe; }
        .dark .row-district { background-color: #1e3a8a; color: #ffffff; }
        .dark .row-district:hover { background-color: #1e40af; }
        .dark .row-district td { border-bottom-color: #3b5998; border-top-color: #3b5998; }
        .row-district .sticky-first { background-color: #eef2ff; }
        .row-district:hover .sticky-first { background-color: #e0e7ff; }
        .dark .row-district .sticky-first { background-color: #1e3a8a; }
        .dark .row-district:hover .sticky-first { background-color: #1e40af; }

        .row-data { background-color: #ffffff; color: #374151; }
        .row-data:hover { background-color: #f8fafc; }
        .row-data:hover .sticky-first { background-color: #f8fafc; }
        .dark .row-data { background-color: #111827; color: #d1d5db; }
        .dark .row-data:hover { background-color: #1f2937; }
        .dark .row-data:hover .sticky-first { background-color: #1f2937; }

        .row-grand-total {
            background-color: #1e3a8a; color: #ffffff; font-weight: 700; font-size: 12px;
        }
        .row-grand-total td {
            border-right: 1px solid #3b5998; border-bottom: none;
            padding: 12px; border-top: 2px solid #0f2557;
        }
        .row-grand-total td:last-child { border-right: none; }
        .row-grand-total .sticky-first { background-color: #1e3a8a; }

        .district-chevron { transition: transform 0.2s ease; display: inline-block; }
        .district-chevron.open { transform: rotate(0deg); }
        .district-chevron.closed { transform: rotate(-90deg); }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .tabular-nums { font-variant-numeric: tabular-nums; }
        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }

        .table-footer {
            padding: 12px 20px; background-color: #f9fafb; border-top: 1px solid #e5e7eb;
            display: flex; flex-wrap: wrap; align-items: center;
            justify-content: space-between; font-size: 12px; color: #6b7280; gap: 10px;
        }
        .dark .table-footer { background-color: #1f2937; border-color: #374151; color: #9ca3af; }
        .table-footer .legend-bar { display: flex; gap: 14px; flex-wrap: wrap; align-items: center; }

        .empty-state {
            padding: 64px 24px; text-align: center; color: #6b7280;
            font-size: 15px; background: #f9fafb; margin: 16px;
            border-radius: 8px; border: 1px dashed #d1d5db;
        }
        .dark .empty-state { background: #1f2937; border-color: #374151; color: #9ca3af; }
        .empty-state .empty-title { font-weight: 700; font-size: 16px; color: #111827; margin: 12px 0 6px; }
        .dark .empty-state .empty-title { color: #f9fafb; }

        @media (max-width: 1280px) { .kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; } }
        @media (max-width: 1024px) {
            .kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
            .dashboard-table { font-size: 11px; }
            .dashboard-table th, .dashboard-table td { padding: 7px 10px; }
            .th-cat { font-size: 10px; padding: 7px 10px; }
            .th-main { font-size: 9px; padding: 6px 10px; }
        }
        @media (max-width: 900px) {
            .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            .kpi-card { padding: 12px; gap: 8px; }
            .kpi-icon { padding: 7px; }
            .kpi-icon svg { width: 20px !important; height: 20px !important; }
            .kpi-value { font-size: 1rem; }
            .kpi-title { font-size: 10px; }
        }
        @media (max-width: 768px) {
            .dashboard-filters {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
                padding: 12px !important;
            }
            .dashboard-filter-form { overflow-x: visible !important; padding-bottom: 0 !important; width: 100% !important; }
            .dashboard-filter-form .fi-fo {
                flex-direction: column !important; flex-wrap: wrap !important;
                width: 100% !important; min-width: 0 !important;
                gap: 10px !important; align-items: stretch !important;
            }
            .dashboard-filter-form .fi-fo > div,
            .dashboard-filter-form .fi-fo > .fi-fo-grid > div { flex: 1 1 auto !important; min-width: 0 !important; width: 100% !important; }
            .dashboard-filter-form .fi-fo > .fi-fo-grid { flex-direction: column !important; flex-wrap: wrap !important; gap: 10px !important; }
            .dashboard-filter-form .fi-fo-field-wrp:has(.fi-fo-toggle) { flex: 1 1 auto !important; width: 100% !important; }
            .action-buttons { width: 100% !important; justify-content: stretch !important; }
            .action-buttons button { flex: 1 1 0% !important; justify-content: center !important; padding: 8px 12px !important; }
            .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 12px !important; }
            .kpi-card { padding: 12px; gap: 8px; border-radius: 6px; }
            .kpi-icon { padding: 7px; border-radius: 6px; }
            .kpi-icon svg { width: 20px !important; height: 20px !important; }
            .kpi-value { font-size: 1rem; }
            .kpi-title { font-size: 10px; letter-spacing: 0.02em; }
            .dashboard-table { font-size: 11px; }
            .dashboard-table th, .dashboard-table td { padding: 6px 8px; }
            .th-cat { font-size: 9px; padding: 6px 8px; letter-spacing: 0.05em; }
            .th-main { font-size: 9px; padding: 6px 8px; top: 29px; }
            .table-footer { padding: 10px 14px; flex-direction: column; align-items: flex-start; gap: 8px; }
            .table-footer .legend-bar { gap: 10px; font-size: 11px; }
        }
        @media (max-width: 480px) {
            .dashboard-filters { padding: 10px !important; }
            .action-buttons button { font-size: 12px !important; padding: 6px 10px !important; }
            .action-buttons button svg { width: 14px !important; height: 14px !important; }
            .kpi-grid { grid-template-columns: 1fr !important; }
            .kpi-card { padding: 12px; }
            .kpi-value { font-size: 1.05rem; }
            .dashboard-table th, .dashboard-table td { padding: 6px 8px; font-size: 10px; }
            .th-cat { font-size: 9px; }
            .th-main { font-size: 8px; }
            .table-footer { font-size: 11px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .district-chevron, .kpi-card, .row-district, .row-data { transition: none !important; }
        }
    </style>

    <form wire:submit="getReportRows" class="space-y-5">

        @php
            $rawRows = $this->getReportRows();
            $reportName  = $this->getReportName();
            $windowLabel = $this->getWindowLabel();

            // Remove "Code" and "Banking Type"
            $columnsToRemove = ['Code', 'code', 'CODE', 'Branch Code', 'branch_code', 'Banking Type'];
            $rows = collect($rawRows)->map(function ($row) use ($columnsToRemove) {
                foreach ($columnsToRemove as $col) { unset($row[$col]); }
                return $row;
            })->all();

            // Load KPI categories and definitions
            $kpiCategories = collect(\Illuminate\Support\Facades\DB::table('k_p_i_categories')->orderBy('id')->get());
            $kpiDefs = collect(\Illuminate\Support\Facades\DB::table('k_p_i_s')->orderBy('id')->get());
            $kpiCategoryMap = [];
            foreach ($kpiDefs as $def) {
                $cat = $kpiCategories->firstWhere('id', $def->category_id);
                $kpiCategoryMap[strtoupper(trim($def->name))] = $cat->name ?? 'Other KPIs';
            }

            // Build the ordered column list — only "Branch" is fixed
            $headings = !empty($rows) ? array_keys($rows[0]) : [];
            $excludedColumns = ['District', 'district', 'district_name', 'Banking Type'];
            $fixedColumnNames = ['Branch'];
            $fixedCols = [];
            $kpiColsRaw = [];
            foreach ($headings as $h) {
                if (in_array($h, $excludedColumns, true)) continue;
                if (in_array($h, $fixedColumnNames, true)) {
                    $fixedCols[] = $h;
                } else {
                    $kpiColsRaw[] = $h;
                }
            }
            $groupedKpiCols = [];
            foreach ($kpiColsRaw as $kpi) {
                $cat = $kpiCategoryMap[strtoupper(trim($kpi))] ?? 'Other KPIs';
                $groupedKpiCols[$cat][] = $kpi;
            }
            $orderedCategories = [];
            foreach ($kpiCategories as $cat) {
                if (!empty($groupedKpiCols[$cat->name])) {
                    $orderedCategories[$cat->name] = $groupedKpiCols[$cat->name];
                    unset($groupedKpiCols[$cat->name]);
                }
            }
            foreach ($groupedKpiCols as $catName => $cols) {
                $orderedCategories[$catName] = $cols;
            }
            $orderedKpiCols = [];
            foreach ($orderedCategories as $catKpis) {
                foreach ($catKpis as $kpi) { $orderedKpiCols[] = $kpi; }
            }
            $allCols = array_merge($fixedCols, $orderedKpiCols);

            $categoryClassMap = [
                'Major KPIs'      => 'cat-major',
                'Digital KPIs'    => 'cat-digital',
                'Activation KPIs' => 'cat-activation',
                'Other KPIs'      => 'cat-other',
            ];
            $kpiColClassMap = [
                'Major KPIs'      => 'kpi-col-major',
                'Digital KPIs'    => 'kpi-col-digital',
                'Activation KPIs' => 'kpi-col-activation',
                'Other KPIs'      => 'kpi-col-other',
            ];

            // Group rows by district
            $groupColumn = 'District';
            if (!empty($headings) && !in_array($groupColumn, $headings, true)) {
                $groupColumn = $headings[0] ?? null;
            }
            $groupedRows = collect($rows)->groupBy(function ($row) use ($groupColumn) {
                return $row[$groupColumn] ?? 'Unknown';
            })->sortKeys();

            // Numeric columns and grand totals
            $nonNumeric = [$groupColumn, 'Branch'];
            $numericColumns = [];
            foreach ($orderedKpiCols as $col) {
                if (in_array($col, $nonNumeric, true)) continue;
                if (is_numeric($rows[0][$col] ?? null)) {
                    $numericColumns[] = $col;
                }
            }
            $grandTotals = [];
            foreach ($numericColumns as $col) {
                $grandTotals[$col] = collect($rows)->sum(fn($r) => (float) ($r[$col] ?? 0));
            }
        @endphp

        <!-- Filter Bar -->
            <div class="dashboard-filter-form">
                {{ $this->form }}
            </div>
        <div class="dashboard-filters">
         
            <div class="action-buttons">
                <!-- <button type="submit"
                        style="background-color: #4f46e5; color: white;"
                     onmouseover="this.style.backgroundColor='#4338ca'"
                        onmouseout="this.style.backgroundColor='#4f46e5'">
                 <x-heroicon-o-arrow-path /> 
                        <x-heroicon-o-arrow-down-tray />
                    Generate
                </button> -->
                <!-- <button type="button"
                        wire:click="exportToExcel"
                        style="background-color: white; color: #374151; border: 1px solid #d1d5db !important;"
                        onmouseover="this.style.backgroundColor='#f9fafb'"
                        onmouseout="this.style.backgroundColor='white'">
                    <x-heroicon-o-arrow-down-tray />
                    Export
                </button> -->
            </div>
        </div>

        <!-- KPI Cards (grid wrapper restored) -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon indigo"><x-heroicon-o-document-text /></div>
                <div style="min-width: 0;">
                    <p class="kpi-title">Selected Report</p>
                    <p class="kpi-value">{{ $reportName }}</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon blue"><x-heroicon-o-calendar /></div>
                <div style="min-width: 0;">
                    <p class="kpi-title">Reporting Period</p>
                    <p class="kpi-value">{{ $windowLabel }}</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon emerald"><x-heroicon-o-building-office-2 /></div>
                <div style="min-width: 0;">
                    <p class="kpi-title">Districts</p>
                    <p class="kpi-value">{{ $groupedRows->count() }}</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon amber"><x-heroicon-o-user-group /></div>
                <div style="min-width: 0;">
                    <p class="kpi-title">Branches</p>
                    <p class="kpi-value">{{ count($rows) }}</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon red"><x-heroicon-o-clock /></div>
                <div style="min-width: 0;">
                    <p class="kpi-title">Last Sync</p>
                    <p class="kpi-value">{{ now()->format('H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="dashboard-table-wrapper" x-data="{ expandedGroups: {} }">
            @if (empty($rows) || count($rows) === 0)
                <div class="empty-state">
                    <x-heroicon-o-inbox style="margin: 0 auto; color: #d1d5db;" />
                    <p class="empty-title">No data available</p>
                    <p style="margin: 0;">There are no records for the selected report and period.</p>
                </div>
            @else
                <div class="dashboard-table-scroll">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th class="th-cat cat-fixed sticky-first" colspan="{{ count($fixedCols) }}" style="left: 0;">
                                    District / Branch
                                </th>
                                @foreach ($orderedCategories as $catName => $catKpis)
                                    <th class="th-cat {{ $categoryClassMap[$catName] ?? 'cat-other' }}" colspan="{{ count($catKpis) }}">
                                        {{ $catName }}
                                    </th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($allCols as $col)
                                    @php
                                        $isFirst  = $col === 'Branch';
                                        $catName  = $kpiCategoryMap[strtoupper(trim($col))] ?? null;
                                        $colClass = $catName ? ($kpiColClassMap[$catName] ?? '') : '';
                                    @endphp
                                    <th class="th-main {{ $isFirst ? 'sticky-first' : '' }} {{ $colClass }}">
                                        {{ $isFirst ? 'District / Branch' : $col }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedRows as $districtName => $districtRows)
                                @php
                                    $groupKey = 'g_' . md5($districtName);
                                    $districtTotals = [];
                                    foreach ($numericColumns as $col) {
                                        $districtTotals[$col] = $districtRows->sum(fn($r) => (float) ($r[$col] ?? 0));
                                    }
                                @endphp

                                <tr class="row-district"
                                    @click="expandedGroups['{{ $groupKey }}'] = !expandedGroups['{{ $groupKey }}']">
                                    @foreach ($allCols as $col)
                                        @php $isFirst = $col === 'Branch'; @endphp
                                        @if ($isFirst)
                                            <td class="sticky-first">
                                                <span style="display: inline-flex; align-items: center; gap: 8px;">
                                                    <x-heroicon-s-chevron-down
                                                        class="district-chevron"
                                                        ::class="expandedGroups['{{ $groupKey }}'] ? 'district-chevron open' : 'district-chevron closed'"
                                                        style="color: #4f46e5;" />
                                                    <span>{{ $districtName }}</span>
                                                    <span style="font-size: 10px; font-weight: 600; background: #c7d2fe; color: #3730a3; padding: 1px 8px; border-radius: 10px;">
                                                        {{ $districtRows->count() }}
                                                    </span>
                                                </span>
                                            </td>
                                        @elseif (isset($districtTotals[$col]))
                                            <td class="text-right tabular-nums font-bold">
                                                {{ number_format($districtTotals[$col], 2) }}
                                            </td>
                                        @else
                                            <td class="text-center" style="opacity: 0.5;">—</td>
                                        @endif
                                    @endforeach
                                </tr>

                                @foreach ($districtRows as $row)
                                    <tr class="row-data"
                                        x-show="expandedGroups['{{ $groupKey }}']"
                                        x-transition.opacity>
                                        @foreach ($allCols as $col)
                                            @php
                                                $value     = $row[$col] ?? '';
                                                $isFirst   = $col === 'Branch';
                                                $isNumeric = is_numeric($value);
                                            @endphp
                                            <td class="{{ $isFirst ? 'sticky-first font-semibold' : '' }} {{ $isNumeric ? 'text-right tabular-nums' : '' }}"
                                                @if ($isFirst) style="padding-left: 28px;" @endif>
                                                {{ $isNumeric ? number_format((float) $value, 2) : $value }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr class="row-grand-total">
                                @foreach ($allCols as $col)
                                    @php $isFirst = $col === 'Branch'; @endphp
                                    @if ($isFirst)
                                        <td class="sticky-first">GRAND TOTAL</td>
                                    @elseif (isset($grandTotals[$col]))
                                        <td class="text-right tabular-nums">
                                            {{ number_format($grandTotals[$col], 2) }}
                                        </td>
                                    @else
                                        <td class="text-center">—</td>
                                    @endif
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="table-footer">
                    <div class="legend-bar">
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <span class="legend-dot" style="background-color: #3b82f6;"></span> Major
                        </span>
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <span class="legend-dot" style="background-color: #8b5cf6;"></span> Digital
                        </span>
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <span class="legend-dot" style="background-color: #10b981;"></span> Activation
                        </span>
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <span class="legend-dot" style="background-color: #f59e0b;"></span> Other
                        </span>
                    </div>
                    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <span><strong>{{ $groupedRows->count() }}</strong> districts · <strong>{{ count($rows) }}</strong> branches</span>
                        <span style="font-family: monospace;">{{ now()->format('d M Y H:i') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </form>
</x-filament-panels::page>