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

    /* Filter Bar - Responsive */
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
        width: 100%;
        justify-content: flex-end;
        margin-top: 8px;
    }
    @media (min-width: 1024px) {
        .pure-action-buttons {
            width: auto;
            margin-top: 0;
        }
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

    .pure-btn-primary:hover {
        background-color: #1d4ed8;
    }

    .pure-btn-outline {
        background-color: white;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .pure-btn-outline:hover {
        background-color: #f9fafb;
    }

    .pure-btn svg {
        width: 16px !important;
        height: 16px !important;
    }

    /* KPI Grid - Responsive */
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

    .pure-kpi-icon svg {
        width: 24px !important;
        height: 24px !important;
    }

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

    .pure-attention-title svg {
        width: 16px !important;
        height: 16px !important;
    }

    .pure-attention-table {
        width: 100%;
        font-size: 11px;
        text-align: left;
        border-collapse: collapse;
    }

    .pure-attention-table th {
        color: #6b7280;
        font-weight: 600;
        padding-bottom: 6px;
        border-bottom: 1px solid #e5e7eb;
    }

    .pure-attention-table td {
        padding: 6px 0;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
    }

    .pure-attention-table td:last-child {
        color: #dc2626;
        font-weight: 500;
    }

    /* Responsive Top Section Grid */
    .pure-top-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        width: 100%;
    }
    @media (min-width: 1024px) {
        .pure-top-section {
            grid-template-columns: 3fr 1fr;
        }
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

    .pure-table th,
    .pure-table td {
        padding: 6px 8px;
        border-right: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .pure-table th:last-child,
    .pure-table td:last-child {
        border-right: none;
    }

    /* Header Colors - LIKE FOOTER */
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
    }

    .pure-row-district td {
        border-bottom: 1px solid #e2e8f0;
    }

    .pure-row-branch {
        background-color: #ffffff;
        color: #475569;
    }

    .pure-row-branch:hover {
        background-color: #f8fafc;
    }

    .pure-row-branch td {
        border-bottom: 1px solid #f1f5f9;
    }

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

    .pure-row-grand-total td:last-child {
        border-right: none;
    }

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
    .pure-th-main.pure-sticky-col { background-color: #1e3a8a !important; z-index: 20; }
    .pure-th-sub.pure-sticky-col { background-color: #2a4d8a !important; z-index: 20; }

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

    .pure-legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }

    .pure-legend-dot.green { background-color: #22c55e; }
    .pure-legend-dot.amber { background-color: #f59e0b; }
    .pure-legend-dot.red { background-color: #ef4444; }

    .pure-table-legend svg {
        width: 14px !important;
        height: 14px !important;
    }

    /* =========================================
       KPI TABS STYLING
       ========================================= */
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
    
    .kpi-tab:hover {
        background: #e5e7eb;
    }
    
    .kpi-tab.active {
        background: #1e3a8a;
        color: white;
        border-color: #1e3a8a;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .kpi-tab svg {
        width: 16px !important;
        height: 16px !important;
    }
</style>

<!-- Alpine.js Data Scope for Tabs -->
<div class="pure-dash-container" x-data="{ activeTab: 'major' }">

    <!-- Filters -->
    <div class="pure-filter-bar">
        <div class="fi-fo" style="flex: 1;">
            {{ $this->form }}
        </div>

        <div class="pure-action-buttons">
            <button type="button" class="pure-btn pure-btn-primary">
                <x-heroicon-o-arrow-path />
                Refresh
            </button>

            <button type="button" class="pure-btn pure-btn-outline">
                <x-heroicon-o-arrow-down-tray />
                Export Excel
            </button>
        </div>
    </div>

    <!-- KPI Cards and Attention Widget -->
    <div class="pure-top-section">

        <div class="pure-kpi-grid">

            <div class="pure-kpi-card">
                <div class="pure-kpi-icon blue">
                    <x-heroicon-o-building-office-2 />
                </div>
                <div>
                    <p class="pure-kpi-title">Total Branches</p>
                    <p class="pure-kpi-value">1,024</p>
                </div>
            </div>

            <div class="pure-kpi-card">
                <div class="pure-kpi-icon green">
                    <x-heroicon-o-check-circle />
                </div>
                <div>
                    <p class="pure-kpi-title">On Target</p>
                    <div style="display: flex; align-items: baseline; gap: 8px;">
                        <p class="pure-kpi-value">687</p>
                        <span class="pure-kpi-badge green">67.1%</span>
                    </div>
                </div>
            </div>

            <div class="pure-kpi-card">
                <div class="pure-kpi-icon amber">
                    <x-heroicon-o-exclamation-circle />
                </div>
                <div>
                    <p class="pure-kpi-title">Near Target</p>
                    <div style="display: flex; align-items: baseline; gap: 8px;">
                        <p class="pure-kpi-value">126</p>
                        <span class="pure-kpi-badge amber">12.3%</span>
                    </div>
                </div>
            </div>

            <div class="pure-kpi-card" style="border-color: #fecaca;">
                <div class="pure-kpi-icon red">
                    <x-heroicon-o-exclamation-triangle />
                </div>
                <div>
                    <p class="pure-kpi-title">Attention Required</p>
                    <div style="display: flex; align-items: baseline; gap: 8px;">
                        <p class="pure-kpi-value">211</p>
                        <span class="pure-kpi-badge red">20.6%</span>
                    </div>
                </div>
            </div>

            <div class="pure-kpi-card">
                <div class="pure-kpi-icon indigo">
                    <x-heroicon-o-chart-bar />
                </div>
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

        <!-- Attention Branches -->
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

    <!-- =========================================
         KPI TABS NAVIGATION
         ========================================= -->
    <div class="kpi-tabs">
        <button @click="activeTab = 'major'" :class="{'active': activeTab === 'major'}" class="kpi-tab">
            <x-heroicon-o-presentation-chart-bar />
            Major KPIs
        </button>
        <button @click="activeTab = 'digital'" :class="{'active': activeTab === 'digital'}" class="kpi-tab">
            <x-heroicon-o-device-phone-mobile />
            Digital KPIs
        </button>
        <button @click="activeTab = 'activation'" :class="{'active': activeTab === 'activation'}" class="kpi-tab">
            <x-heroicon-o-bolt />
            Activation KPIs
        </button>
        <button @click="activeTab = 'other'" :class="{'active': activeTab === 'other'}" class="kpi-tab">
            <x-heroicon-o-squares-plus />
            Other KPIs
        </button>
    </div>

    @php
    // Mock Data Array for all branches
    $data = [
        ['id'=>'', 'district'=>'JIMMA DISTRICT', 'branch'=>'', 'type'=>'IFB', 'is_district'=>true,
         'major'=>['deposit'=>['a'=>'299M','t'=>'310M','v'=>'-11M','g'=>96,'tr'=>'↑'], 'accounts'=>['a'=>52,'t'=>52,'v'=>0,'g'=>100,'tr'=>'↑'], 'fcy'=>['a'=>'50K','t'=>'60K','v'=>'-10K','g'=>83,'tr'=>'↓'], 'loans'=>['a'=>'180M','t'=>'200M','v'=>'-20M','g'=>90,'tr'=>'↓']],
         'digital'=>['card_sub'=>['a'=>120,'t'=>150,'v'=>-30,'g'=>80,'tr'=>'↓'], 'pos_sub'=>['a'=>20,'t'=>30,'v'=>-10,'g'=>67,'tr'=>'↓'], 'super_sub'=>['a'=>45,'t'=>60,'v'=>-15,'g'=>75,'tr'=>'↓']],
         'activation'=>['acc_act'=>['a'=>39,'t'=>50,'v'=>-11,'g'=>78,'tr'=>'↓'], 'card_act'=>['a'=>100,'t'=>130,'v'=>-30,'g'=>77,'tr'=>'↓'], 'super_act'=>['a'=>40,'t'=>50,'v'=>-10,'g'=>80,'tr'=>'↓']],
         'other'=>['service_quality'=>['a'=>'88','t'=>'90','v'=>'-2','g'=>98,'tr'=>'↑'], 'atm_txn'=>['a'=>'3,200','t'=>'3,500','v'=>'-300','g'=>91,'tr'=>'↓'], 'pos_txn'=>['a'=>'1,800','t'=>'2,000','v'=>'-200','g'=>90,'tr'=>'→'], 'nps'=>['a'=>'45','t'=>'50','v'=>'-5','g'=>90,'tr'=>'↓']],
         'overall'=>['score'=>94.2,'trend'=>'down']],
         
        ['id'=>'1.', 'district'=>'Jimma Main', 'branch'=>'', 'type'=>'Conventional', 'is_district'=>false,
         'major'=>['deposit'=>['a'=>'125M','t'=>'120M','v'=>'+5M','g'=>104,'tr'=>'↑'], 'accounts'=>['a'=>24,'t'=>20,'v'=>'+4','g'=>120,'tr'=>'↑'], 'fcy'=>['a'=>'25K','t'=>'20K','v'=>'+5K','g'=>125,'tr'=>'↑'], 'loans'=>['a'=>'95M','t'=>'100M','v'=>'-5M','g'=>95,'tr'=>'↑']],
         'digital'=>['card_sub'=>['a'=>50,'t'=>60,'v'=>-10,'g'=>83,'tr'=>'↓'], 'pos_sub'=>['a'=>10,'t'=>12,'v'=>-2,'g'=>83,'tr'=>'↓'], 'super_sub'=>['a'=>20,'t'=>25,'v'=>-5,'g'=>80,'tr'=>'↓']],
         'activation'=>['acc_act'=>['a'=>18,'t'=>25,'v'=>-7,'g'=>72,'tr'=>'↓'], 'card_act'=>['a'=>45,'t'=>55,'v'=>-10,'g'=>82,'tr'=>'↓'], 'super_act'=>['a'=>18,'t'=>22,'v'=>-4,'g'=>82,'tr'=>'↓']],
         'other'=>['service_quality'=>['a'=>'92','t'=>'90','v'=>'+2','g'=>102,'tr'=>'↑'], 'atm_txn'=>['a'=>'1,500','t'=>'1,400','v'=>'+100','g'=>107,'tr'=>'↑'], 'pos_txn'=>['a'=>'800','t'=>'750','v'=>'+50','g'=>107,'tr'=>'↑'], 'nps'=>['a'=>'55','t'=>'50','v'=>'+5','g'=>110,'tr'=>'↑']],
         'overall'=>['score'=>97.8,'trend'=>'up']],
         
        ['id'=>'2.', 'district'=>'Agaro', 'branch'=>'', 'type'=>'Conventional', 'is_district'=>false,
         'major'=>['deposit'=>['a'=>'98M','t'=>'110M','v'=>'-12M','g'=>89,'tr'=>'↓'], 'accounts'=>['a'=>17,'t'=>20,'v'=>'-3','g'=>85,'tr'=>'↓'], 'fcy'=>['a'=>'15K','t'=>'25K','v'=>'-10K','g'=>60,'tr'=>'↓'], 'loans'=>['a'=>'52M','t'=>'60M','v'=>'-8M','g'=>87,'tr'=>'↓']],
         'digital'=>['card_sub'=>['a'=>40,'t'=>50,'v'=>-10,'g'=>80,'tr'=>'↓'], 'pos_sub'=>['a'=>5,'t'=>10,'v'=>-5,'g'=>50,'tr'=>'↓'], 'super_sub'=>['a'=>15,'t'=>20,'v'=>-5,'g'=>75,'tr'=>'↓']],
         'activation'=>['acc_act'=>['a'=>12,'t'=>15,'v'=>-3,'g'=>80,'tr'=>'↓'], 'card_act'=>['a'=>35,'t'=>45,'v'=>-10,'g'=>78,'tr'=>'↓'], 'super_act'=>['a'=>12,'t'=>18,'v'=>-6,'g'=>67,'tr'=>'↓']],
         'other'=>['service_quality'=>['a'=>'75','t'=>'90','v'=>'-15','g'=>83,'tr'=>'↓'], 'atm_txn'=>['a'=>'900','t'=>'1,200','v'=>'-300','g'=>75,'tr'=>'↓'], 'pos_txn'=>['a'=>'500','t'=>'800','v'=>'-300','g'=>63,'tr'=>'↓'], 'nps'=>['a'=>'30','t'=>'50','v'=>'-20','g'=>60,'tr'=>'↓']],
         'overall'=>['score'=>87.2,'trend'=>'down']],
         
        ['id'=>'3.', 'district'=>'Bedele', 'branch'=>'', 'type'=>'IFB', 'is_district'=>false,
         'major'=>['deposit'=>['a'=>'76M','t'=>'80M','v'=>'-4M','g'=>95,'tr'=>'→'], 'accounts'=>['a'=>11,'t'=>12,'v'=>'-1','g'=>92,'tr'=>'→'], 'fcy'=>['a'=>'10K','t'=>'15K','v'=>'-5K','g'=>67,'tr'=>'→'], 'loans'=>['a'=>'33M','t'=>'40M','v'=>'-7M','g'=>83,'tr'=>'↓']],
         'digital'=>['card_sub'=>['a'=>30,'t'=>40,'v'=>-10,'g'=>75,'tr'=>'↓'], 'pos_sub'=>['a'=>5,'t'=>8,'v'=>-3,'g'=>63,'tr'=>'→'], 'super_sub'=>['a'=>10,'t'=>15,'v'=>-5,'g'=>67,'tr'=>'↓']],
         'activation'=>['acc_act'=>['a'=>9,'t'=>10,'v'=>-1,'g'=>90,'tr'=>'→'], 'card_act'=>['a'=>20,'t'=>30,'v'=>-10,'g'=>67,'tr'=>'↓'], 'super_act'=>['a'=>10,'t'=>10,'v'=>0,'g'=>100,'tr'=>'→']],
         'other'=>['service_quality'=>['a'=>'85','t'=>'90','v'=>'-5','g'=>94,'tr'=>'→'], 'atm_txn'=>['a'=>'800','t'=>'900','v'=>'-100','g'=>89,'tr'=>'→'], 'pos_txn'=>['a'=>'500','t'=>'600','v'=>'-100','g'=>83,'tr'=>'→'], 'nps'=>['a'=>'40','t'=>'50','v'=>'-10','g'=>80,'tr'=>'→']],
         'overall'=>['score'=>91.4,'trend'=>'stable']],
         
        ['id'=>'4.', 'district'=>'DISTRICT TOTAL', 'branch'=>'', 'type'=>'IFB', 'is_district'=>true,
         'major'=>['deposit'=>['a'=>'299M','t'=>'310M','v'=>'-11M','g'=>96,'tr'=>'→'], 'accounts'=>['a'=>52,'t'=>52,'v'=>0,'g'=>100,'tr'=>'↑'], 'fcy'=>['a'=>'50K','t'=>'60K','v'=>'-10K','g'=>83,'tr'=>'↓'], 'loans'=>['a'=>'180M','t'=>'200M','v'=>'-20M','g'=>90,'tr'=>'↓']],
         'digital'=>['card_sub'=>['a'=>120,'t'=>150,'v'=>-30,'g'=>80,'tr'=>'↓'], 'pos_sub'=>['a'=>20,'t'=>30,'v'=>-10,'g'=>67,'tr'=>'↓'], 'super_sub'=>['a'=>45,'t'=>60,'v'=>-15,'g'=>75,'tr'=>'↓']],
         'activation'=>['acc_act'=>['a'=>39,'t'=>50,'v'=>-11,'g'=>78,'tr'=>'↓'], 'card_act'=>['a'=>100,'t'=>130,'v'=>-30,'g'=>77,'tr'=>'↓'], 'super_act'=>['a'=>40,'t'=>50,'v'=>-10,'g'=>80,'tr'=>'↓']],
         'other'=>['service_quality'=>['a'=>'88','t'=>'90','v'=>'-2','g'=>98,'tr'=>'↑'], 'atm_txn'=>['a'=>'3,200','t'=>'3,500','v'=>'-300','g'=>91,'tr'=>'↓'], 'pos_txn'=>['a'=>'1,800','t'=>'2,000','v'=>'-200','g'=>90,'tr'=>'→'], 'nps'=>['a'=>'45','t'=>'50','v'=>'-5','g'=>90,'tr'=>'↓']],
         'overall'=>['score'=>94.2,'trend'=>'down']],
    ];
    @endphp

    <!-- =========================================
         MAIN DATA TABLE (Major KPIs)
         ========================================= -->
    <div class="pure-table-wrapper" x-show="activeTab === 'major'">
        <div class="pure-table-scroll">
            <table class="pure-table">
                <thead>
                    <tr>
                        <th class="pure-th-main pure-sticky-col" style="width: 40px;">#</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 40px; width: 80px;">District</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 120px; width: 140px;">Banking Type</th>
                        
                        <th colspan="5" class="pure-th-main">DEPOSIT</th>
                        <th colspan="5" class="pure-th-main">NEW ACCOUNTS</th>
                        <th colspan="5" class="pure-th-main">FCY</th>
                        <th colspan="5" class="pure-th-main">LOANS</th>
                        <th colspan="2" class="pure-th-main">Overall</th>
                    </tr>
                    <tr>
                        <th class="pure-th-sub pure-sticky-col" style="left: 0; width: 40px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 40px; width: 80px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 120px; width: 140px;"></th>
                        @for ($i = 0; $i < 4; $i++)
                            <th class="pure-th-sub">Actual</th><th class="pure-th-sub">Target</th><th class="pure-th-sub">Variance</th><th class="pure-th-sub">Achievement</th><th class="pure-th-sub">Trend</th>
                        @endfor
                        <th class="pure-th-sub">Score</th>
                        <th class="pure-th-sub">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr class="{{ $row['is_district'] ? 'pure-row-district' : 'pure-row-branch' }}">
                            <td class="pure-sticky-col" style="width: 40px;">
                                @if($row['is_district'] && $row['id'] == '')
                                    <x-heroicon-s-chevron-down style="width: 12px; height: 12px; color: #9ca3af;" />
                                @else
                                    {{ $row['id'] }}
                                @endif
                            </td>
                            <td class="pure-sticky-col {{ $row['is_district'] ? 'pure-font-bold' : '' }}" style="left: 40px;">{{ $row['district'] }}</td>
                            <td class="pure-sticky-col" style="left: 120px; text-align: left;">{{ $row['type'] }}</td>

                            <!-- Major KPIs -->
                            @foreach(['deposit', 'accounts', 'fcy', 'loans'] as $kpi)
                                @php $k = $row['major'][$kpi]; @endphp
                                <td>{{ $k['a'] }}</td>
                                <td>{{ $k['t'] }}</td>
                                <td class="{{ strpos((string)$k['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$k['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $k['v'] }}</td>
                                <td>{{ $k['g'] }}%</td>
                                <td class="{{ $k['tr'] === '↓' ? 'pure-text-red' : ($k['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $k['tr'] }}</td>
                            @endforeach

                            <!-- Overall -->
                            <td class="{{ $row['overall']['score'] < 90 ? 'pure-text-red' : ($row['overall']['score'] < 95 ? 'pure-text-amber' : 'pure-text-green') }}">{{ $row['overall']['score'] }}%</td>
                            <td class="{{ $row['overall']['trend'] === 'down' ? 'pure-text-red' : ($row['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $row['overall']['trend'] === 'up' ? '▲' : ($row['overall']['trend'] === 'down' ? '▼' : '→') }}</td>
                        </tr>
                    @endforeach

                    <!-- Grand Total -->
                    <tfoot class="pure-row-grand-total">
                        <tr>
                            <td class="pure-sticky-col" style="width: 40px;"></td>
                            <td class="pure-sticky-col" style="left: 40px;">GRAND TOTAL</td>
                            <td class="pure-sticky-col" style="left: 120px; text-align: left;">-</td>
                            @php $gt = $data[0]['major']; @endphp
                            @foreach(['deposit', 'accounts', 'fcy', 'loans'] as $kpi)
                                @php $k = $gt[$kpi]; @endphp
                                <td>{{ $k['a'] }}</td><td>{{ $k['t'] }}</td><td class="pure-text-red">{{ $k['v'] }}</td><td>{{ $k['g'] }}%</td><td>↑</td>
                            @endforeach
                            <td>93.7%</td><td class="pure-text-red">▼</td>
                        </tr>
                    </tfoot>
                </tbody>
            </table>
        </div>
    </div>

    <!-- =========================================
         MAIN DATA TABLE (Digital KPIs)
         ========================================= -->
    <div class="pure-table-wrapper" x-show="activeTab === 'digital'">
        <div class="pure-table-scroll">
            <table class="pure-table">
                <thead>
                    <tr>
                        <th class="pure-th-main pure-sticky-col" style="width: 40px;">#</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 40px; width: 80px;">District</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 120px; width: 140px;">Banking Type</th>
                        
                        <th colspan="5" class="pure-th-main">CARD SUBSCRIPTION</th>
                        <th colspan="5" class="pure-th-main">POS SUBSCRIPTION</th>
                        <th colspan="5" class="pure-th-main">SUPER APP SUBSCRIPTION</th>
                        <th colspan="2" class="pure-th-main">Overall</th>
                    </tr>
                    <tr>
                        <th class="pure-th-sub pure-sticky-col" style="left: 0; width: 40px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 40px; width: 80px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 120px; width: 140px;"></th>
                        @for ($i = 0; $i < 3; $i++)
                            <th class="pure-th-sub">Actual</th><th class="pure-th-sub">Target</th><th class="pure-th-sub">Variance</th><th class="pure-th-sub">Achievement</th><th class="pure-th-sub">Trend</th>
                        @endfor
                        <th class="pure-th-sub">Score</th>
                        <th class="pure-th-sub">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr class="{{ $row['is_district'] ? 'pure-row-district' : 'pure-row-branch' }}">
                            <td class="pure-sticky-col" style="width: 40px;">
                                @if($row['is_district'] && $row['id'] == '')
                                    <x-heroicon-s-chevron-down style="width: 12px; height: 12px; color: #9ca3af;" />
                                @else
                                    {{ $row['id'] }}
                                @endif
                            </td>
                            <td class="pure-sticky-col {{ $row['is_district'] ? 'pure-font-bold' : '' }}" style="left: 40px;">{{ $row['district'] }}</td>
                            <td class="pure-sticky-col" style="left: 120px; text-align: left;">{{ $row['type'] }}</td>

                            <!-- Digital KPIs -->
                            @foreach(['card_sub', 'pos_sub', 'super_sub'] as $kpi)
                                @php $k = $row['digital'][$kpi]; @endphp
                                <td>{{ $k['a'] }}</td>
                                <td>{{ $k['t'] }}</td>
                                <td class="{{ strpos((string)$k['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$k['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $k['v'] }}</td>
                                <td>{{ $k['g'] }}%</td>
                                <td class="{{ $k['tr'] === '↓' ? 'pure-text-red' : ($k['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $k['tr'] }}</td>
                            @endforeach

                            <!-- Overall -->
                            <td class="{{ $row['overall']['score'] < 90 ? 'pure-text-red' : ($row['overall']['score'] < 95 ? 'pure-text-amber' : 'pure-text-green') }}">{{ $row['overall']['score'] }}%</td>
                            <td class="{{ $row['overall']['trend'] === 'down' ? 'pure-text-red' : ($row['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $row['overall']['trend'] === 'up' ? '▲' : ($row['overall']['trend'] === 'down' ? '▼' : '→') }}</td>
                        </tr>
                    @endforeach

                    <!-- Grand Total -->
                    <tfoot class="pure-row-grand-total">
                        <tr>
                            <td class="pure-sticky-col" style="width: 40px;"></td>
                            <td class="pure-sticky-col" style="left: 40px;">GRAND TOTAL</td>
                            <td class="pure-sticky-col" style="left: 120px; text-align: left;">-</td>
                            @php $gt = $data[0]['digital']; @endphp
                            @foreach(['card_sub', 'pos_sub', 'super_sub'] as $kpi)
                                @php $k = $gt[$kpi]; @endphp
                                <td>{{ $k['a'] }}</td><td>{{ $k['t'] }}</td><td class="pure-text-red">{{ $k['v'] }}</td><td>{{ $k['g'] }}%</td><td>↓</td>
                            @endforeach
                            <td>93.7%</td><td class="pure-text-red">▼</td>
                        </tr>
                    </tfoot>
                </tbody>
            </table>
        </div>
    </div>

    <!-- =========================================
         MAIN DATA TABLE (Activation KPIs)
         ========================================= -->
    <div class="pure-table-wrapper" x-show="activeTab === 'activation'">
        <div class="pure-table-scroll">
            <table class="pure-table">
                <thead>
                    <tr>
                        <th class="pure-th-main pure-sticky-col" style="width: 40px;">#</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 40px; width: 80px;">District</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 120px; width: 140px;">Banking Type</th>
                        
                        <th colspan="5" class="pure-th-main">ACCOUNT ACTIVATION</th>
                        <th colspan="5" class="pure-th-main">CARD ACTIVATION</th>
                        <th colspan="5" class="pure-th-main">SUPER APP ACTIVATION</th>
                        <th colspan="2" class="pure-th-main">Overall</th>
                    </tr>
                    <tr>
                        <th class="pure-th-sub pure-sticky-col" style="left: 0; width: 40px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 40px; width: 80px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 120px; width: 140px;"></th>
                        @for ($i = 0; $i < 3; $i++)
                            <th class="pure-th-sub">Actual</th><th class="pure-th-sub">Target</th><th class="pure-th-sub">Variance</th><th class="pure-th-sub">Achievement</th><th class="pure-th-sub">Trend</th>
                        @endfor
                        <th class="pure-th-sub">Score</th>
                        <th class="pure-th-sub">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr class="{{ $row['is_district'] ? 'pure-row-district' : 'pure-row-branch' }}">
                            <td class="pure-sticky-col" style="width: 40px;">
                                @if($row['is_district'] && $row['id'] == '')
                                    <x-heroicon-s-chevron-down style="width: 12px; height: 12px; color: #9ca3af;" />
                                @else
                                    {{ $row['id'] }}
                                @endif
                            </td>
                            <td class="pure-sticky-col {{ $row['is_district'] ? 'pure-font-bold' : '' }}" style="left: 40px;">{{ $row['district'] }}</td>
                            <td class="pure-sticky-col" style="left: 120px; text-align: left;">{{ $row['type'] }}</td>

                            <!-- Activation KPIs -->
                            @foreach(['acc_act', 'card_act', 'super_act'] as $kpi)
                                @php $k = $row['activation'][$kpi]; @endphp
                                <td>{{ $k['a'] }}</td>
                                <td>{{ $k['t'] }}</td>
                                <td class="{{ strpos((string)$k['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$k['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $k['v'] }}</td>
                                <td>{{ $k['g'] }}%</td>
                                <td class="{{ $k['tr'] === '↓' ? 'pure-text-red' : ($k['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $k['tr'] }}</td>
                            @endforeach

                            <!-- Overall -->
                            <td class="{{ $row['overall']['score'] < 90 ? 'pure-text-red' : ($row['overall']['score'] < 95 ? 'pure-text-amber' : 'pure-text-green') }}">{{ $row['overall']['score'] }}%</td>
                            <td class="{{ $row['overall']['trend'] === 'down' ? 'pure-text-red' : ($row['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $row['overall']['trend'] === 'up' ? '▲' : ($row['overall']['trend'] === 'down' ? '▼' : '→') }}</td>
                        </tr>
                    @endforeach

                    <!-- Grand Total -->
                    <tfoot class="pure-row-grand-total">
                        <tr>
                            <td class="pure-sticky-col" style="width: 40px;"></td>
                            <td class="pure-sticky-col" style="left: 40px;">GRAND TOTAL</td>
                            <td class="pure-sticky-col" style="left: 120px; text-align: left;">-</td>
                            @php $gt = $data[0]['activation']; @endphp
                            @foreach(['acc_act', 'card_act', 'super_act'] as $kpi)
                                @php $k = $gt[$kpi]; @endphp
                                <td>{{ $k['a'] }}</td><td>{{ $k['t'] }}</td><td class="pure-text-red">{{ $k['v'] }}</td><td>{{ $k['g'] }}%</td><td>↓</td>
                            @endforeach
                            <td>93.7%</td><td class="pure-text-red">▼</td>
                        </tr>
                    </tfoot>
                </tbody>
            </table>
        </div>
    </div>

    <!-- =========================================
         MAIN DATA TABLE (Other KPIs)
         ========================================= -->
    <div class="pure-table-wrapper" x-show="activeTab === 'other'">
        <div class="pure-table-scroll">
            <table class="pure-table">
                <thead>
                    <tr>
                        <th class="pure-th-main pure-sticky-col" style="width: 40px;">#</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 40px; width: 80px;">District</th>
                        <th class="pure-th-main pure-sticky-col" style="left: 120px; width: 140px;">Banking Type</th>
                        
                        <th colspan="5" class="pure-th-main">SERVICE QUALITY</th>
                        <th colspan="5" class="pure-th-main">ATM TRANSACTION</th>
                        <th colspan="5" class="pure-th-main">POS TRANSACTION</th>
                        <th colspan="5" class="pure-th-main">NPS</th>
                        <th colspan="2" class="pure-th-main">Overall</th>
                    </tr>
                    <tr>
                        <th class="pure-th-sub pure-sticky-col" style="left: 0; width: 40px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 40px; width: 80px;"></th>
                        <th class="pure-th-sub pure-sticky-col" style="left: 120px; width: 140px;"></th>
                        @for ($i = 0; $i < 4; $i++)
                            <th class="pure-th-sub">Actual</th><th class="pure-th-sub">Target</th><th class="pure-th-sub">Variance</th><th class="pure-th-sub">Achievement</th><th class="pure-th-sub">Trend</th>
                        @endfor
                        <th class="pure-th-sub">Score</th>
                        <th class="pure-th-sub">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr class="{{ $row['is_district'] ? 'pure-row-district' : 'pure-row-branch' }}">
                            <td class="pure-sticky-col" style="width: 40px;">
                                @if($row['is_district'] && $row['id'] == '')
                                    <x-heroicon-s-chevron-down style="width: 12px; height: 12px; color: #9ca3af;" />
                                @else
                                    {{ $row['id'] }}
                                @endif
                            </td>
                            <td class="pure-sticky-col {{ $row['is_district'] ? 'pure-font-bold' : '' }}" style="left: 40px;">{{ $row['district'] }}</td>
                            <td class="pure-sticky-col" style="left: 120px; text-align: left;">{{ $row['type'] }}</td>

                            <!-- Other KPIs -->
                            @foreach(['service_quality', 'atm_txn', 'pos_txn', 'nps'] as $kpi)
                                @php $k = $row['other'][$kpi]; @endphp
                                <td>{{ $k['a'] }}</td>
                                <td>{{ $k['t'] }}</td>
                                <td class="{{ strpos((string)$k['v'], '-') !== false ? 'pure-text-red' : (strpos((string)$k['v'], '+') !== false ? 'pure-text-green' : '') }}">{{ $k['v'] }}</td>
                                <td>{{ $k['g'] }}%</td>
                                <td class="{{ $k['tr'] === '↓' ? 'pure-text-red' : ($k['tr'] === '↑' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $k['tr'] }}</td>
                            @endforeach

                            <!-- Overall -->
                            <td class="{{ $row['overall']['score'] < 90 ? 'pure-text-red' : ($row['overall']['score'] < 95 ? 'pure-text-amber' : 'pure-text-green') }}">{{ $row['overall']['score'] }}%</td>
                            <td class="{{ $row['overall']['trend'] === 'down' ? 'pure-text-red' : ($row['overall']['trend'] === 'up' ? 'pure-text-green' : 'pure-text-amber') }}">{{ $row['overall']['trend'] === 'up' ? '▲' : ($row['overall']['trend'] === 'down' ? '▼' : '→') }}</td>
                        </tr>
                    @endforeach

                    <!-- Grand Total -->
                    <tfoot class="pure-row-grand-total">
                        <tr>
                            <td class="pure-sticky-col" style="width: 40px;"></td>
                            <td class="pure-sticky-col" style="left: 40px;">GRAND TOTAL</td>
                            <td class="pure-sticky-col" style="left: 120px; text-align: left;">-</td>
                            @php $gt = $data[0]['other']; @endphp
                            @foreach(['service_quality', 'atm_txn', 'pos_txn', 'nps'] as $kpi)
                                @php $k = $gt[$kpi]; @endphp
                                <td>{{ $k['a'] }}</td><td>{{ $k['t'] }}</td><td class="pure-text-red">{{ $k['v'] }}</td><td>{{ $k['g'] }}%</td><td>↓</td>
                            @endforeach
                            <td>93.7%</td><td class="pure-text-red">▼</td>
                        </tr>
                    </tfoot>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table Legend (Shared across all tabs) -->
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

        <div style="display: flex; gap: 12px; font-family: monospace; font-size: 11px; flex-wrap: wrap;">
            <span>Trend = Previous Day %</span>
            <span>St = Status</span>
        </div>
    </div>

</div> <!-- End Alpine Data Scope -->


</x-filament-panels::page>