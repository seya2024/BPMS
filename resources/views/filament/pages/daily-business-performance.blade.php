<x-filament-panels::page>
 
 <style>
/* =========================================
PURE CSS DASHBOARD LAYOUT (Responsive + Tabs)
========================================= */

    .pure-dash-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
            "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        box-sizing: border-box;
    }

    .pure-dash-container * { box-sizing: border-box; }

    /* Filter Bar */
    .pure-filter-bar {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        gap: 16px;
        background: white;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        width: 100%;
    }

    .pure-filter-bar .fi-fo {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        align-items: flex-end !important;
        width: 100% !important;
        flex: 1 !important;
    }

    .pure-filter-bar .fi-fo > div {
        flex: 1 1 200px !important;
        min-width: 150px !important;
    }

    .pure-filter-bar .fi-fo-grid { display: contents !important; }

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
        width: 100%;
        justify-content: flex-end;
        margin-top: 8px;
    }
    @media (min-width: 1024px) {
        .pure-action-buttons { width: auto; margin-top: 0; }
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

    .pure-btn-primary { background-color: #2563eb; color: white; }
    .pure-btn-primary:hover { background-color: #1d4ed8; }
    .pure-btn-outline { background-color: white; color: #374151; border: 1px solid #d1d5db; }
    .pure-btn-outline:hover { background-color: #f9fafb; }
    .pure-btn svg { width: 16px !important; height: 16px !important; }

    /* KPI Grid */
    .pure-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        width: 100%;
    }

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

    .pure-kpi-title {
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin: 0 0 2px 0;
    }

    .pure-kpi-value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
        margin: 0;
    }

    .pure-kpi-badge {
        font-size: 10px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
    }

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
        font-size: 13px;
        font-weight: 700;
        color: #dc2626;
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0 0 12px 0;
    }

    .pure-attention-title svg { width: 16px !important; height: 16px !important; }
    .pure-attention-table { width: 100%; font-size: 11px; text-align: left; border-collapse: collapse; }
    .pure-attention-table th { color: #6b7280; font-weight: 600; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb; }
    .pure-attention-table td { padding: 6px 0; border-bottom: 1px solid #f3f4f6; color: #374151; }
    .pure-attention-table td:last-child { color: #dc2626; font-weight: 500; }

    /* Top Section Grid */
    .pure-top-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        width: 100%;
    }
    @media (min-width: 1024px) {
        .pure-top-section { grid-template-columns: 3fr 1fr; }
    }

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

    .pure-table-scroll { overflow-x: auto; width: 100%; }

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

    /* Headers - Like Footer */
    .pure-th-main {
        background-color: #1e3a8a !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        padding: 8px 6px;
        border-bottom: 1px solid #3b5998;
    }

    .pure-th-sub {
        background-color: #2a4d8a !important;
        color: #e5e7eb !important;
        font-weight: 600;
        font-size: 9px;
        padding: 4px 6px;
        border-bottom: 1px solid #3b5998;
        text-transform: uppercase;
    }

    /* Rows */
    .pure-row-district {
        background-color: #f8fafc;
        font-weight: 700;
        color: #1e293b;
        cursor: pointer;
        user-select: none;
        transition: background-color 0.15s;
    }
    .pure-row-district:hover { background-color: #eef2f7; }
    .pure-row-district td { border-bottom: 1px solid #e2e8f0; }

    .pure-row-branch { background-color: #ffffff; color: #475569; }
    .pure-row-branch:hover { background-color: #f8fafc; }
    .pure-row-branch td { border-bottom: 1px solid #f1f5f9; }

    .pure-row-grand-total {
        background-color: #1e3a8a;
        color: #ffffff;
        font-weight: 700;
        font-size: 11px;
    }

    .pure-row-grand-total td {
        border-right: 1px solid #3b5998;
        border-bottom: none;
        padding: 8px 6px;
    }
    .pure-row-grand-total td:last-child { border-right: none; }

    /* Utilities */
    .pure-text-red { color: #dc2626 !important; }
    .pure-text-green { color: #16a34a !important; }
    .pure-text-amber { color: #d97706 !important; }
    .pure-font-bold { font-weight: 700; }

    /* Type badge */
    .pure-type-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.025em;
    }
    .pure-type-badge.ifb {
        background-color: #eef2ff;
        color: #4338ca;
    }
    .pure-type-badge.conv {
        background-color: #f0fdf4;
        color: #15803d;
    }
    .pure-type-badge.mixed {
        background-color: #fef3c7;
        color: #92400e;
    }

    /* Sticky Columns */
    .pure-sticky-col {
        position: sticky;
        left: 0;
        z-index: 10;
        text-align: left;
        padding-left: 12px !important;
    }

    .pure-row-district .pure-sticky-col { background-color: #f8fafc; }
    .pure-row-district:hover .pure-sticky-col { background-color: #eef2f7; }
    .pure-row-branch .pure-sticky-col { background-color: #ffffff; }
    .pure-row-grand-total .pure-sticky-col { background-color: #1e3a8a; color: #ffffff; }
    .pure-th-main.pure-sticky-col { background-color: #1e3a8a !important; z-index: 20; }
    .pure-th-sub.pure-sticky-col { background-color: #2a4d8a !important; z-index: 20; }

    /* Chevron rotation */
    .district-chevron {
        transition: transform 0.2s ease;
        display: inline-block;
    }
    .district-chevron.open { transform: rotate(0deg); }
    .district-chevron.closed { transform: rotate(-90deg); }

    /* Legend */
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

    /* Tabs */
    .kpi-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 8px;
        flex-wrap: wrap;
    }
    
    .kpi-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid transparent;
        background: #f3f4f6;
        color: #4b5563;
        transition: all 0.2s;
        outline: none;
    }
    
    .kpi-tab:hover { background: #e5e7eb; }
    .kpi-tab.active {
        background: #1e3a8a;
        color: white;
        border-color: #1e3a8a;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .kpi-tab svg { width: 16px !important; height: 16px !important; }
</style>

<!-- Alpine.js Data Scope for Tabs + District collapse -->
<div class="pure-dash-container" x-data="{ activeTab: 'major', expandedDistricts: {} }">

    @php
        $dashboardData = $this->getDashboardData();
        $rows = $dashboardData['table_data'] ?? [];
    @endphp

    <!-- Filters -->
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

    <!-- KPI Summary Cards -->
    <div class="pure-top-section">
        <div class="pure-kpi-grid">
            <div class="pure-kpi-card">
                <div class="pure-kpi-icon blue"><x-heroicon-o-building-office-2 /></div>
                <div>
                    <p class="pure-kpi-title">Total Branches</p>
                    <p class="pure-kpi-value">{{ collect($rows)->sum(fn($d) => count($d['branches'])) }}</p>
                </div>
            </div>
            <div class="pure-kpi-card">
                <div class="pure-kpi-icon green"><x-heroicon-o-check-circle /></div>
                <div>
                    <p class="pure-kpi-title">Districts</p>
                    <p class="pure-kpi-value">{{ count($rows) }}</p>
                </div>
            </div>
            <div class="pure-kpi-card">
                <div class="pure-kpi-icon indigo"><x-heroicon-o-chart-bar /></div>
                <div>
                    <p class="pure-kpi-title">Avg Achievement</p>
                    <p class="pure-kpi-value">94.6%</p>
                </div>
            </div>
        </div>

        <div class="pure-attention-widget">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 class="pure-attention-title" style="margin: 0;">
                    <x-heroicon-s-exclamation-triangle /> Top Attention Branches
                </h3>
                <a href="#" style="font-size: 10px; color: #2563eb; text-decoration: none; font-weight: 500;">View All</a>
            </div>
            <table class="pure-attention-table">
                <thead>
                    <tr><th>Branch</th><th>District</th><th>Issues</th></tr>
                </thead>
                <tbody>
                    <tr><td>Agaro</td><td>Jimma</td><td>4 KPIs below target</td></tr>
                    <tr><td>Bahir Dar</td><td>Bahir Dar</td><td>3 KPIs below target</td></tr>
                    <tr><td>Hawassa</td><td>SNNP</td><td>2 KPIs below target</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabs -->
    <div class="kpi-tabs">
        <button @click="activeTab = 'major'" :class="{'active': activeTab === 'major'}" class="kpi-tab">
            <x-heroicon-o-presentation-chart-bar /> Major KPIs
        </button>
        <button @click="activeTab = 'digital'" :class="{'active': activeTab === 'digital'}" class="kpi-tab">
            <x-heroicon-o-device-phone-mobile /> Digital KPIs
        </button>
        <button @click="activeTab = 'activation'" :class="{'active': activeTab === 'activation'}" class="kpi-tab">
            <x-heroicon-o-bolt /> Activation KPIs
        </button>
        <button @click="activeTab = 'other'" :class="{'active': activeTab === 'other'}" class="kpi-tab">
            <x-heroicon-o-squares-plus /> Other KPIs
        </button>
    </div>

    @if(empty($rows))
        <div class="pure-table-wrapper" style="padding: 40px; text-align: center; color: #6b7280;">
            <x-heroicon-o-inbox style="width: 48px; height: 48px; margin: 0 auto 12px; color: #d1d5db;" />
            <p style="font-weight: 600; font-size: 16px;">No data available</p>
            <p style="font-size: 14px;">Please adjust your filters or ensure branch data exists.</p>
        </div>
    @else

    <!-- =========================================
         MAJOR KPIs TABLE
         ========================================= -->
    <div class="pure-table-wrapper" x-show="activeTab === 'major'">
        <div class="pure-table-scroll">
            <table class="pure-table">
                <thead>
                    <tr>
                        <th class="pure-th-main pure-sticky-col" style="width: 40px;">#</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 40px; width: 200px;">District / Branch</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 240px; width: 120px;">Banking Type</th>
                        <th colspan="5" class="pure-th-main">DEPOSIT</th>
                        <th colspan="5" class="pure-th-main">NEW ACCOUNTS</th>
                        <th colspan="5" class="pure-th-main">FCY</th>
                        <th colspan="5" class="pure-th-main">LOANS</th>
                        <th colspan="2" class="pure-th-main">Overall</th>
                    </tr>
                    <tr>
                        <th class="pure-th-sub pure-sticky-col" style="left: 0;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 40px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 240px;"></th>
                        @for ($i = 0; $i < 4; $i++)
                            <th class="pure-th-sub">Actual</th>
                            <th class="pure-th-sub">Target</th>
                            <th class="pure-th-sub">Variance</th>
                            <th class="pure-th-sub">Achievement</th>
                            <th class="pure-th-sub">Trend</th>
                        @endfor
                        <th class="pure-th-sub">Score</th>
                        <th class="pure-th-sub">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $group)
                        @php $districtKey = $group['district_name']; @endphp
                        <tr class="pure-row-district"
                            @click="expandedDistricts['{{ $districtKey }}'] = !expandedDistricts['{{ $districtKey }}']">
                            <td class="pure-sticky-col" style="width: 40px;">
                                <x-heroicon-s-chevron-down
                                    class="district-chevron"
                                    ::class="expandedDistricts['{{ $districtKey }}'] ? 'district-chevron open' : 'district-chevron closed'"
                                    style="width: 12px; height: 12px; color: #64748b;" />
                            </td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">{{ $group['district_name'] }}</td>
                            <td class="pure-sticky-col" style="left: 240px;">
                                @php $dt = $group['district_type'] ?? 'Mixed'; @endphp
                                <span class="pure-type-badge {{ $dt === 'IFB' ? 'ifb' : ($dt === 'Conventional' ? 'conv' : 'mixed') }}">
                                    {{ $dt }}
                                </span>
                            </td>
                            @php $d = $group['district_data']['major']; @endphp
                            @foreach(['deposit','accounts','fcy','loans'] as $kpi)
                                @php $v = $d[$kpi]; @endphp
                                <td>{{ $v['a'] }}</td><td>{{ $v['t'] }}</td>
                                <td class="{{ strpos((string)$v['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$v['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $v['v'] }}</td>
                                <td>{{ $v['g'] }}%</td>
                                <td class="{{ $v['tr'] === '↓' ? 'pure-text-red' : ($v['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $v['tr'] }}</td>
                            @endforeach
                            <td class="pure-font-bold">{{ $group['district_data']['overall']['score'] }}%</td>
                            <td class="{{ $group['district_data']['overall']['trend'] === 'down' ? 'pure-text-red' : ($group['district_data']['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">
                                {{ $group['district_data']['overall']['trend'] === 'up' ? '▲' : ($group['district_data']['overall']['trend'] === 'down' ? '▼' : '→') }}
                            </td>
                        </tr>

                        @foreach($group['branches'] as $branch)
                            <tr class="pure-row-branch"
                                x-show="expandedDistricts['{{ $districtKey }}']"
                                x-transition.opacity>
                                <td class="pure-sticky-col" style="width: 40px;">{{ explode('.', $branch['name'])[0] }}.</td>
                                <td class="pure-sticky-col" style="left: 40px;">{{ trim(explode('.', $branch['name'])[1] ?? $branch['name']) }}</td>
                                <td class="pure-sticky-col" style="left: 240px;">
                                    @php $bt = $branch['type'] ?? 'Conventional'; @endphp
                                    <span class="pure-type-badge {{ $bt === 'IFB' ? 'ifb' : 'conv' }}">
                                        {{ $bt }}
                                    </span>
                                </td>
                                @php $b = $branch['data']['major']; @endphp
                                @foreach(['deposit','accounts','fcy','loans'] as $kpi)
                                    @php $v = $b[$kpi]; @endphp
                                    <td>{{ $v['a'] }}</td><td>{{ $v['t'] }}</td>
                                    <td class="{{ strpos((string)$v['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$v['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $v['v'] }}</td>
                                    <td>{{ $v['g'] }}%</td>
                                    <td class="{{ $v['tr'] === '↓' ? 'pure-text-red' : ($v['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $v['tr'] }}</td>
                                @endforeach
                                <td class="{{ $branch['data']['overall']['score'] < 90 ? 'pure-text-red' : ($branch['data']['overall']['score'] < 95 ? 'pure-text-amber' : 'pure-text-green') }}">{{ $branch['data']['overall']['score'] }}%</td>
                                <td class="{{ $branch['data']['overall']['trend'] === 'down' ? 'pure-text-red' : ($branch['data']['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">
                                    {{ $branch['data']['overall']['trend'] === 'up' ? '▲' : ($branch['data']['overall']['trend'] === 'down' ? '▼' : '→') }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- =========================================
         DIGITAL KPIs TABLE
         ========================================= -->
    <div class="pure-table-wrapper" x-show="activeTab === 'digital'">
        <div class="pure-table-scroll">
            <table class="pure-table">
                <thead>
                    <tr>
                        <th class="pure-th-main pure-sticky-col" style="width: 40px;">#</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 40px; width: 200px;">District / Branch</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 240px; width: 120px;">Banking Type</th>
                        <th colspan="5" class="pure-th-main">CARD SUBSCRIPTION</th>
                        <th colspan="5" class="pure-th-main">POS SUBSCRIPTION</th>
                        <th colspan="5" class="pure-th-main">SUPER APP SUBSCRIPTION</th>
                        <th colspan="2" class="pure-th-main">Overall</th>
                    </tr>
                    <tr>
                        <th class="pure-th-sub pure-sticky-col" style="left: 0;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 40px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 240px;"></th>
                        @for ($i = 0; $i < 3; $i++)
                            <th class="pure-th-sub">Actual</th>
                            <th class="pure-th-sub">Target</th>
                            <th class="pure-th-sub">Variance</th>
                            <th class="pure-th-sub">Achievement</th>
                            <th class="pure-th-sub">Trend</th>
                        @endfor
                        <th class="pure-th-sub">Score</th>
                        <th class="pure-th-sub">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $group)
                        @php $districtKey = 'digital_' . $group['district_name']; @endphp
                        <tr class="pure-row-district"
                            @click="expandedDistricts['{{ $districtKey }}'] = !expandedDistricts['{{ $districtKey }}']">
                            <td class="pure-sticky-col" style="width: 40px;">
                                <x-heroicon-s-chevron-down
                                    class="district-chevron"
                                    ::class="expandedDistricts['{{ $districtKey }}'] ? 'district-chevron open' : 'district-chevron closed'"
                                    style="width: 12px; height: 12px; color: #64748b;" />
                            </td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">{{ $group['district_name'] }}</td>
                            <td class="pure-sticky-col" style="left: 240px;">
                                @php $dt = $group['district_type'] ?? 'Mixed'; @endphp
                                <span class="pure-type-badge {{ $dt === 'IFB' ? 'ifb' : ($dt === 'Conventional' ? 'conv' : 'mixed') }}">
                                    {{ $dt }}
                                </span>
                            </td>
                            @php $d = $group['district_data']['digital']; @endphp
                            @foreach(['card_sub','pos_sub','super_sub'] as $kpi)
                                @php $v = $d[$kpi]; @endphp
                                <td>{{ $v['a'] }}</td><td>{{ $v['t'] }}</td>
                                <td class="{{ strpos((string)$v['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$v['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $v['v'] }}</td>
                                <td>{{ $v['g'] }}%</td>
                                <td class="{{ $v['tr'] === '↓' ? 'pure-text-red' : ($v['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $v['tr'] }}</td>
                            @endforeach
                            <td class="pure-font-bold">{{ $group['district_data']['overall']['score'] }}%</td>
                            <td class="{{ $group['district_data']['overall']['trend'] === 'down' ? 'pure-text-red' : ($group['district_data']['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">
                                {{ $group['district_data']['overall']['trend'] === 'up' ? '▲' : ($group['district_data']['overall']['trend'] === 'down' ? '▼' : '→') }}
                            </td>
                        </tr>
                        @foreach($group['branches'] as $branch)
                            <tr class="pure-row-branch"
                                x-show="expandedDistricts['{{ $districtKey }}']"
                                x-transition.opacity>
                                <td class="pure-sticky-col" style="width: 40px;">{{ explode('.', $branch['name'])[0] }}.</td>
                                <td class="pure-sticky-col" style="left: 40px;">{{ trim(explode('.', $branch['name'])[1] ?? $branch['name']) }}</td>
                                <td class="pure-sticky-col" style="left: 240px;">
                                    @php $bt = $branch['type'] ?? 'Conventional'; @endphp
                                    <span class="pure-type-badge {{ $bt === 'IFB' ? 'ifb' : 'conv' }}">
                                        {{ $bt }}
                                    </span>
                                </td>
                                @php $b = $branch['data']['digital']; @endphp
                                @foreach(['card_sub','pos_sub','super_sub'] as $kpi)
                                    @php $v = $b[$kpi]; @endphp
                                    <td>{{ $v['a'] }}</td><td>{{ $v['t'] }}</td>
                                    <td class="{{ strpos((string)$v['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$v['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $v['v'] }}</td>
                                    <td>{{ $v['g'] }}%</td>
                                    <td class="{{ $v['tr'] === '↓' ? 'pure-text-red' : ($v['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $v['tr'] }}</td>
                                @endforeach
                                <td class="{{ $branch['data']['overall']['score'] < 90 ? 'pure-text-red' : ($branch['data']['overall']['score'] < 95 ? 'pure-text-amber' : 'pure-text-green') }}">{{ $branch['data']['overall']['score'] }}%</td>
                                <td class="{{ $branch['data']['overall']['trend'] === 'down' ? 'pure-text-red' : ($branch['data']['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">
                                    {{ $branch['data']['overall']['trend'] === 'up' ? '▲' : ($branch['data']['overall']['trend'] === 'down' ? '▼' : '→') }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- =========================================
         ACTIVATION KPIs TABLE
         ========================================= -->
    <div class="pure-table-wrapper" x-show="activeTab === 'activation'">
        <div class="pure-table-scroll">
            <table class="pure-table">
                <thead>
                    <tr>
                        <th class="pure-th-main pure-sticky-col" style="width: 40px;">#</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 40px; width: 200px;">District / Branch</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 240px; width: 120px;">Banking Type</th>
                        <th colspan="5" class="pure-th-main">ACCOUNT ACTIVATION</th>
                        <th colspan="5" class="pure-th-main">CARD ACTIVATION</th>
                        <th colspan="5" class="pure-th-main">SUPER APP ACTIVATION</th>
                        <th colspan="2" class="pure-th-main">Overall</th>
                    </tr>
                    <tr>
                        <th class="pure-th-sub pure-sticky-col" style="left: 0;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 40px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 240px;"></th>
                        @for ($i = 0; $i < 3; $i++)
                            <th class="pure-th-sub">Actual</th>
                            <th class="pure-th-sub">Target</th>
                            <th class="pure-th-sub">Variance</th>
                            <th class="pure-th-sub">Achievement</th>
                            <th class="pure-th-sub">Trend</th>
                        @endfor
                        <th class="pure-th-sub">Score</th>
                        <th class="pure-th-sub">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $group)
                        @php $districtKey = 'activation_' . $group['district_name']; @endphp
                        <tr class="pure-row-district"
                            @click="expandedDistricts['{{ $districtKey }}'] = !expandedDistricts['{{ $districtKey }}']">
                            <td class="pure-sticky-col" style="width: 40px;">
                                <x-heroicon-s-chevron-down
                                    class="district-chevron"
                                    ::class="expandedDistricts['{{ $districtKey }}'] ? 'district-chevron open' : 'district-chevron closed'"
                                    style="width: 12px; height: 12px; color: #64748b;" />
                            </td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">{{ $group['district_name'] }}</td>
                            <td class="pure-sticky-col" style="left: 240px;">
                                @php $dt = $group['district_type'] ?? 'Mixed'; @endphp
                                <span class="pure-type-badge {{ $dt === 'IFB' ? 'ifb' : ($dt === 'Conventional' ? 'conv' : 'mixed') }}">
                                    {{ $dt }}
                                </span>
                            </td>
                            @php $d = $group['district_data']['activation']; @endphp
                            @foreach(['acc_act','card_act','super_act'] as $kpi)
                                @php $v = $d[$kpi]; @endphp
                                <td>{{ $v['a'] }}</td><td>{{ $v['t'] }}</td>
                                <td class="{{ strpos((string)$v['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$v['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $v['v'] }}</td>
                                <td>{{ $v['g'] }}%</td>
                                <td class="{{ $v['tr'] === '↓' ? 'pure-text-red' : ($v['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $v['tr'] }}</td>
                            @endforeach
                            <td class="pure-font-bold">{{ $group['district_data']['overall']['score'] }}%</td>
                            <td class="{{ $group['district_data']['overall']['trend'] === 'down' ? 'pure-text-red' : ($group['district_data']['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">
                                {{ $group['district_data']['overall']['trend'] === 'up' ? '▲' : ($group['district_data']['overall']['trend'] === 'down' ? '▼' : '→') }}
                            </td>
                        </tr>
                        @foreach($group['branches'] as $branch)
                            <tr class="pure-row-branch"
                                x-show="expandedDistricts['{{ $districtKey }}']"
                                x-transition.opacity>
                                <td class="pure-sticky-col" style="width: 40px;">{{ explode('.', $branch['name'])[0] }}.</td>
                                <td class="pure-sticky-col" style="left: 40px;">{{ trim(explode('.', $branch['name'])[1] ?? $branch['name']) }}</td>
                                <td class="pure-sticky-col" style="left: 240px;">
                                    @php $bt = $branch['type'] ?? 'Conventional'; @endphp
                                    <span class="pure-type-badge {{ $bt === 'IFB' ? 'ifb' : 'conv' }}">
                                        {{ $bt }}
                                    </span>
                                </td>
                                @php $b = $branch['data']['activation']; @endphp
                                @foreach(['acc_act','card_act','super_act'] as $kpi)
                                    @php $v = $b[$kpi]; @endphp
                                    <td>{{ $v['a'] }}</td><td>{{ $v['t'] }}</td>
                                    <td class="{{ strpos((string)$v['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$v['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $v['v'] }}</td>
                                    <td>{{ $v['g'] }}%</td>
                                    <td class="{{ $v['tr'] === '↓' ? 'pure-text-red' : ($v['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $v['tr'] }}</td>
                                @endforeach
                                <td class="{{ $branch['data']['overall']['score'] < 90 ? 'pure-text-red' : ($branch['data']['overall']['score'] < 95 ? 'pure-text-amber' : 'pure-text-green') }}">{{ $branch['data']['overall']['score'] }}%</td>
                                <td class="{{ $branch['data']['overall']['trend'] === 'down' ? 'pure-text-red' : ($branch['data']['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">
                                    {{ $branch['data']['overall']['trend'] === 'up' ? '▲' : ($branch['data']['overall']['trend'] === 'down' ? '▼' : '→') }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- =========================================
         OTHER KPIs TABLE
         ========================================= -->
    <div class="pure-table-wrapper" x-show="activeTab === 'other'">
        <div class="pure-table-scroll">
            <table class="pure-table">
                <thead>
                    <tr>
                        <th class="pure-th-main pure-sticky-col" style="width: 40px;">#</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 40px; width: 200px;">District / Branch</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 240px; width: 120px;">Banking Type</th>
                        <th colspan="5" class="pure-th-main">SERVICE QUALITY</th>
                        <th colspan="5" class="pure-th-main">ATM TRANSACTION</th>
                        <th colspan="5" class="pure-th-main">POS TRANSACTION</th>
                        <th colspan="5" class="pure-th-main">NPS</th>
                        <th colspan="2" class="pure-th-main">Overall</th>
                    </tr>
                    <tr>
                        <th class="pure-th-sub pure-sticky-col" style="left: 0;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 40px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 240px;"></th>
                        @for ($i = 0; $i < 4; $i++)
                            <th class="pure-th-sub">Actual</th>
                            <th class="pure-th-sub">Target</th>
                            <th class="pure-th-sub">Variance</th>
                            <th class="pure-th-sub">Achievement</th>
                            <th class="pure-th-sub">Trend</th>
                        @endfor
                        <th class="pure-th-sub">Score</th>
                        <th class="pure-th-sub">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $group)
                        @php $districtKey = 'other_' . $group['district_name']; @endphp
                        <tr class="pure-row-district"
                            @click="expandedDistricts['{{ $districtKey }}'] = !expandedDistricts['{{ $districtKey }}']">
                            <td class="pure-sticky-col" style="width: 40px;">
                                <x-heroicon-s-chevron-down
                                    class="district-chevron"
                                    ::class="expandedDistricts['{{ $districtKey }}'] ? 'district-chevron open' : 'district-chevron closed'"
                                    style="width: 12px; height: 12px; color: #64748b;" />
                            </td>
                            <td class="pure-sticky-col pure-font-bold" style="left: 40px;">{{ $group['district_name'] }}</td>
                            <td class="pure-sticky-col" style="left: 240px;">
                                @php $dt = $group['district_type'] ?? 'Mixed'; @endphp
                                <span class="pure-type-badge {{ $dt === 'IFB' ? 'ifb' : ($dt === 'Conventional' ? 'conv' : 'mixed') }}">
                                    {{ $dt }}
                                </span>
                            </td>
                            @php $d = $group['district_data']['other']; @endphp
                            @foreach(['service_quality','atm_txn','pos_txn','nps'] as $kpi)
                                @php $v = $d[$kpi]; @endphp
                                <td>{{ $v['a'] }}</td><td>{{ $v['t'] }}</td>
                                <td class="{{ strpos((string)$v['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$v['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $v['v'] }}</td>
                                <td>{{ $v['g'] }}%</td>
                                <td class="{{ $v['tr'] === '↓' ? 'pure-text-red' : ($v['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $v['tr'] }}</td>
                            @endforeach
                            <td class="pure-font-bold">{{ $group['district_data']['overall']['score'] }}%</td>
                            <td class="{{ $group['district_data']['overall']['trend'] === 'down' ? 'pure-text-red' : ($group['district_data']['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">
                                {{ $group['district_data']['overall']['trend'] === 'up' ? '▲' : ($group['district_data']['overall']['trend'] === 'down' ? '▼' : '→') }}
                            </td>
                        </tr>
                        @foreach($group['branches'] as $branch)
                            <tr class="pure-row-branch"
                                x-show="expandedDistricts['{{ $districtKey }}']"
                                x-transition.opacity>
                                <td class="pure-sticky-col" style="width: 40px;">{{ explode('.', $branch['name'])[0] }}.</td>
                                <td class="pure-sticky-col" style="left: 40px;">{{ trim(explode('.', $branch['name'])[1] ?? $branch['name']) }}</td>
                                <td class="pure-sticky-col" style="left: 240px;">
                                    @php $bt = $branch['type'] ?? 'Conventional'; @endphp
                                    <span class="pure-type-badge {{ $bt === 'IFB' ? 'ifb' : 'conv' }}">
                                        {{ $bt }}
                                    </span>
                                </td>
                                @php $b = $branch['data']['other']; @endphp
                                @foreach(['service_quality','atm_txn','pos_txn','nps'] as $kpi)
                                    @php $v = $b[$kpi]; @endphp
                                    <td>{{ $v['a'] }}</td><td>{{ $v['t'] }}</td>
                                    <td class="{{ strpos((string)$v['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$v['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $v['v'] }}</td>
                                    <td>{{ $v['g'] }}%</td>
                                    <td class="{{ $v['tr'] === '↓' ? 'pure-text-red' : ($v['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $v['tr'] }}</td>
                                @endforeach
                                <td class="{{ $branch['data']['overall']['score'] < 90 ? 'pure-text-red' : ($branch['data']['overall']['score'] < 95 ? 'pure-text-amber' : 'pure-text-green') }}">{{ $branch['data']['overall']['score'] }}%</td>
                                <td class="{{ $branch['data']['overall']['trend'] === 'down' ? 'pure-text-red' : ($branch['data']['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">
                                    {{ $branch['data']['overall']['trend'] === 'up' ? '▲' : ($branch['data']['overall']['trend'] === 'down' ? '▼' : '→') }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @endif

    <!-- Legend -->
    <div class="pure-table-legend">
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <span style="display: flex; align-items: center;">
                <span class="pure-legend-dot green"></span> On Target (≥ 100%)
            </span>
            <span style="display: flex; align-items: center;">
                <span class="pure-legend-dot amber"></span> Near Target (90 to 99%)
            </span>
            <span style="display: flex; align-items: center;">
                <span class="pure-legend-dot red"></span> Below Target (&lt; 90%)
            </span>
        </div>
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <span style="color: #16a34a; display: flex; align-items: center; gap: 2px;">
                <x-heroicon-s-arrow-up /> Improving
            </span>
            <span style="color: #6b7280; display: flex; align-items: center; gap: 2px;">
                <x-heroicon-s-arrow-right /> Stable
            </span>
            <span style="color: #dc2626; display: flex; align-items: center; gap: 2px;">
                <x-heroicon-s-arrow-down /> Declining
            </span>
        </div>
    </div>

</div>
</x-filament-panels::page>