<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pearl-user-id" content="{{ auth()->id() }}">
    <meta name="pearl-user-admin" content="{{ auth()->check() && auth()->user()->is_admin ? '1' : '0' }}">
    <title>Admin Operations - The Pearl Manila Hotel</title>
    <link rel="icon" type="image/png" href="{{ asset('image/PearlMNL_LOGO.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/test.css') }}">
    @vite(['resources/js/app.js'])
    <style>
        body.admin-operations-page {
            transform: none !important;
            filter: none !important;
            perspective: none !important;
            contain: none !important;
        }

        html.is-transitioning body.admin-operations-page {
            opacity: 1 !important;
            transform: none !important;
        }

        body.admin-modal-open {
            overflow: hidden;
        }

        body > .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
        }

        body > .modal-overlay .modal-content {
            max-height: min(90vh, calc(100dvh - 24px));
        }

        .admin-operations-page .admin-card {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(248, 251, 255, 0.94) 100%);
            border: 1px solid #d6e0ef;
            box-shadow:
                0 14px 32px rgba(15, 23, 42, 0.08),
                0 2px 8px rgba(37, 99, 235, 0.06);
        }

        .admin-operations-page .admin-panel {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(246, 250, 255, 0.92) 100%);
            border: 1px solid #dbe5f2;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.85),
                0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .admin-operations-page .admin-month-nav {
            background: linear-gradient(180deg, #f8fbff 0%, #eef4fc 100%);
            border: 1px solid #d4e0ef;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .admin-operations-page .checkout-queue-widget {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(243, 248, 255, 0.92) 100%);
            border: 1px solid #cfdced;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.85),
                0 8px 20px rgba(30, 64, 175, 0.08);
        }

        .admin-operations-page .calendar-day {
            background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
            border: 1px solid #d5e0ed;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.95),
                0 4px 10px rgba(15, 23, 42, 0.04);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .admin-operations-page .calendar-day:hover {
            border-color: #93c5fd;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.98),
                0 10px 22px rgba(37, 99, 235, 0.14);
            transform: translateY(-1px);
        }

        .admin-operations-page .calendar-day.has-confirmed {
            background: linear-gradient(180deg, #ecfdf3 0%, #e2f8eb 100%);
            border-color: #9dd9b0;
        }

        .admin-operations-page .calendar-day.has-pending {
            background: linear-gradient(180deg, #fff8e8 0%, #fff1cf 100%);
            border-color: #f3cc86;
        }

        .admin-operations-page .calendar-status-dots {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            position: relative;
            z-index: 2;
        }

        .admin-operations-page .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.9);
        }

        .admin-operations-page .status-dot-pending,
        .admin-operations-page .status-dot-pending_verification {
            background: #f59e0b;
        }

        .admin-operations-page .status-dot-confirmed {
            background: #22c55e;
        }

        .admin-operations-page .status-dot-checked_in,
        .admin-operations-page .status-dot-checkout_scheduled {
            background: #3b82f6;
        }

        .admin-operations-page .status-dot-cancelled {
            background: #ef4444;
        }

        .admin-operations-page .status-dot-available {
            background: #14b8a6;
        }

        .admin-operations-page .calendar-empty-inline {
            margin-top: 6px;
            font-size: 0.75rem;
            color: var(--ops-text-dim);
        }

        .admin-operations-page .calendar-day-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 10px;
        }

        .admin-operations-page .calendar-day-footer .btn {
            padding: 6px 10px;
            font-size: 0.7rem;
            border-radius: 8px;
        }

        .admin-operations-page .calendar-unavailable-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 600;
        }

        html.dark-theme .admin-operations-page .calendar-unavailable-pill {
            background: #1e293b;
            color: #94a3b8;
        }

        .admin-operations-page .day-details-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .admin-operations-page .day-details-badge {
            border: 1px solid var(--ops-border);
            border-radius: 10px;
            padding: 8px 10px;
            background: var(--ops-surface-muted);
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--ops-text);
        }

        .admin-operations-page .day-details-badge .legend-dot {
            width: 8px;
            height: 8px;
        }

        .admin-operations-page .day-details-section h4 {
            margin: 0 0 8px;
            font-size: 0.9rem;
            color: var(--ops-text);
        }

        .admin-operations-page .day-details-list {
            display: grid;
            gap: 8px;
        }

        .admin-operations-page .day-details-item {
            border: 1px solid var(--ops-border);
            border-radius: 10px;
            padding: 10px 12px;
            background: var(--ops-surface-muted);
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 0.82rem;
            color: var(--ops-text);
        }

        .admin-operations-page .day-details-item span {
            color: var(--ops-text-dim);
        }

        .checkout-queue-widget {
            margin-bottom: 16px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            background: #f8fafc;
            padding: 12px 14px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08);
        }

        .checkout-queue-widget-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .checkout-queue-widget h4 {
            margin: 0;
            font-size: 1rem;
            color: #0f172a;
        }

        .checkout-queue-widget p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 0.86rem;
        }

        .checkout-queue-widget-actions {
            display: inline-flex;
            gap: 8px;
        }

        .checkout-queue-widget.is-collapsed .checkout-queue-widget-body {
            display: none;
        }

        .checkout-queue-inline-list {
            margin-top: 10px;
            display: grid;
            gap: 8px;
        }

        .checkout-queue-inline-item {
            border: 1px dashed #bfdbfe;
            border-radius: 10px;
            padding: 8px 10px;
            color: #1e3a8a;
            background: #eff6ff;
            font-size: 0.84rem;
        }

        .checkout-release-row {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            padding: 12px;
            display: grid;
            gap: 10px;
        }

        .checkout-release-row h5 {
            margin: 0;
            color: #0f172a;
            font-size: 0.95rem;
        }

        .checkout-release-meta {
            color: #64748b;
            font-size: 0.84rem;
        }

        .checkout-release-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .checkout-release-actions input[type="datetime-local"] {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px 8px;
            font-family: inherit;
            font-size: 0.84rem;
            min-width: 210px;
            background: #fff;
        }

        html.dark-theme .checkout-queue-widget,
        html.dark-theme .checkout-release-row,
        html.dark-theme .checkout-release-actions input[type="datetime-local"] {
            background: #0f172a;
            border-color: #334155;
        }

        html.dark-theme .checkout-queue-widget h4,
        html.dark-theme .checkout-release-row h5 {
            color: #e2e8f0;
        }

        html.dark-theme .checkout-queue-widget p,
        html.dark-theme .checkout-release-meta {
            color: #94a3b8;
        }

        html.dark-theme .checkout-queue-inline-item {
            background: #0b2142;
            border-color: #1d4ed8;
            color: #bfdbfe;
        }

        html.dark-theme .admin-operations-page .admin-card {
            background:
                linear-gradient(180deg, rgba(15, 23, 42, 0.96) 0%, rgba(11, 25, 47, 0.94) 100%);
            border-color: #2f3f59;
            box-shadow:
                0 18px 36px rgba(2, 6, 23, 0.6),
                0 0 0 1px rgba(59, 130, 246, 0.12);
        }

        html.dark-theme .admin-operations-page .admin-panel {
            background:
                linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(12, 28, 52, 0.94) 100%);
            border-color: #334155;
            box-shadow:
                inset 0 1px 0 rgba(148, 163, 184, 0.08),
                0 12px 24px rgba(2, 6, 23, 0.45);
        }

        html.dark-theme .admin-operations-page .admin-month-nav {
            background: linear-gradient(180deg, #10203a 0%, #0f1b31 100%);
            border-color: #334155;
            box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.08);
        }

        html.dark-theme .admin-operations-page .checkout-queue-widget {
            background:
                linear-gradient(180deg, rgba(15, 23, 42, 0.96) 0%, rgba(12, 24, 45, 0.94) 100%);
            border-color: #334155;
            box-shadow:
                inset 0 1px 0 rgba(148, 163, 184, 0.08),
                0 12px 28px rgba(2, 6, 23, 0.45);
        }

        html.dark-theme .admin-operations-page .calendar-day {
            background: linear-gradient(180deg, #0f1c33 0%, #0c172b 100%);
            border-color: #30435f;
            box-shadow:
                inset 0 1px 0 rgba(148, 163, 184, 0.08),
                0 6px 14px rgba(2, 6, 23, 0.35);
        }

        html.dark-theme .admin-operations-page .calendar-day:hover {
            border-color: #60a5fa;
            box-shadow:
                inset 0 1px 0 rgba(148, 163, 184, 0.12),
                0 12px 26px rgba(30, 64, 175, 0.3);
        }

        html.dark-theme .admin-operations-page .calendar-day.has-confirmed {
            background: linear-gradient(180deg, #123c2c 0%, #102f23 100%);
            border-color: #1f7a4a;
        }

        html.dark-theme .admin-operations-page .calendar-day.has-pending {
            background: linear-gradient(180deg, #4a3414 0%, #39270f 100%);
            border-color: #a16207;
        }

        html.dark-theme .admin-operations-page .status-dot {
            box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.95);
        }

        .room-current-set {
            margin-top: 4px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #2563eb;
        }

        .room-limit-note {
            margin-top: 2px;
            font-size: 0.76rem;
            font-weight: 600;
            color: #0f766e;
        }

        .room-distribution-limit {
            margin-top: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #0f172a;
        }

        .room-distribution-helper {
            margin-top: 8px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .room-distribution-hint {
            font-size: 0.78rem;
            color: #64748b;
        }

        body > .modal-overlay .btn-outline {
            border: 1px solid #cbd5e1;
            color: #0f172a;
            background: #ffffff;
        }

        body > .modal-overlay .btn-outline:hover {
            border-color: #38bdf8;
            color: #0c4a6e;
            background: #eff6ff;
        }

        .room-settings-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 10px;
            font-family: inherit;
            font-size: 0.9rem;
            background: #fff;
            color: #0f172a;
        }

        .room-settings-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.86rem;
            font-weight: 600;
            color: #334155;
        }

        .room-settings-toggle input[type="checkbox"] {
            accent-color: #0ea5e9;
        }

        html.dark-theme .room-current-set {
            color: #93c5fd;
        }

        html.dark-theme .room-limit-note {
            color: #5eead4;
        }

        html.dark-theme .room-distribution-limit {
            color: #cbd5e1;
        }

        html.dark-theme .room-distribution-hint {
            color: #94a3b8;
        }

        html.dark-theme .room-settings-input {
            background: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }

        html.dark-theme .room-settings-toggle {
            color: #cbd5e1;
        }

        html.dark-theme body > .modal-overlay .btn-outline {
            border-color: #475569;
            color: #e2e8f0;
            background: #0f172a;
        }

        html.dark-theme body > .modal-overlay .btn-outline:hover {
            border-color: #60a5fa;
            color: #dbeafe;
            background: #1e293b;
        }

        html.dark-theme body > #inventoryModal .modal-header h3,
        html.dark-theme body > #inventoryModal .modal-header p,
        html.dark-theme body > #inventoryModal .form-label,
        html.dark-theme body > #inventoryModal .room-name,
        html.dark-theme body > #inventoryModal .room-capacity,
        html.dark-theme body > #inventoryModal .room-current-set,
        html.dark-theme body > #inventoryModal .room-limit-note,
        html.dark-theme body > #inventoryModal .room-distribution-limit {
            color: #e2e8f0 !important;
        }

        html.dark-theme body > #inventoryModal .room-current-set {
            color: #93c5fd !important;
        }

        html.dark-theme body > #inventoryModal .room-limit-note {
            color: #5eead4 !important;
        }

        html.dark-theme body > #inventoryModal .room-distribution-limit {
            color: #cbd5e1 !important;
        }

        html.dark-theme body > #inventoryModal .form-select,
        html.dark-theme body > #inventoryModal .form-input,
        html.dark-theme body > #inventoryModal .room-input,
        html.dark-theme body > #inventoryModal .room-btn {
            background: #0b1220 !important;
            border-color: #334155 !important;
            color: #e5e7eb !important;
        }

        html.dark-theme body > #inventoryModal .room-card {
            background: #0b1220 !important;
            border-color: #334155 !important;
        }

        html.dark-theme body > #inventoryModal .room-btn:hover {
            border-color: #60a5fa !important;
            color: #bfdbfe !important;
            background: #1e293b !important;
        }

        .calendar-day.is-past-date {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .calendar-day.is-past-date .calendar-date,
        .calendar-day.is-past-date .calendar-events {
            pointer-events: none;
        }

        /* Operations V2 visual polish (scoped, behavior-safe) */
        body.admin-operations-page {
            --ops-bg: radial-gradient(1200px 560px at 10% -10%, #f4f7fd 0%, #edf1f8 40%, #e7ecf5 100%);
            --ops-surface: #fdfdff;
            --ops-surface-soft: #f6f8fd;
            --ops-surface-muted: #eef2f8;
            --ops-border: #d8dfeb;
            --ops-border-strong: #c3ccdd;
            --ops-text: #121826;
            --ops-text-dim: #5f6d85;
            --ops-brand: #4f46e5;
            --ops-brand-2: #06b6d4;
            --ops-shadow: 0 20px 44px rgba(15, 23, 42, 0.10);
        }

        html.dark-theme body.admin-operations-page {
            --ops-bg: radial-gradient(950px 480px at 8% -8%, #1b2133 0%, #0f1320 40%, #090c14 100%);
            --ops-surface: #0f131f;
            --ops-surface-soft: #121727;
            --ops-surface-muted: #171d2d;
            --ops-border: #2c3449;
            --ops-border-strong: #3b4560;
            --ops-text: #e7ecf7;
            --ops-text-dim: #9aa8c1;
            --ops-brand: #6366f1;
            --ops-brand-2: #22d3ee;
            --ops-shadow: 0 26px 56px rgba(0, 0, 0, 0.48);
        }

        body.admin-operations-page .admin-navbar {
            background: linear-gradient(180deg, #0c0f18 0%, #090c14 100%);
            border-bottom: 1px solid #1f2739;
            box-shadow: 0 10px 26px rgba(2, 6, 23, 0.45);
        }

        body.admin-operations-page .admin-navbar .admin-nav-menu a,
        body.admin-operations-page .admin-logo {
            color: #d8e0f2;
        }

        body.admin-operations-page .admin-navbar .admin-nav-menu a:hover {
            color: #ffffff;
        }

        body.admin-operations-page .admin-navbar .theme-toggle {
            background: rgba(99, 102, 241, 0.18);
            border-color: rgba(99, 102, 241, 0.5);
            color: #dbe2ff;
        }

        body.admin-operations-page .auth-page.admin-page {
            background: var(--ops-bg) !important;
            min-height: calc(100vh - 74px);
            padding: 24px 0 34px;
        }

        body.admin-operations-page .admin-card {
            border-radius: 16px;
            background: linear-gradient(180deg, var(--ops-surface) 0%, var(--ops-surface-soft) 100%);
            border: 1px solid var(--ops-border);
            box-shadow: var(--ops-shadow);
            padding: 18px;
        }

        body.admin-operations-page .admin-panel {
            border-radius: 14px;
            background: linear-gradient(180deg, var(--ops-surface) 0%, var(--ops-surface-soft) 100%);
            border: 1px solid var(--ops-border);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
            padding: 14px;
            margin-bottom: 14px;
        }

        body.admin-operations-page .admin-hero {
            margin-bottom: 14px;
            align-items: flex-start;
            gap: 12px;
        }

        body.admin-operations-page .admin-hero h2 {
            color: var(--ops-text);
            font-size: 2.05rem;
            line-height: 1.12;
            margin-bottom: 4px;
        }

        body.admin-operations-page .admin-hero p {
            color: var(--ops-text-dim);
            margin: 0;
            font-size: 0.9rem;
        }

        body.admin-operations-page .admin-hero-stats {
            gap: 8px;
        }

        body.admin-operations-page .stat-pill {
            border-radius: 999px;
            border: 1px solid var(--ops-border);
            background: linear-gradient(180deg, var(--ops-surface-muted) 0%, var(--ops-surface-soft) 100%);
            color: var(--ops-text);
            padding: 6px 12px;
            font-size: 0.74rem;
            font-weight: 700;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
        }

        body.admin-operations-page .stat-pill-clickable:hover {
            border-color: var(--ops-brand);
            color: var(--ops-brand);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
            transform: translateY(-1px);
        }

        body.admin-operations-page .checkout-queue-widget {
            border-radius: 12px;
            border: 1px solid var(--ops-border);
            background: linear-gradient(180deg, var(--ops-surface) 0%, var(--ops-surface-soft) 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        body.admin-operations-page .checkout-queue-widget h4,
        body.admin-operations-page .calendar-header h3,
        body.admin-operations-page .room-settings-panel h3 {
            color: var(--ops-text);
            font-size: 0.95rem;
            letter-spacing: 0.01em;
            margin: 0;
        }

        body.admin-operations-page .checkout-queue-widget p,
        body.admin-operations-page .calendar-header p {
            color: var(--ops-text-dim);
            font-size: 0.78rem;
            margin: 4px 0 0;
        }

        body.admin-operations-page .checkout-queue-inline-item {
            border-radius: 8px;
            border: 1px solid var(--ops-border-strong);
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.12) 0%, rgba(34, 211, 238, 0.08) 100%);
            color: var(--ops-text);
            font-size: 0.76rem;
            padding: 6px 8px;
        }

        body.admin-operations-page .calendar-header {
            margin-bottom: 10px;
        }

        body.admin-operations-page .calendar-legend {
            gap: 6px;
        }

        body.admin-operations-page .legend-pill {
            border: 1px solid var(--ops-border);
            border-radius: 999px;
            font-size: 0.66rem;
            font-weight: 700;
            padding: 5px 8px;
        }

        body.admin-operations-page .legend-pill.pending {
            background: rgba(245, 158, 11, 0.14);
            border-color: rgba(245, 158, 11, 0.35);
            color: #d97706;
        }

        body.admin-operations-page .legend-pill.confirmed {
            background: rgba(34, 197, 94, 0.14);
            border-color: rgba(34, 197, 94, 0.35);
            color: #15803d;
        }

        html.dark-theme body.admin-operations-page .legend-pill.pending {
            color: #fbbf24;
        }

        html.dark-theme body.admin-operations-page .legend-pill.confirmed {
            color: #4ade80;
        }

        body.admin-operations-page .admin-month-nav {
            border-radius: 10px;
            border: 1px solid var(--ops-border);
            background: linear-gradient(180deg, var(--ops-surface-soft) 0%, var(--ops-surface-muted) 100%);
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        body.admin-operations-page .admin-month-nav-title {
            color: var(--ops-text);
            font-size: 1.3rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        body.admin-operations-page .admin-month-nav-subtitle {
            color: var(--ops-text-dim);
            font-size: 0.64rem;
            letter-spacing: 0.18em;
        }

        body.admin-operations-page .calendar-grid {
            gap: 8px;
            grid-template-columns: repeat(7, minmax(128px, 1fr));
        }

        body.admin-operations-page .calendar-day {
            border-radius: 10px;
            border: 1px solid var(--ops-border);
            background: linear-gradient(180deg, var(--ops-surface) 0%, var(--ops-surface-soft) 100%);
            min-height: 180px;
            padding: 10px;
            box-shadow: none;
        }

        body.admin-operations-page .calendar-day:hover {
            transform: none;
            border-color: var(--ops-brand);
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.22);
        }

        body.admin-operations-page .calendar-daynum {
            font-size: 1.05rem;
        }

        body.admin-operations-page .calendar-weekday {
            font-size: 0.64rem;
            letter-spacing: 0.12em;
        }

        body.admin-operations-page .calendar-month {
            font-size: 0.72rem;
        }

        body.admin-operations-page .calendar-event {
            border-radius: 8px;
            border: 1px solid var(--ops-border);
            background: var(--ops-surface-muted);
            padding: 7px;
            margin-bottom: 6px;
        }

        body.admin-operations-page .calendar-event-title {
            color: var(--ops-text);
            font-size: 0.78rem;
        }

        body.admin-operations-page .calendar-event-meta {
            color: var(--ops-text-dim);
            font-size: 0.71rem;
            line-height: 1.35;
        }

        body.admin-operations-page .history-filters {
            border: 1px solid var(--ops-border);
            background: linear-gradient(180deg, var(--ops-surface-soft) 0%, var(--ops-surface-muted) 100%);
            border-radius: 10px;
            padding: 10px;
            margin: 10px 0 8px;
        }

        body.admin-operations-page .filter-row {
            gap: 8px;
            align-items: end;
        }

        body.admin-operations-page .filter-group {
            min-width: 160px;
            gap: 4px;
        }

        body.admin-operations-page .filter-group label {
            color: var(--ops-text-dim);
            font-size: 0.65rem;
            letter-spacing: 0.08em;
        }

        body.admin-operations-page .filter-group label i {
            color: var(--ops-brand);
        }

        body.admin-operations-page .filter-input,
        body.admin-operations-page .filter-select {
            border-radius: 7px;
            border: 1px solid var(--ops-border-strong);
            background: var(--ops-surface);
            color: var(--ops-text);
            padding: 7px 10px;
            font-size: 0.82rem;
            min-height: 34px;
        }

        body.admin-operations-page .history-count {
            color: var(--ops-text-dim);
            font-size: 0.74rem;
            margin-bottom: 8px;
        }

        body.admin-operations-page .admin-table-wrapper {
            border: 1px solid var(--ops-border);
            border-radius: 10px;
            background: var(--ops-surface);
            overflow: auto;
            max-height: min(58vh, 560px);
        }

        body.admin-operations-page .admin-table {
            min-width: 1240px;
            font-size: 0.78rem;
            border-collapse: separate;
            border-spacing: 0;
        }

        body.admin-operations-page .admin-table th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: linear-gradient(180deg, var(--ops-surface-soft) 0%, var(--ops-surface-muted) 100%);
            color: var(--ops-text-dim);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.64rem;
            border-bottom: 1px solid var(--ops-border);
            padding: 9px 10px;
        }

        body.admin-operations-page .admin-table td {
            border-bottom: 1px solid var(--ops-border);
            color: var(--ops-text);
            padding: 8px 10px;
            vertical-align: middle;
        }

        body.admin-operations-page .booking-history-table td:first-child {
            font-weight: 600;
        }

        body.admin-operations-page .booking-history-table .calendar-event-actions {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            flex-wrap: nowrap;
            max-width: 140px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        body.admin-operations-page .history-status {
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        body.admin-operations-page .room-settings-panel .admin-table-wrapper {
            max-height: none;
        }

        body.admin-operations-page .room-settings-input {
            border: 1px solid var(--ops-border-strong);
            background: var(--ops-surface);
            color: var(--ops-text);
            border-radius: 7px;
            min-height: 34px;
            padding: 7px 10px;
            font-size: 0.82rem;
        }

        body.admin-operations-page .room-settings-toggle {
            color: var(--ops-text);
            font-size: 0.76rem;
            font-weight: 700;
            gap: 6px;
        }

        body.admin-operations-page .queue-item,
        body.admin-operations-page .checkout-release-row {
            border: 1px solid var(--ops-border);
            border-radius: 10px;
            background: var(--ops-surface-muted);
            padding: 9px;
        }

        body.admin-operations-page .queue-title,
        body.admin-operations-page .checkout-release-row h5 {
            color: var(--ops-text);
            font-size: 0.82rem;
        }

        body.admin-operations-page .queue-meta,
        body.admin-operations-page .checkout-release-meta {
            color: var(--ops-text-dim);
            font-size: 0.72rem;
        }

        body.admin-operations-page .btn {
            border-radius: 7px;
            min-height: 34px;
            padding: 6px 12px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        body.admin-operations-page .btn-primary {
            background: linear-gradient(90deg, var(--ops-brand) 0%, var(--ops-brand-2) 100%);
            color: #fff;
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.3);
        }

        body.admin-operations-page .btn-primary:hover {
            filter: brightness(1.05);
        }

        body.admin-operations-page .btn-outline {
            background: rgba(255, 255, 255, 0.32);
            border-color: var(--ops-border-strong);
            color: var(--ops-text);
        }

        body.admin-operations-page .btn-outline:hover {
            border-color: var(--ops-brand);
            color: var(--ops-brand);
            background: rgba(99, 102, 241, 0.1);
        }

        body.admin-operations-page .modal-content {
            border-radius: 14px;
            border: 1px solid var(--ops-border);
            background: linear-gradient(180deg, var(--ops-surface) 0%, var(--ops-surface-soft) 100%);
        }

        .id-preview-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 0 12px;
        }

        .id-preview-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .id-preview-action {
            border: 1px solid var(--ops-border-strong);
            background: var(--ops-surface);
            color: var(--ops-text);
            border-radius: 10px;
            padding: 6px 10px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .id-preview-action[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .id-preview-zoom {
            min-width: 60px;
            text-align: center;
            font-weight: 700;
            color: var(--ops-text);
        }

        .id-preview-stage {
            background: var(--ops-surface-muted);
            border: 1px solid var(--ops-border);
            border-radius: 12px;
            height: 70vh;
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            cursor: grab;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .id-preview-stage::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        #idPreviewModal .modal-content {
            max-height: min(90vh, calc(100dvh - 24px));
            width: calc(100vw - 80px);
            max-width: 1600px;
        }

        .id-preview-image {
            display: block;
            max-width: none;
            max-height: none;
            border-radius: 10px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
            user-select: none;
            -webkit-user-drag: none;
            cursor: inherit;
        }

        .id-preview-stage,
        .id-preview-stage * {
            user-select: none;
        }

        .id-preview-stage.is-dragging {
            cursor: grabbing;
        }

        body.admin-operations-page .modal-header h3,
        body.admin-operations-page .modal-header p,
        body.admin-operations-page .modal-body,
        body.admin-operations-page .form-label,
        body.admin-operations-page .room-name,
        body.admin-operations-page .room-capacity,
        body.admin-operations-page .room-current-set,
        body.admin-operations-page .room-limit-note,
        body.admin-operations-page .room-distribution-limit {
            color: var(--ops-text);
        }

        body.admin-operations-page .room-capacity {
            color: var(--ops-text-dim);
        }

        body.admin-operations-page .form-select,
        body.admin-operations-page .form-input,
        body.admin-operations-page .room-input,
        body.admin-operations-page .room-btn {
            background: var(--ops-surface);
            border-color: var(--ops-border-strong);
            color: var(--ops-text);
        }

        body.admin-operations-page .inventory-date-range {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        body.admin-operations-page .inventory-date-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        body.admin-operations-page .inventory-date-field span {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--ops-text-dim);
        }

        body.admin-operations-page .form-input {
            padding: 9px 12px;
            border-radius: 10px;
            border: 1px solid var(--ops-border-strong);
            font-family: inherit;
            font-size: 0.88rem;
        }

        body.admin-operations-page .room-card {
            border: 1px solid var(--ops-border);
            background: var(--ops-surface-muted);
            border-radius: 10px;
            padding: 12px;
        }

        body.admin-operations-page .booking-management-grid {
            display: grid;
            grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
            gap: 12px;
            min-height: 620px;
        }

        body.admin-operations-page .booking-list-panel,
        body.admin-operations-page .booking-detail-panel {
            border: 1px solid var(--ops-border);
            border-radius: 12px;
            background: linear-gradient(180deg, var(--ops-surface) 0%, var(--ops-surface-soft) 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        body.admin-operations-page .booking-list-scroll {
            max-height: 620px;
            overflow-y: auto;
            padding: 8px;
            display: grid;
            gap: 8px;
        }

        body.admin-operations-page .booking-list-item {
            display: block;
            border: 1px solid transparent;
            border-radius: 10px;
            padding: 9px;
            background: var(--ops-surface-muted);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        body.admin-operations-page .booking-list-item:hover {
            border-color: var(--ops-brand);
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.14);
            transform: translateY(-1px);
        }

        body.admin-operations-page .booking-list-item.is-active {
            border-color: var(--ops-brand);
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.16) 0%, rgba(34, 211, 238, 0.08) 100%);
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.24);
        }

        body.admin-operations-page .booking-list-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
        }

        body.admin-operations-page .booking-list-id {
            border: 1px solid var(--ops-border-strong);
            background: var(--ops-surface);
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 0.64rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: var(--ops-text-dim);
            text-transform: uppercase;
        }

        body.admin-operations-page .booking-list-booked {
            color: var(--ops-text-dim);
            font-size: 0.67rem;
        }

        body.admin-operations-page .booking-list-guest {
            color: var(--ops-text);
            font-size: 0.84rem;
            font-weight: 700;
            margin-bottom: 2px;
        }

        body.admin-operations-page .booking-list-room,
        body.admin-operations-page .booking-list-meta {
            color: var(--ops-text-dim);
            font-size: 0.71rem;
        }

        body.admin-operations-page .booking-list-meta {
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        body.admin-operations-page .booking-list-attempts {
            color: var(--ops-text-dim);
            font-size: 0.68rem;
        }

        body.admin-operations-page .booking-detail-note {
            font-size: 0.72rem;
            color: var(--ops-text-dim);
            margin-top: 6px;
        }

        body.admin-operations-page .booking-detail-panel {
            padding: 12px;
            display: flex;
            flex-direction: column;
            min-height: 620px;
        }

        body.admin-operations-page .booking-detail-panel.is-loading {
            opacity: 0.65;
            pointer-events: none;
        }

        body.admin-operations-page .booking-detail-content {
            display: none;
            flex-direction: column;
            min-height: 100%;
        }

        body.admin-operations-page .booking-detail-content.is-active {
            display: flex;
        }

        body.admin-operations-page .booking-detail-header {
            border: 1px solid var(--ops-border);
            border-radius: 12px;
            background: var(--ops-surface-muted);
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }

        body.admin-operations-page .booking-detail-header-actions {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        body.admin-operations-page .booking-detail-identity {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        body.admin-operations-page .booking-avatar {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--ops-brand) 0%, var(--ops-brand-2) 100%);
            color: #fff;
            font-weight: 800;
            font-size: 0.9rem;
            letter-spacing: 0.03em;
        }

        body.admin-operations-page .booking-detail-name {
            color: var(--ops-text);
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }

        body.admin-operations-page .booking-detail-email {
            color: var(--ops-text-dim);
            font-size: 0.75rem;
            margin-top: 2px;
        }

        body.admin-operations-page .booking-detail-tags {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        body.admin-operations-page .booking-detail-body {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }

        body.admin-operations-page .booking-detail-card {
            border: 1px solid var(--ops-border);
            border-radius: 11px;
            background: var(--ops-surface-muted);
            padding: 11px;
        }

        body.admin-operations-page .booking-detail-card.full-width {
            grid-column: span 2;
        }

        body.admin-operations-page .booking-detail-card h4 {
            color: var(--ops-text-dim);
            font-size: 0.67rem;
            letter-spacing: 0.11em;
            text-transform: uppercase;
            margin: 0 0 10px;
            font-weight: 700;
        }

        body.admin-operations-page .booking-detail-row {
            display: grid;
            grid-template-columns: 96px 1fr;
            gap: 8px;
            margin-bottom: 6px;
        }

        body.admin-operations-page .booking-detail-row:last-child {
            margin-bottom: 0;
        }

        body.admin-operations-page .booking-detail-label {
            color: var(--ops-text-dim);
            font-size: 0.69rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
        }

        body.admin-operations-page .booking-detail-value {
            color: var(--ops-text);
            font-size: 0.78rem;
            font-weight: 600;
        }

        body.admin-operations-page .booking-docs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        body.admin-operations-page .booking-detail-actions {
            margin-top: auto;
            border-top: 1px solid var(--ops-border);
            padding-top: 10px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        body.admin-operations-page .booking-detail-empty {
            min-height: 360px;
            display: grid;
            place-items: center;
            color: var(--ops-text-dim);
            font-size: 0.86rem;
            border: 1px dashed var(--ops-border);
            border-radius: 12px;
            background: var(--ops-surface-muted);
        }

        html.dark-theme body.admin-operations-page .booking-list-item.is-active {
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.25) 0%, rgba(34, 211, 238, 0.12) 100%);
        }

        /* Dark mode exact tone matching (reference-driven) */
        html.dark-theme body.admin-operations-page {
            --ops-bg: radial-gradient(1100px 520px at 12% -10%, #141a24 0%, #0a0d14 38%, #06080d 100%);
            --ops-surface: #0b0f17;
            --ops-surface-soft: #0d121b;
            --ops-surface-muted: #111722;
            --ops-border: #1f2735;
            --ops-border-strong: #2b3446;
            --ops-text: #e8edf7;
            --ops-text-dim: #97a3bb;
            --ops-brand: #6366f1;
            --ops-brand-2: #4f46e5;
            --ops-shadow: 0 20px 48px rgba(0, 0, 0, 0.58);
        }

        html.dark-theme body.admin-operations-page .admin-navbar {
            background: #070a11;
            border-bottom: 1px solid #1a2130;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.48);
        }

        html.dark-theme body.admin-operations-page .auth-page.admin-page {
            background: var(--ops-bg) !important;
        }

        html.dark-theme body.admin-operations-page .admin-card {
            background: linear-gradient(180deg, #090d15 0%, #0b1018 100%);
            border-color: #1b2433;
            box-shadow: var(--ops-shadow);
        }

        html.dark-theme body.admin-operations-page .admin-panel {
            background: linear-gradient(180deg, #0b1018 0%, #0d121b 100%);
            border-color: #1f2735;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
        }

        html.dark-theme body.admin-operations-page .checkout-queue-widget,
        html.dark-theme body.admin-operations-page .history-filters,
        html.dark-theme body.admin-operations-page .booking-list-panel,
        html.dark-theme body.admin-operations-page .booking-detail-panel,
        html.dark-theme body.admin-operations-page .admin-table-wrapper,
        html.dark-theme body.admin-operations-page .modal-content {
            background: linear-gradient(180deg, #0b1018 0%, #0e131d 100%);
            border-color: #1f2735;
        }

        html.dark-theme body.admin-operations-page .stat-pill {
            background: #111722;
            border-color: #2a3345;
            color: #d3dbec;
            box-shadow: none;
        }

        html.dark-theme body.admin-operations-page .stat-pill-clickable:hover {
            border-color: #4f46e5;
            color: #c7d2fe;
            box-shadow: 0 10px 22px rgba(79, 70, 229, 0.22);
        }

        html.dark-theme body.admin-operations-page .admin-month-nav {
            background: #101621;
            border-color: #262f3f;
        }

        html.dark-theme body.admin-operations-page .calendar-day {
            background: #141923;
            border-color: #262f3f;
            box-shadow: none;
        }

        html.dark-theme body.admin-operations-page .calendar-day:hover {
            border-color: #3a4358;
            box-shadow: inset 0 0 0 1px rgba(99, 102, 241, 0.22);
        }

        html.dark-theme body.admin-operations-page .calendar-event,
        html.dark-theme body.admin-operations-page .room-card,
        html.dark-theme body.admin-operations-page .queue-item,
        html.dark-theme body.admin-operations-page .checkout-release-row,
        html.dark-theme body.admin-operations-page .booking-detail-header,
        html.dark-theme body.admin-operations-page .booking-detail-card,
        html.dark-theme body.admin-operations-page .booking-list-item {
            background: #121823;
            border-color: #273144;
        }

        html.dark-theme body.admin-operations-page .booking-list-item.is-active {
            border-color: #4f46e5;
            background: linear-gradient(180deg, rgba(79, 70, 229, 0.26) 0%, rgba(79, 70, 229, 0.14) 100%);
            box-shadow: 0 10px 24px rgba(79, 70, 229, 0.28);
        }

        html.dark-theme body.admin-operations-page .btn-primary {
            background: linear-gradient(90deg, #4f46e5 0%, #4338ca 100%);
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.32);
        }

        html.dark-theme body.admin-operations-page .btn-outline {
            background: #171d2a;
            border-color: #2c3648;
            color: #d2dbee;
        }

        html.dark-theme body.admin-operations-page .btn-outline:hover {
            border-color: #4f46e5;
            background: #1d2434;
            color: #c7d2fe;
        }

        html.dark-theme body.admin-operations-page .legend-pill.pending {
            background: rgba(245, 158, 11, 0.13);
            border-color: rgba(245, 158, 11, 0.34);
            color: #fbbf24;
        }

        html.dark-theme body.admin-operations-page .legend-pill.confirmed {
            background: rgba(16, 185, 129, 0.14);
            border-color: rgba(16, 185, 129, 0.34);
            color: #34d399;
        }

        @media (max-width: 980px) {
            body.admin-operations-page .admin-hero {
                flex-direction: column;
            }

            body.admin-operations-page .admin-hero h2 {
                font-size: 1.6rem;
            }

            body.admin-operations-page .filter-group {
                min-width: 100%;
            }

            body.admin-operations-page .filter-actions {
                width: 100%;
                justify-content: flex-end;
            }

            body.admin-operations-page .booking-management-grid {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            body.admin-operations-page .booking-list-scroll {
                max-height: 280px;
            }

            body.admin-operations-page .booking-detail-body {
                grid-template-columns: 1fr;
            }

            body.admin-operations-page .booking-detail-card.full-width {
                grid-column: span 1;
            }

            body.admin-operations-page .booking-detail-header-actions {
                align-items: stretch;
                width: 100%;
            }
        }
    </style>
</head>
<body class="admin-operations-page">
    <nav class="admin-navbar">
        <div class="container admin-nav-container">
            <div class="admin-logo">
                <img src="{{ asset('image/PearlMNL_LOGO.png') }}" alt="Pearl Manila">
                Admin Console
            </div>
            <ul class="admin-nav-menu">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.operations') }}">Booking &amp; Inventory</a></li>
                <li><a href="{{ route('admin.users') }}">Admin Management</a></li>
                <li>
                    <button class="theme-toggle" type="button" aria-label="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                        <span class="theme-toggle-label">Dark</span>
                    </button>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <section class="auth-page admin-page">
        <div class="container admin-container">
            <div class="admin-card">
                <div class="admin-hero">
                    <div>
                        <h2>Operations Center</h2>
                        <p>Manage bookings, confirmations, cancellations, and floor inventory.</p>
                    </div>
                    <div class="admin-hero-stats">
                        <button class="stat-pill stat-pill-clickable" id="openPendingQueue" type="button">
                            <i class="fas fa-clock"></i> Pending {{ $pendingBookings->count() }}
                        </button>
                        <button class="stat-pill stat-pill-clickable" id="openCheckoutQueue" type="button">
                            <i class="fas fa-bell"></i> Checkouts Today {{ $checkoutQueue->count() }}
                        </button>
                        <span class="stat-pill">
                            <i class="fas fa-check-circle"></i> Confirmed {{ $confirmedBookings->count() }}
                        </span>
                    </div>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="checkout-queue-widget" id="checkoutQueueWidget">
                    <div class="checkout-queue-widget-head">
                        <div>
                            <h4><i class="fas fa-bell"></i> Checkout Release Queue</h4>
                            <p>Customers checking out today.</p>
                        </div>
                        <div class="checkout-queue-widget-actions">
                            <button class="btn btn-outline btn-sm" type="button" id="enlargeCheckoutQueue">Enlarge</button>
                            <button class="btn btn-outline btn-sm" type="button" id="collapseCheckoutQueue">Close</button>
                        </div>
                    </div>
                    <div class="checkout-queue-widget-body">
                        @if ($checkoutQueue->count() > 0)
                            <div class="checkout-queue-inline-list">
                                @foreach ($checkoutQueue->take(3) as $checkoutBooking)
                                    <div class="checkout-queue-inline-item">
                                        {{ $checkoutBooking->user->name }} · {{ $checkoutBooking->room->name }} · Due out {{ \Carbon\Carbon::parse($checkoutBooking->check_out_time)->format('h:i A') }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>No checkout releases pending right now.</p>
                        @endif
                    </div>
                </div>

                @php
                    $calendarBookings = $pendingBookings->concat($confirmedBookings)->concat($cancelledBookings);
                    $bookingsByDate = $calendarBookings->groupBy(fn ($booking) => $booking->check_in_date);
                @endphp

                <div class="admin-layout">
                        <div class="admin-panel calendar-panel">
                            <div class="calendar-header">
                                <div>
                                    <h3>Booking Calendar</h3>
                                    <p>Click a date to set inventory. Hover to reveal bookings and manage confirmations/cancellations.</p>
                                </div>
                                <div class="calendar-legend">
                                    <span class="legend-pill pending"><span class="legend-dot"></span> Pending Verification</span>
                                    <span class="legend-pill confirmed"><span class="legend-dot"></span> Confirmed</span>
                                </div>
                            </div>

                            @php
                                $calendarMonths = $calendarDays->groupBy(fn ($day) => $day->format('Y-m'));
                            @endphp
                            <div class="admin-month-nav" id="adminMonthNav">
                                <button class="admin-month-nav-btn" type="button" id="adminMonthPrev">
                                    <i class="fas fa-chevron-left"></i>
                                    <span>Previous</span>
                                </button>
                                <div class="admin-month-nav-title-wrap">
                                    <h4 class="admin-month-nav-title" id="adminMonthTitle"></h4>
                                    <p class="admin-month-nav-subtitle">Monthly view</p>
                                </div>
                                <button class="admin-month-nav-btn" type="button" id="adminMonthNext">
                                    <span>Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            @foreach ($calendarMonths as $monthDays)
                                <div class="admin-month-block {{ $loop->first ? 'is-active' : '' }}" data-month-label="{{ $monthDays->first()->format('F Y') }}">
                                    <div class="calendar-grid">
                                        @foreach ($monthDays as $day)
                                            @php
                                                $dateKey = $day->toDateString();
                                                $dayBookings = $bookingsByDate->get($dateKey, collect());
                                                $isPastDate = $day->lt(\Carbon\Carbon::today()->startOfDay());
                                                $highlightClass = '';
                                                if ($dayBookings->contains(fn ($booking) => in_array($booking->status, ['confirmed', 'checked_in', 'checkout_scheduled'], true))) {
                                                    $highlightClass = 'has-confirmed';
                                                } elseif ($dayBookings->contains(fn ($booking) => in_array($booking->status, ['pending', 'pending_verification'], true))) {
                                                    $highlightClass = 'has-pending';
                                                }

                                                $dayStatusCounts = $dayBookings->groupBy('status')->map->count();
                                                $availabilityTotal = 0;
                                                foreach ($inventoryRooms as $inventoryRoom) {
                                                    foreach ($floors as $floor) {
                                                        $inventoryKey = $dateKey.'|'.$floor->id.'-'.$inventoryRoom->id;
                                                        $availabilityTotal += (int) ($inventory->get($inventoryKey)?->available_rooms ?? 0);
                                                    }
                                                }

                                                $dayBookingsPayload = $dayBookings->map(function ($booking) {
                                                    return [
                                                        'id' => $booking->id,
                                                        'guest' => $booking->user?->name ?? 'Guest',
                                                        'room' => $booking->room?->name ?? 'Room',
                                                        'status' => $booking->status,
                                                        'check_in_time' => $booking->check_in_time,
                                                        'check_out_time' => $booking->check_out_time,
                                                        'rooms_count' => $booking->rooms_count,
                                                    ];
                                                })->values();
                                            @endphp
                                            <div class="calendar-day {{ $highlightClass }} {{ $isPastDate ? 'is-past-date' : '' }}" data-inventory-date="{{ $dateKey }}">
                                                <div class="calendar-date">
                                                    <span class="calendar-weekday">{{ $day->format('D') }}</span>
                                                    <span class="calendar-daynum">{{ $day->format('d') }}</span>
                                                </div>
                                                @if (! $isPastDate && ($availabilityTotal > 0 || $dayStatusCounts->count() > 0))
                                                    <div class="calendar-status-dots">
                                                        @if ($availabilityTotal > 0)
                                                            <span class="status-dot status-dot-available" title="Available rooms set"></span>
                                                        @endif
                                                        @if ($dayStatusCounts->has('pending') || $dayStatusCounts->has('pending_verification'))
                                                            <span class="status-dot status-dot-pending" title="Pending booking(s)"></span>
                                                        @endif
                                                        @if ($dayStatusCounts->has('confirmed'))
                                                            <span class="status-dot status-dot-confirmed" title="Confirmed booking(s)"></span>
                                                        @endif
                                                        @if ($dayStatusCounts->has('checked_in') || $dayStatusCounts->has('checkout_scheduled'))
                                                            <span class="status-dot status-dot-checked_in" title="Checked in / checkout scheduled"></span>
                                                        @endif
                                                        @if ($dayStatusCounts->has('cancelled'))
                                                            <span class="status-dot status-dot-cancelled" title="Cancelled booking(s)"></span>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="calendar-events">
                                                    @forelse ($dayBookings as $booking)
                                                        <div class="calendar-event is-{{ $booking->status }}">
                                                            <div class="calendar-event-title">{{ $booking->room->name }}</div>
                                                            <div class="calendar-event-meta">{{ $booking->user->name }} · {{ $booking->check_in_time }} - {{ $booking->check_out_time }}</div>
                                                            <div class="calendar-event-meta">Guests: {{ $booking->adults }} adults, {{ $booking->children }} children · Rooms: {{ $booking->rooms_count }}</div>
                                                            <div class="calendar-event-meta">Status: {{ ucfirst(str_replace('_', ' ', $booking->status)) }}</div>
                                                            <div class="calendar-event-actions">
                                                                @php
                                                                    $idDocuments = collect($booking->id_document_paths ?? [])->filter()->values();
                                                                    if ($idDocuments->isEmpty() && !empty($booking->id_document_path)) {
                                                                        $idDocuments = collect([$booking->id_document_path]);
                                                                    }
                                                                @endphp
                                                                @if ($idDocuments->count() <= 1)
                                                                    <button class="btn btn-outline btn-sm js-preview-id" type="button" data-preview-url="{{ route('admin.bookings.id-document', ['booking' => $booking, 'file' => 0, 'preview' => 1]) }}">View ID</button>
                                                                @else
                                                                    @foreach ($idDocuments as $index => $idDocumentPath)
                                                                        <button class="btn btn-outline btn-sm js-preview-id" type="button" data-preview-url="{{ route('admin.bookings.id-document', ['booking' => $booking, 'file' => $index, 'preview' => 1]) }}">View ID {{ $index + 1 }}</button>
                                                                    @endforeach
                                                                @endif
                                                                @if (empty($booking->verified_at))
                                                                    <form method="POST" action="{{ route('admin.bookings.verify', $booking) }}" onsubmit="return confirm('Verify customer ID for this booking?');">
                                                                        @csrf
                                                                        <button class="btn btn-outline btn-sm" type="submit">Verify ID</button>
                                                                    </form>
                                                                @else
                                                                    <span class="history-status status-confirmed">Verified by {{ $booking->verifiedByAdmin?->name ?? 'Admin' }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="calendar-event-actions">
                                                                @if (in_array($booking->status, ['pending', 'pending_verification'], true))
                                                                    <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" onsubmit="return confirm('Confirm this booking now?');">
                                                                        @csrf
                                                                        <button class="btn btn-primary btn-sm" type="submit" {{ empty($booking->verified_at) ? 'disabled' : '' }}>Confirm</button>
                                                                    </form>
                                                                @endif
                                                                @if ($booking->status === 'confirmed')
                                                                    <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}" onsubmit="return confirm('Mark this guest as checked in?');">
                                                                        @csrf
                                                                        <button class="btn btn-outline btn-sm" type="submit">Check In</button>
                                                                    </form>
                                                                @endif
                                                                @if (in_array($booking->status, ['pending', 'pending_verification', 'confirmed'], true))
                                                                    <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                                        @csrf
                                                                        <button class="btn btn-outline admin-cancel btn-sm" type="submit">Cancel</button>
                                                                    </form>
                                                                @endif
                                                                @if (in_array($booking->status, ['checked_in', 'checkout_scheduled'], true))
                                                                    <form method="POST" action="{{ route('admin.bookings.checkout-release-now', $booking) }}" onsubmit="return confirm('Check out this guest now and release the room?');">
                                                                        @csrf
                                                                        <button class="btn btn-primary btn-sm" type="submit">Early Checkout</button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="calendar-empty">No bookings</div>
                                                    @endforelse
                                                </div>
                                                <div class="calendar-day-footer">
                                                    <div class="calendar-empty-inline">
                                                        @if ($isPastDate)
                                                            <span class="calendar-unavailable-pill">Unavailable</span>
                                                        @else
                                                            {{ $dayBookings->count() > 0 ? $dayBookings->count().' booking(s)' : 'No bookings' }}
                                                        @endif
                                                    </div>
                                                    @if (! $isPastDate)
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline btn-xs js-day-details"
                                                            data-date="{{ $dateKey }}"
                                                            data-status-counts='@json($dayStatusCounts)'
                                                            data-bookings='@json($dayBookingsPayload)'
                                                        >
                                                            View details
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>



                        <div class="admin-panel history-panel">
                            <div class="calendar-header">
                                <div>
                                    <h3>Booking History</h3>
                                    <p>View all customer bookings across pending, confirmed, and cancelled statuses.</p>
                                </div>
                            </div>

                            <div class="history-filters">
                                <form method="GET" action="{{ route('admin.operations') }}" id="historyFilterForm">
                                    <div class="filter-row">
                                        <div class="filter-group">
                                            <label for="searchInput"><i class="fas fa-search"></i> Search</label>
                                            <input 
                                                type="text" 
                                                id="searchInput" 
                                                name="search" 
                                                placeholder="Customer name or email..."
                                                value="{{ request('search') }}"
                                                class="filter-input">
                                        </div>
                                        
                                        <div class="filter-group">
                                            <label for="statusFilter"><i class="fas fa-filter"></i> Status</label>
                                            <select id="statusFilter" name="status_filter" class="filter-select">
                                                <option value="">All Statuses</option>
                                                <option value="pending" {{ request('status_filter') === 'pending' ? 'selected' : '' }}>Pending (Legacy)</option>
                                                <option value="pending_verification" {{ request('status_filter') === 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                                                <option value="confirmed" {{ request('status_filter') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                <option value="checked_in" {{ request('status_filter') === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                                                <option value="checkout_scheduled" {{ request('status_filter') === 'checkout_scheduled' ? 'selected' : '' }}>Checkout Scheduled</option>
                                                <option value="checked_out" {{ request('status_filter') === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                                                <option value="cancelled" {{ request('status_filter') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                        
                                        <div class="filter-group">
                                            <label for="sortBy"><i class="fas fa-sort"></i> Sort By</label>
                                            <select id="sortBy" name="sort_by" class="filter-select">
                                                <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Date Booked</option>
                                                <option value="check_in_date" {{ request('sort_by') === 'check_in_date' ? 'selected' : '' }}>Check-in Date</option>
                                                <option value="check_out_date" {{ request('sort_by') === 'check_out_date' ? 'selected' : '' }}>Check-out Date</option>
                                                <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Last Updated</option>
                                            </select>
                                        </div>
                                        
                                        <div class="filter-group">
                                            <label for="sortOrder"><i class="fas fa-arrow-down-wide-short"></i> Order</label>
                                            <select id="sortOrder" name="sort_order" class="filter-select">
                                                <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Newest First</option>
                                                <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Oldest First</option>
                                            </select>
                                        </div>
                                        
                                        <div class="filter-actions">
                                            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                                            <a href="{{ route('admin.operations') }}" class="btn btn-outline btn-sm">Clear</a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            @if ($bookingHistory->count() > 0)
                                <div class="history-count">
                                    <span>Showing {{ $bookingHistory->count() }} {{ $bookingHistory->count() === 1 ? 'booking' : 'bookings' }}</span>
                                </div>
                                <div class="booking-management-grid">
                                    <aside class="booking-list-panel">
                                        <div class="booking-list-scroll">
                                            @foreach ($bookingHistory as $booking)
                                                @php
                                                    $isActiveBooking = !empty($selectedBooking) && $selectedBooking->id === $booking->id;
                                                @endphp
                                                <a
                                                    href="{{ route('admin.operations', array_merge(request()->except('page', 'selected_booking'), ['selected_booking' => $booking->id])) }}"
                                                    class="booking-list-item {{ $isActiveBooking ? 'is-active' : '' }}"
                                                    data-booking-select
                                                    data-booking-id="{{ $booking->id }}"
                                                >
                                                    <div class="booking-list-top">
                                                        <span class="booking-list-id">BK-{{ $booking->id }}</span>
                                                        <span class="booking-list-booked">{{ $booking->created_at->format('M d') }}</span>
                                                    </div>
                                                    <div class="booking-list-guest">{{ $booking->user->name }}</div>
                                                    <div class="booking-list-room">{{ $booking->room->name }}</div>
                                                    <div class="booking-list-meta">
                                                        <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d') }}</span>
                                                        <span class="history-status status-{{ $booking->status }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                                                    </div>
                                                    <div class="booking-list-attempts">
                                                        Payment attempts: {{ (int) ($booking->payment_attempts ?? 0) }} / 3
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </aside>
                                    <section class="booking-detail-panel" id="bookingDetailPanel" data-active-booking-id="{{ $selectedBooking->id ?? '' }}">
                                        @if (!empty($selectedBooking))
                                        @php
                                            $selectedBookingIdDocuments = collect($selectedBooking->id_document_paths ?? [])->filter()->values();
                                            if ($selectedBookingIdDocuments->isEmpty() && !empty($selectedBooking->id_document_path)) {
                                                $selectedBookingIdDocuments = collect([$selectedBooking->id_document_path]);
                                            }
                                            $stayNights = max(
                                                \Carbon\Carbon::parse($selectedBooking->check_in_date)->startOfDay()->diffInDays(\Carbon\Carbon::parse($selectedBooking->check_out_date)->startOfDay()),
                                                1
                                            );
                                            $baseRate = (float) ($selectedBooking->room->base_rate ?? 0);
                                            $estimatedTotal = $baseRate * $stayNights * (int) $selectedBooking->rooms_count;
                                            $paymentStatus = $selectedBooking->payment_status ?? 'unpaid';
                                        @endphp
                                            <div class="booking-detail-header">
                                                <div class="booking-detail-identity">
                                                    <div class="booking-avatar">
                                                        {{ collect(explode(' ', $selectedBooking->user->name))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                                                    </div>
                                                    <div>
                                                        <h4 class="booking-detail-name">{{ $selectedBooking->user->name }}</h4>
                                                        <div class="booking-detail-email">{{ $selectedBooking->user->email }}</div>
                                                        <div class="booking-detail-tags">
                                                            <span class="booking-list-id">BK-{{ $selectedBooking->id }}</span>
                                                            <span class="history-status status-{{ $selectedBooking->status }}">{{ ucfirst(str_replace('_', ' ', $selectedBooking->status)) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="booking-detail-header-actions">
                                                    <button
                                                        class="btn btn-outline btn-sm js-open-booking-details"
                                                        type="button"
                                                        data-guest-name="{{ e($selectedBooking->user->name) }}"
                                                        data-guest-email="{{ e($selectedBooking->user->email) }}"
                                                        data-room-name="{{ e($selectedBooking->room->name) }}"
                                                        data-check-in-date="{{ \Carbon\Carbon::parse($selectedBooking->check_in_date)->format('M d, Y') }}"
                                                        data-check-in-time="{{ e($selectedBooking->check_in_time) }}"
                                                        data-check-out-date="{{ \Carbon\Carbon::parse($selectedBooking->check_out_date)->format('M d, Y') }}"
                                                        data-check-out-time="{{ e($selectedBooking->check_out_time) }}"
                                                        data-adults="{{ $selectedBooking->adults }}"
                                                        data-children="{{ $selectedBooking->children }}"
                                                        data-rooms-count="{{ $selectedBooking->rooms_count }}"
                                                        data-status="{{ ucfirst(str_replace('_', ' ', $selectedBooking->status)) }}"
                                                        data-verification="{{ $selectedBooking->verified_at ? 'Verified by '.($selectedBooking->verifiedByAdmin?->name ?? 'Admin') : 'Not yet verified' }}"
                                                        data-booked-at="{{ $selectedBooking->created_at->format('M d, Y H:i') }}"
                                                    >
                                                        View Full Details
                                                    </button>
                                                    @if ($selectedBooking->status === 'confirmed')
                                                        <form method="POST" action="{{ route('admin.bookings.cancel', $selectedBooking) }}" onsubmit="return confirm('Are you sure you want to cancel this confirmed booking?');">
                                                            @csrf
                                                            <button class="btn btn-outline admin-cancel btn-sm" type="submit">Cancel Booking</button>
                                                        </form>
                                                    @elseif (in_array($selectedBooking->status, ['checked_in', 'checkout_scheduled'], true))
                                                        <form method="POST" action="{{ route('admin.bookings.checkout-release-now', $selectedBooking) }}" onsubmit="return confirm('Check out this guest now and release the room?');">
                                                            @csrf
                                                            <button class="btn btn-primary btn-sm" type="submit">Early Checkout</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="booking-detail-body">
                                                <div class="booking-detail-card">
                                                    <h4>Stay Information</h4>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Check In</div>
                                                        <div class="booking-detail-value">{{ \Carbon\Carbon::parse($selectedBooking->check_in_date)->format('M d, Y') }} {{ $selectedBooking->check_in_time }}</div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Check Out</div>
                                                        <div class="booking-detail-value">{{ \Carbon\Carbon::parse($selectedBooking->check_out_date)->format('M d, Y') }} {{ $selectedBooking->check_out_time }}</div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Room</div>
                                                        <div class="booking-detail-value">{{ $selectedBooking->room->name }}</div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Guests</div>
                                                        <div class="booking-detail-value">{{ $selectedBooking->adults }}A / {{ $selectedBooking->children }}C · {{ $selectedBooking->rooms_count }} room(s)</div>
                                                    </div>
                                                </div>
                                                <div class="booking-detail-card">
                                                    <h4>Identity and Verification</h4>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Documents</div>
                                                        <div class="booking-detail-value">
                                                            @if ($selectedBookingIdDocuments->isEmpty())
                                                                No ID uploaded
                                                            @elseif ($selectedBookingIdDocuments->count() === 1)
                                                                1 ID file
                                                            @else
                                                                {{ $selectedBookingIdDocuments->count() }} ID files
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Verified By</div>
                                                        <div class="booking-detail-value">{{ $selectedBooking->verifiedByAdmin?->name ?? '-' }}</div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Booked At</div>
                                                        <div class="booking-detail-value">{{ $selectedBooking->created_at->format('M d, Y H:i') }}</div>
                                                    </div>
                                                    <div class="booking-docs">
                                                        @if ($selectedBookingIdDocuments->isEmpty())
                                                            <span class="history-status">N/A</span>
                                                        @elseif ($selectedBookingIdDocuments->count() === 1)
                                                            <button class="btn btn-outline btn-sm js-preview-id" type="button" data-booking-id="{{ $selectedBooking->id }}" data-preview-url="{{ route('admin.bookings.id-document', ['booking' => $selectedBooking, 'file' => 0, 'preview' => 1]) }}">View ID</button>
                                                        @else
                                                            @foreach ($selectedBookingIdDocuments as $index => $idDocumentPath)
                                                                <button class="btn btn-outline btn-sm js-preview-id" type="button" data-booking-id="{{ $selectedBooking->id }}" data-preview-url="{{ route('admin.bookings.id-document', ['booking' => $selectedBooking, 'file' => $index, 'preview' => 1]) }}">ID {{ $index + 1 }}</button>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                                  <div class="booking-detail-card full-width">
                                                      <h4>Payment Summary</h4>
                                                      <div class="booking-detail-row">
                                                          <div class="booking-detail-label">Base Rate</div>
                                                          <div class="booking-detail-value">
                                                            @if ($baseRate > 0)
                                                                PHP {{ number_format($baseRate, 2) }} / night
                                                            @else
                                                                Rate not set
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Stay Nights</div>
                                                        <div class="booking-detail-value">{{ $stayNights }} night(s)</div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Estimate</div>
                                                        <div class="booking-detail-value">
                                                            @if ($baseRate > 0)
                                                                PHP {{ number_format($estimatedTotal, 2) }}
                                                            @else
                                                                Pending room rate configuration
                                                            @endif
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="booking-detail-card full-width">
                                                      <h4>Payment Verification</h4>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Status</div>
                                                        <div class="booking-detail-value">
                                                            @if ($paymentStatus === 'submitted')
                                                                <span class="history-status status-pending_verification">Payment to be Confirmed</span>
                                                            @elseif ($paymentStatus === 'verified')
                                                                <span class="history-status status-confirmed">Payment Verified</span>
                                                            @elseif ($paymentStatus === 'rejected')
                                                                <span class="history-status status-cancelled">Payment Rejected</span>
                                                            @elseif ($paymentStatus === 'pay_on_site')
                                                                <span class="history-status status-pending">Pay on Site</span>
                                                            @else
                                                                <span class="history-status status-pending">Unpaid</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Attempts</div>
                                                        <div class="booking-detail-value">{{ (int) ($selectedBooking->payment_attempts ?? 0) }} / 3</div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Proof</div>
                                                        <div class="booking-detail-value">
                                                            @if (!empty($selectedBooking->payment_proof_path))
                                                                <button class="btn btn-outline btn-sm js-preview-id" type="button" data-booking-id="{{ $selectedBooking->id }}" data-preview-url="{{ route('admin.bookings.payment-proof', ['booking' => $selectedBooking, 'preview' => 1]) }}">View Payment</button>
                                                            @else
                                                                No payment proof uploaded
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Amount Paid</div>
                                                        <div class="booking-detail-value">
                                                            @if (!empty($selectedBooking->payment_amount))
                                                                PHP {{ number_format((float) $selectedBooking->payment_amount, 2) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Reference No</div>
                                                        <div class="booking-detail-value">{{ $selectedBooking->payment_reference ?? '-' }}</div>
                                                    </div>
                                                    <div class="booking-detail-row">
                                                        <div class="booking-detail-label">Reviewed By</div>
                                                        <div class="booking-detail-value">{{ $selectedBooking->paymentVerifiedByAdmin?->name ?? '-' }}</div>
                                                    </div>
                                                      @if (!empty($selectedBooking->payment_notes))
                                                          <div class="booking-detail-row">
                                                              <div class="booking-detail-label">Notes</div>
                                                              <div class="booking-detail-value">{{ $selectedBooking->payment_notes }}</div>
                                                          </div>
                                                      @endif
                                                  </div>
                                              </div>
                                              <div class="booking-detail-actions">
                                                  @if (in_array($selectedBooking->status, ['pending', 'pending_verification'], true))
                                                      @if (empty($selectedBooking->verified_at))
                                                          @php
                                                              $disableVerifyId = empty($selectedBookingIdViewed);
                                                          @endphp
                                                          <form method="POST" action="{{ route('admin.bookings.verify', $selectedBooking) }}" onsubmit="return confirm('Verify customer ID for this booking?');">
                                                            @csrf
                                                            <button class="btn btn-outline btn-sm" type="submit" data-requires-id-view {{ $disableVerifyId ? 'disabled' : '' }} title="{{ $disableVerifyId ? 'View ID to enable verification' : '' }}">Verify ID</button>
                                                        </form>
                                                        @if ($disableVerifyId)
                                                            <div class="booking-detail-note" data-note="id">View the ID document to enable verification.</div>
                                                        @endif
                                                    @endif
                                                    <form method="POST" action="{{ route('admin.bookings.confirm', $selectedBooking) }}" onsubmit="return confirm('Confirm this booking now?');">
                                                        @csrf
                                                        <button class="btn btn-primary btn-sm" type="submit" {{ empty($selectedBooking->verified_at) ? 'disabled' : '' }}>Confirm</button>
                                                      </form>
                                                      <form method="POST" action="{{ route('admin.bookings.cancel', $selectedBooking) }}" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                          @csrf
                                                          <button class="btn btn-outline admin-cancel btn-sm" type="submit">Cancel Booking</button>
                                                      </form>
                                                      @if ($paymentStatus === 'submitted')
                                                          @php
                                                              $disableVerifyPayment = empty($selectedBookingPaymentViewed);
                                                          @endphp
                                                          <form method="POST" action="{{ route('admin.bookings.payment-verify', $selectedBooking) }}" onsubmit="return confirm('Verify this payment proof?');">
                                                              @csrf
                                                              <button class="btn btn-primary btn-sm" type="submit" data-requires-payment-view {{ $disableVerifyPayment ? 'disabled' : '' }} title="{{ $disableVerifyPayment ? 'View payment proof to enable verification' : '' }}">Verify Payment</button>
                                                          </form>
                                                          <form method="POST" action="{{ route('admin.bookings.payment-reject', $selectedBooking) }}" onsubmit="return confirm('Reject this payment proof?');">
                                                              @csrf
                                                              <button class="btn btn-outline admin-cancel btn-sm" type="submit" data-requires-payment-view {{ $disableVerifyPayment ? 'disabled' : '' }} title="{{ $disableVerifyPayment ? 'View payment proof to enable rejection' : '' }}">Reject Payment</button>
                                                          </form>
                                                          @if ($disableVerifyPayment)
                                                              <div class="booking-detail-note" data-note="payment">View the payment proof to enable verification or rejection.</div>
                                                          @endif
                                                      @endif
                                                  @elseif ($selectedBooking->status === 'confirmed')
                                                      <form method="POST" action="{{ route('admin.bookings.check-in', $selectedBooking) }}" onsubmit="return confirm('Mark this guest as checked in?');">
                                                          @csrf
                                                          <button class="btn btn-primary btn-sm" type="submit">Check In Guest</button>
                                                      </form>
                                                      <form method="POST" action="{{ route('admin.bookings.cancel', $selectedBooking) }}" onsubmit="return confirm('Are you sure you want to cancel this confirmed booking?');">
                                                          @csrf
                                                          <button class="btn btn-outline admin-cancel btn-sm" type="submit">Cancel Booking</button>
                                                      </form>
                                                      @if ($paymentStatus === 'submitted')
                                                          <form method="POST" action="{{ route('admin.bookings.payment-verify', $selectedBooking) }}" onsubmit="return confirm('Verify this payment proof?');">
                                                              @csrf
                                                              <button class="btn btn-primary btn-sm" type="submit">Verify Payment</button>
                                                          </form>
                                                          <form method="POST" action="{{ route('admin.bookings.payment-reject', $selectedBooking) }}" onsubmit="return confirm('Reject this payment proof?');">
                                                              @csrf
                                                              <button class="btn btn-outline admin-cancel btn-sm" type="submit">Reject Payment</button>
                                                          </form>
                                                      @endif
                                                  @elseif (in_array($selectedBooking->status, ['checked_in', 'checkout_scheduled'], true))
                                                      <form method="POST" action="{{ route('admin.bookings.checkout-release-now', $selectedBooking) }}" onsubmit="return confirm('Check out this guest now and release the room?');">
                                                          @csrf
                                                          <button class="btn btn-primary btn-sm" type="submit">Early Checkout</button>
                                                      </form>
                                                      @if ($paymentStatus === 'submitted')
                                                          <form method="POST" action="{{ route('admin.bookings.payment-verify', $selectedBooking) }}" onsubmit="return confirm('Verify this payment proof?');">
                                                              @csrf
                                                              <button class="btn btn-primary btn-sm" type="submit">Verify Payment</button>
                                                          </form>
                                                          <form method="POST" action="{{ route('admin.bookings.payment-reject', $selectedBooking) }}" onsubmit="return confirm('Reject this payment proof?');">
                                                              @csrf
                                                              <button class="btn btn-outline admin-cancel btn-sm" type="submit">Reject Payment</button>
                                                          </form>
                                                      @endif
                                                  @else
                                                      <span class="history-status">No actions available</span>
                                                  @endif
                                              </div>
                                        @else
                                            <div class="booking-detail-empty">Select a booking to view details.</div>
                                        @endif
                                    </section>
                                </div>
                            @else
                                <p class="calendar-empty">No bookings found matching your criteria.</p>
                            @endif
                        </div>

                        <div class="admin-panel room-settings-panel">
                            <div class="calendar-header">
                                <div>
                                    <h3>Room Settings</h3>
                                    <p>Update room names, base rates, and active status from one place.</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.rooms.update') }}" id="roomSettingsForm" onsubmit="return confirm('Are you sure you want to save room settings changes?');">
                                @csrf
                                <div class="admin-table-wrapper">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>Room Type</th>
                                                <th>Base Rate (PHP)</th>
                                                <th>Active</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rooms as $room)
                                                <tr>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="room-settings-input"
                                                            name="rooms[{{ $room->id }}][name]"
                                                            value="{{ $room->name }}"
                                                            required
                                                        >
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="number"
                                                            class="room-settings-input"
                                                            name="rooms[{{ $room->id }}][base_rate]"
                                                            min="0"
                                                            step="0.01"
                                                            value="{{ old('rooms.'.$room->id.'.base_rate', $room->base_rate) }}"
                                                        >
                                                    </td>
                                                    <td>
                                                        <label class="room-settings-toggle">
                                                            <input type="hidden" name="rooms[{{ $room->id }}][is_active]" value="0">
                                                            <input type="checkbox" name="rooms[{{ $room->id }}][is_active]" value="1" {{ $room->is_active ? 'checked' : '' }}>
                                                            <span>{{ $room->is_active ? 'Enabled' : 'Disabled' }}</span>
                                                        </label>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit">Save Room Settings</button>
                                </div>
                            </form>
                        </div>
                </div>

                <!-- Inventory Modal -->
                <div class="modal-overlay is-hidden" id="inventoryModal">
                    <div class="modal-content modal-inventory">
                        <div class="modal-header">
                            <div>
                                <h3><i class="fas fa-door-open"></i> Set Room Availability</h3>
                                <p id="modalInventoryDateText">Configure room counts for specific floors or bulk update.</p>
                            </div>
                            <button class="modal-close" id="closeInventoryModal" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.inventory.update') }}" id="inventoryForm">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">DATE RANGE (UP TO 90 DAYS)</label>
                                    <div class="inventory-date-range">
                                        <div class="inventory-date-field">
                                            <span>Start date</span>
                                            <input
                                                type="date"
                                                class="form-input"
                                                name="inventory_date"
                                                id="modalInventoryDate"
                                                value="{{ $inventoryDate }}"
                                                min="{{ \Carbon\Carbon::today()->toDateString() }}"
                                            >
                                        </div>
                                        <div class="inventory-date-field">
                                            <span>End date</span>
                                            <input
                                                type="date"
                                                class="form-input"
                                                name="inventory_end_date"
                                                id="modalInventoryEndDate"
                                                value="{{ $inventoryDate }}"
                                                min="{{ \Carbon\Carbon::today()->toDateString() }}"
                                            >
                                        </div>
                                    </div>
                                    <div class="room-distribution-hint">Set availability across a date range (max 90 days). Leave the end date the same as the start date for a single day.</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">TARGET FLOORS</label>
                                    <select class="form-select" id="floorSelector">
                                        <option value="all">Apply to Bookable Floors (15-16)</option>
                                        @foreach ($floors as $floor)
                                            <option value="{{ $floor->id }}">Floor {{ $floor->number }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">ROOM DISTRIBUTION</label>
                                    <div class="room-distribution-limit">
                                        Per-floor total limit: {{ $floorTotalLimit }} room(s) (Superior {{ $superiorFloorLimit }}, Junior {{ $juniorFloorLimit }})
                                    </div>
                                    <div class="room-distribution-helper">
                                        <label class="room-settings-toggle">
                                            <input type="checkbox" id="inventoryAdditiveToggle" name="inventory_additive" value="1">
                                            <span>Add to existing inventory (increment)</span>
                                        </label>
                                        <div class="room-distribution-hint">When enabled, values you enter are added on top of the current counts.</div>
                                    </div>
                                    <div class="room-distribution">
                                        @foreach ($inventoryRooms as $room)
                                            <div class="room-card">
                                                <div class="room-info">
                                                    <div class="room-icon">
                                                        <i class="fas fa-bed"></i>
                                                    </div>
                                                    <div>
                                                        <div class="room-name">{{ $room->name }}</div>
                                                        <div class="room-capacity">
                                                            @if(str_contains(strtolower($room->name), 'suite'))
                                                                Cap: 4 Guests
                                                            @else
                                                                Cap: 2 Guests
                                                            @endif
                                                        </div>
                                                        <div class="room-limit-note">Limit per floor: {{ $roomPerFloorLimits[$room->id] ?? $floorTotalLimit }} room(s)</div>
                                                        <div class="room-current-set" data-room-current="{{ $room->id }}">Currently set: 0 room(s) across 0 floor(s)</div>
                                                    </div>
                                                </div>
                                                <div class="room-controls">
                                                    <button type="button" class="room-btn room-btn-minus" data-room-id="{{ $room->id }}">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" 
                                                           class="room-input" 
                                                           data-room-id="{{ $room->id }}"
                                                           value="0" 
                                                           min="0" 
                                                           max="{{ $roomPerFloorLimits[$room->id] ?? $floorTotalLimit }}"
                                                           readonly>
                                                    <button type="button" class="room-btn room-btn-plus" data-room-id="{{ $room->id }}">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline" id="prefillDefaultInventory">Prefill Default {{ $superiorFloorLimit }}/{{ $juniorFloorLimit }}</button>
                                    <button type="button" class="btn btn-outline" id="quickClearInventory">Quick Clear All</button>
                                    <button type="button" class="btn btn-outline" id="cancelInventory">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Apply Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal-overlay is-hidden" id="inventorySuccessModal">
                    <div class="modal-content" style="width:min(460px, 94vw);">
                        <div class="modal-header">
                            <div>
                                <h3><i class="fas fa-circle-check"></i> Availability Updated</h3>
                                <p id="inventorySuccessMessage">Room availability has been saved.</p>
                            </div>
                            <button class="modal-close" id="closeInventorySuccessModal" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="button" id="inventorySuccessOk">OK</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-overlay is-hidden" id="dayDetailsModal">
                    <div class="modal-content" style="width:min(720px, 94vw);">
                        <div class="modal-header">
                            <div>
                                <h3><i class="fas fa-calendar-day"></i> Day Details</h3>
                                <p id="dayDetailsDate">Selected date</p>
                            </div>
                            <button class="modal-close" id="closeDayDetailsModal" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="day-details-summary" id="dayDetailsSummary"></div>
                            <div class="day-details-section">
                                <h4>Availability</h4>
                                <div class="day-details-list" id="dayAvailabilityList"></div>
                            </div>
                            <div class="day-details-section" style="margin-top: 16px;">
                                <h4>Bookings</h4>
                                <div class="day-details-list" id="dayBookingsList"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Queue Modal -->
                <div class="modal-overlay is-hidden" id="pendingQueueModal">
                    <div class="modal-content modal-queue">
                        <div class="modal-header">
                            <div>
                                <h3><i class="fas fa-clock"></i> Pending Queue</h3>
                                <p>Most recent booking requests needing action.</p>
                            </div>
                            <button class="modal-close" id="closePendingQueue" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="queue-list">
                                @forelse ($pendingBookings as $booking)
                                    @php
                                        $pendingStatusLabel = ucfirst(str_replace('_', ' ', $booking->status));
                                        $pendingVerificationLabel = $booking->verified_at
                                            ? 'ID verified' . ($booking->verifiedByAdmin?->name ? ' by ' . $booking->verifiedByAdmin->name : '')
                                            : 'ID pending review';
                                        $pendingCheckInLabel = \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y');
                                        $pendingCheckOutLabel = \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y');
                                        $pendingCreatedAtLabel = $booking->created_at->format('M d, Y H:i');
                                    @endphp
                                    <div class="queue-item">
                                        <div>
                                            <div class="queue-title">{{ $booking->user->name }}</div>
                                            <div class="queue-meta">{{ $booking->room->name }} · {{ $pendingCheckInLabel }} to {{ $pendingCheckOutLabel }}</div>
                                            <div class="queue-meta">{{ $booking->adults }}A/{{ $booking->children }}C · {{ $booking->rooms_count }} room(s) · {{ $pendingStatusLabel }}</div>
                                            <div class="queue-meta">{{ $pendingVerificationLabel }}</div>
                                        </div>
                                        <div class="queue-actions">
                                            <button
                                                class="btn btn-outline btn-sm js-open-booking-details"
                                                type="button"
                                                data-guest-name="{{ e($booking->user->name) }}"
                                                data-guest-email="{{ e($booking->user->email) }}"
                                                data-room-name="{{ e($booking->room->name) }}"
                                                data-check-in-date="{{ $pendingCheckInLabel }}"
                                                data-check-in-time="{{ e($booking->check_in_time) }}"
                                                data-check-out-date="{{ $pendingCheckOutLabel }}"
                                                data-check-out-time="{{ e($booking->check_out_time) }}"
                                                data-adults="{{ $booking->adults }}"
                                                data-children="{{ $booking->children }}"
                                                data-rooms-count="{{ $booking->rooms_count }}"
                                                data-status="{{ $pendingStatusLabel }}"
                                                data-verification="{{ e($pendingVerificationLabel) }}"
                                                data-booked-at="{{ $pendingCreatedAtLabel }}"
                                            >
                                                View Details
                                            </button>
                                            @php
                                                $idDocuments = collect($booking->id_document_paths ?? [])->filter()->values();
                                                if ($idDocuments->isEmpty() && !empty($booking->id_document_path)) {
                                                    $idDocuments = collect([$booking->id_document_path]);
                                                }
                                            @endphp
                                            @if ($idDocuments->count() <= 1)
                                                <button class="btn btn-outline btn-sm js-preview-id" type="button" data-preview-url="{{ route('admin.bookings.id-document', ['booking' => $booking, 'file' => 0, 'preview' => 1]) }}">View ID</button>
                                            @else
                                                @foreach ($idDocuments as $index => $idDocumentPath)
                                                    <button class="btn btn-outline btn-sm js-preview-id" type="button" data-preview-url="{{ route('admin.bookings.id-document', ['booking' => $booking, 'file' => $index, 'preview' => 1]) }}">View ID {{ $index + 1 }}</button>
                                                @endforeach
                                            @endif
                                            @if (empty($booking->verified_at))
                                                <form method="POST" action="{{ route('admin.bookings.verify', $booking) }}" onsubmit="return confirm('Verify customer ID for this booking?');">
                                                    @csrf
                                                    <button class="btn btn-outline btn-sm" type="submit">Verify ID</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" onsubmit="return confirm('Confirm this booking now?');">
                                                @csrf
                                                <button class="btn btn-primary btn-sm" type="submit" {{ empty($booking->verified_at) ? 'disabled' : '' }}>Confirm</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                @csrf
                                                <button class="btn btn-outline admin-cancel btn-sm" type="submit">Cancel</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="calendar-empty">No pending bookings.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-overlay is-hidden" id="checkoutQueueModal">
                    <div class="modal-content modal-queue" style="width:min(860px, 96vw);">
                        <div class="modal-header">
                            <div>
                                <h3><i class="fas fa-bell"></i> Checkout Release Queue</h3>
                                <p>Release room inventory for customers who are due to check out today.</p>
                            </div>
                            <button class="modal-close" id="closeCheckoutQueue" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="queue-list">
                                @forelse ($checkoutQueue as $checkoutBooking)
                                    <div class="checkout-release-row">
                                        <div>
                                            <h5>{{ $checkoutBooking->user->name }} · {{ $checkoutBooking->room->name }}</h5>
                                            <div class="checkout-release-meta">
                                                Ref #{{ $checkoutBooking->id }} · Check-out: {{ \Carbon\Carbon::parse($checkoutBooking->check_out_date)->format('M d, Y') }} {{ \Carbon\Carbon::parse($checkoutBooking->check_out_time)->format('h:i A') }}
                                            </div>
                                            @if (!empty($checkoutBooking->checkout_release_available_at))
                                                <div class="checkout-release-meta">
                                                    Scheduled availability: {{ \Carbon\Carbon::parse($checkoutBooking->checkout_release_available_at)->format('M d, Y h:i A') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="checkout-release-actions">
                                            <form method="POST" action="{{ route('admin.bookings.checkout-release-now', $checkoutBooking) }}" onsubmit="return confirm('Are you sure you want to mark this room available now?');">
                                                @csrf
                                                <button class="btn btn-primary btn-sm" type="submit">Yes, Available Now</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.bookings.checkout-release-schedule', $checkoutBooking) }}" onsubmit="return confirm('Are you sure you want to set a release schedule for this room?');">
                                                @csrf
                                                <input
                                                    type="datetime-local"
                                                    name="checkout_release_available_at"
                                                    required
                                                    value="{{ old('checkout_release_available_at', now()->format('Y-m-d\\TH:i')) }}"
                                                    min="{{ now()->format('Y-m-d\\TH:i') }}"
                                                >
                                                <button class="btn btn-outline btn-sm" type="submit">Set Schedule</button>
                                            </form>
                                            @if ($checkoutBooking->status === 'confirmed')
                                                <form method="POST" action="{{ route('admin.bookings.cancel', $checkoutBooking) }}" onsubmit="return confirm('Are you sure you want to cancel this confirmed booking?');">
                                                    @csrf
                                                    <button class="btn btn-outline admin-cancel btn-sm" type="submit">Cancel Booking</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="calendar-empty">No checkout releases pending right now.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-overlay is-hidden" id="bookingDetailsModal">
                    <div class="modal-content" style="width:min(760px, 96vw);">
                        <div class="modal-header">
                            <div>
                                <h3><i class="fas fa-receipt"></i> Booking Details</h3>
                                <p>Review full customer information before confirm/cancel.</p>
                            </div>
                            <button class="modal-close" id="closeBookingDetailsModal" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="queue-list">
                                <div class="queue-item" style="display:block;">
                                    <div class="queue-meta"><strong>Guest:</strong> <span id="detailsGuestName">-</span></div>
                                    <div class="queue-meta"><strong>Email:</strong> <span id="detailsGuestEmail">-</span></div>
                                    <div class="queue-meta"><strong>Room:</strong> <span id="detailsRoomName">-</span></div>
                                    <div class="queue-meta"><strong>Check-in:</strong> <span id="detailsCheckIn">-</span></div>
                                    <div class="queue-meta"><strong>Check-out:</strong> <span id="detailsCheckOut">-</span></div>
                                    <div class="queue-meta"><strong>Guests:</strong> <span id="detailsGuests">-</span></div>
                                    <div class="queue-meta"><strong>Rooms:</strong> <span id="detailsRooms">-</span></div>
                                    <div class="queue-meta"><strong>Status:</strong> <span id="detailsStatus">-</span></div>
                                    <div class="queue-meta"><strong>Verification:</strong> <span id="detailsVerification">-</span></div>
                                    <div class="queue-meta"><strong>Booked at:</strong> <span id="detailsBookedAt">-</span></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline" id="closeBookingDetailsFooter">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-overlay is-hidden" id="idPreviewModal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h3><i class="fas fa-id-card"></i> ID Document Preview</h3>
                                <p>Review the uploaded customer ID before verification.</p>
                            </div>
                            <button class="modal-close" id="closeIdPreviewModal" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body" style="padding-top: 0;">
                            <div class="id-preview-toolbar">
                                <div class="id-preview-actions">
                                    <button class="id-preview-action" type="button" id="idPreviewZoomOut">
                                        <i class="fas fa-search-minus"></i> Zoom out
                                    </button>
                                    <div class="id-preview-zoom" id="idPreviewZoomValue">100%</div>
                                    <button class="id-preview-action" type="button" id="idPreviewZoomIn">
                                        <i class="fas fa-search-plus"></i> Zoom in
                                    </button>
                                    <button class="id-preview-action" type="button" id="idPreviewZoomFit">
                                        <i class="fas fa-expand"></i> Fit
                                    </button>
                                    <a class="id-preview-action" id="idPreviewOpenLink" href="#" target="_blank" rel="noopener">
                                        <i class="fas fa-external-link-alt"></i> Open full
                                    </a>
                                </div>
                            </div>
                            <div class="id-preview-stage" id="idPreviewStage">
                                <img id="idPreviewImage" class="id-preview-image" alt="ID preview" draggable="false">
                                <iframe id="idPreviewFrame" title="ID Document Preview" style="width:100%; height:100%; border:0; border-radius:10px; background:#fff; display:none;"></iframe>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline" id="closeIdPreviewFooter">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/test.js') }}"></script>
    <script>
        // Month navigation
        const monthBlocks = Array.from(document.querySelectorAll('.admin-month-block'));
        const monthTitle = document.getElementById('adminMonthTitle');
        const monthPrevBtn = document.getElementById('adminMonthPrev');
        const monthNextBtn = document.getElementById('adminMonthNext');
        let activeMonthIndex = monthBlocks.findIndex((block) => block.classList.contains('is-active'));

        if (activeMonthIndex < 0 && monthBlocks.length > 0) {
            activeMonthIndex = 0;
            monthBlocks[0].classList.add('is-active');
        }

        const renderAdminMonth = () => {
            monthBlocks.forEach((block, index) => {
                block.classList.toggle('is-active', index === activeMonthIndex);
            });

            const activeBlock = monthBlocks[activeMonthIndex];
            if (monthTitle && activeBlock) {
                monthTitle.textContent = activeBlock.dataset.monthLabel || '';
            }

            if (monthPrevBtn) {
                monthPrevBtn.disabled = activeMonthIndex <= 0;
            }

            if (monthNextBtn) {
                monthNextBtn.disabled = activeMonthIndex >= monthBlocks.length - 1;
            }
        };

        monthPrevBtn?.addEventListener('click', () => {
            if (activeMonthIndex > 0) {
                activeMonthIndex -= 1;
                renderAdminMonth();
            }
        });

        monthNextBtn?.addEventListener('click', () => {
            if (activeMonthIndex < monthBlocks.length - 1) {
                activeMonthIndex += 1;
                renderAdminMonth();
            }
        });

        renderAdminMonth();

        // Inventory date selection
        const dateCards = document.querySelectorAll('.calendar-day');
        let selectedInventoryDate = '{{ $inventoryDate }}';

        const formatInventoryDateLabel = (dateValue) => {
            return new Date(dateValue + 'T00:00:00').toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
        };

        dateCards.forEach((card) => {
            card.addEventListener('click', (event) => {
                if (event.target.closest('.calendar-events') || event.target.closest('.calendar-day-footer')) {
                    return;
                }

                selectedInventoryDate = card.dataset.inventoryDate;
                if (!selectedInventoryDate || selectedInventoryDate < todayDateKey) {
                    return;
                }
                openInventoryModalForDate(selectedInventoryDate);
            });
        });

        // Inventory modal
        const inventoryModal = document.getElementById('inventoryModal');
        const modalInventoryDateText = document.getElementById('modalInventoryDateText');
        const closeInventoryBtn = document.getElementById('closeInventoryModal');
        const cancelInventoryBtn = document.getElementById('cancelInventory');
        const prefillDefaultInventoryBtn = document.getElementById('prefillDefaultInventory');
        const quickClearInventoryBtn = document.getElementById('quickClearInventory');
        const modalInventoryDate = document.getElementById('modalInventoryDate');
        const floorSelector = document.getElementById('floorSelector');
        const roomInputs = document.querySelectorAll('.room-input');
        const roomCurrentSetLabels = document.querySelectorAll('[data-room-current]');
        const inventoryForm = document.getElementById('inventoryForm');
        const inventorySuccessModal = document.getElementById('inventorySuccessModal');
        const inventorySuccessMessage = document.getElementById('inventorySuccessMessage');
        const closeInventorySuccessModal = document.getElementById('closeInventorySuccessModal');
        const inventorySuccessOk = document.getElementById('inventorySuccessOk');
        const inventoryAdditiveToggle = document.getElementById('inventoryAdditiveToggle');
        const modalInventoryEndDate = document.getElementById('modalInventoryEndDate');
        const dayDetailsModal = document.getElementById('dayDetailsModal');
        const closeDayDetailsModal = document.getElementById('closeDayDetailsModal');
        const dayDetailsDate = document.getElementById('dayDetailsDate');
        const dayDetailsSummary = document.getElementById('dayDetailsSummary');
        const dayAvailabilityList = document.getElementById('dayAvailabilityList');
        const dayBookingsList = document.getElementById('dayBookingsList');

        // Inventory data from server
        const inventoryData = @json($inventory);
        const floorsData = @json($floors);
        const roomsData = @json($inventoryRooms);
        const roomPerFloorLimits = @json($roomPerFloorLimits);
        const floorTotalLimit = @json($floorTotalLimit);
        const bookableFloorIds = floorsData.map((floor) => Number(floor.id));
        const todayDateKey = @json(\Carbon\Carbon::today()->toDateString());
        if (!selectedInventoryDate || selectedInventoryDate < todayDateKey) {
            selectedInventoryDate = todayDateKey;
        }

        const maxInventoryRangeDays = 90;
        const getInventoryDateKey = () => modalInventoryDate?.value || selectedInventoryDate;
        const getInventoryRange = () => {
            const start = modalInventoryDate?.value || selectedInventoryDate;
            const end = modalInventoryEndDate?.value || start;
            return { start, end };
        };

        const resolveTargetFloors = (floorRange) => {
            if (floorRange === 'all') {
                return [...bookableFloorIds];
            }
            const parsedFloorId = parseInt(floorRange, 10);
            if (!Number.isFinite(parsedFloorId)) {
                return [];
            }
            return bookableFloorIds.includes(parsedFloorId) ? [parsedFloorId] : [];
        };

        const refreshCurrentRoomSetLabels = (targetFloors) => {
            const currentDateKey = getInventoryDateKey();
            roomCurrentSetLabels.forEach((label) => {
                const roomId = Number(label.getAttribute('data-room-current'));
                let total = 0;
                let floorsWithData = 0;

                targetFloors.forEach((floorId) => {
                    const key = `${currentDateKey}|${floorId}-${roomId}`;
                    const availableRooms = Number(inventoryData?.[key]?.available_rooms ?? 0);
                    total += availableRooms;
                    if (availableRooms > 0) {
                        floorsWithData += 1;
                    }
                });

                label.textContent = `Currently set: ${total} room(s) across ${floorsWithData} floor(s)`;
            });
        };

        const syncInventoryRangeLabel = () => {
            if (!modalInventoryDateText) return;
            const { start, end } = getInventoryRange();
            if (!start) {
                modalInventoryDateText.textContent = 'Select a date range to configure availability.';
                return;
            }
            if (!end || start === end) {
                modalInventoryDateText.textContent = `Selected date: ${formatInventoryDateLabel(start)}`;
                return;
            }
            modalInventoryDateText.textContent = `Selected range: ${formatInventoryDateLabel(start)} to ${formatInventoryDateLabel(end)}`;
        };

        const clampInventoryRange = () => {
            if (!modalInventoryDate || !modalInventoryEndDate) return;
            const startValue = modalInventoryDate.value;
            if (!startValue) return;
            const startDate = new Date(startValue + 'T00:00:00');
            let endValue = modalInventoryEndDate.value || startValue;
            let endDate = new Date(endValue + 'T00:00:00');

            if (Number.isNaN(startDate.getTime())) return;

            if (Number.isNaN(endDate.getTime()) || endDate < startDate) {
                endDate = new Date(startDate);
                endValue = startValue;
            }

            const diffDays = Math.floor((endDate - startDate) / 86400000);
            if (diffDays > maxInventoryRangeDays) {
                const capped = new Date(startDate);
                capped.setDate(capped.getDate() + maxInventoryRangeDays);
                endDate = capped;
                endValue = capped.toISOString().slice(0, 10);
            }

            modalInventoryEndDate.value = endValue;
        };

        const getRoomLimit = (roomId) => {
            const configuredLimit = Number(roomPerFloorLimits?.[roomId]);
            if (Number.isFinite(configuredLimit) && configuredLimit >= 0) {
                return configuredLimit;
            }
            return floorTotalLimit;
        };

        const getExistingRoomCount = (floorId, roomId) => {
            const dateKey = getInventoryDateKey();
            const key = `${dateKey}|${floorId}-${roomId}`;
            return Number(inventoryData?.[key]?.available_rooms ?? 0);
        };

        const getRemainingCaps = (targetFloors) => {
            let maxFloorIncrement = Infinity;
            const maxRoomIncrements = {};

            roomsData.forEach((room) => {
                maxRoomIncrements[room.id] = Infinity;
            });

            targetFloors.forEach((floorId) => {
                let existingTotal = 0;
                roomsData.forEach((room) => {
                    const existing = getExistingRoomCount(floorId, room.id);
                    existingTotal += existing;

                    const roomLimit = getRoomLimit(Number(room.id));
                    const remaining = Math.max(roomLimit - existing, 0);
                    maxRoomIncrements[room.id] = Math.min(maxRoomIncrements[room.id], remaining);
                });

                const floorRemaining = Math.max(floorTotalLimit - existingTotal, 0);
                maxFloorIncrement = Math.min(maxFloorIncrement, floorRemaining);
            });

            if (!Number.isFinite(maxFloorIncrement)) {
                maxFloorIncrement = floorTotalLimit;
            }

            roomsData.forEach((room) => {
                if (!Number.isFinite(maxRoomIncrements[room.id])) {
                    maxRoomIncrements[room.id] = getRoomLimit(Number(room.id));
                }
            });

            return { maxRoomIncrements, maxFloorIncrement };
        };

        const getDistributionCaps = (targetFloors) => {
            if (!inventoryAdditiveToggle?.checked) {
                return { maxRoomIncrements: null, maxFloorIncrement: floorTotalLimit };
            }
            return getRemainingCaps(targetFloors);
        };

        const getCurrentDistributionTotal = () => {
            let total = 0;
            roomInputs.forEach((input) => {
                total += Number.parseInt(input.value || '0', 10) || 0;
            });
            return total;
        };

        const clampRoomInputsToLimits = (targetFloors) => {
            const caps = getDistributionCaps(targetFloors);
            let runningTotal = 0;
            roomInputs.forEach((input) => {
                const roomId = Number(input.getAttribute('data-room-id'));
                const roomLimit = inventoryAdditiveToggle?.checked
                    ? (caps.maxRoomIncrements?.[roomId] ?? getRoomLimit(roomId))
                    : getRoomLimit(roomId);
                const currentValue = Number.parseInt(input.value || '0', 10) || 0;
                const clampedRoomValue = Math.min(Math.max(currentValue, 0), roomLimit);
                const remaining = Math.max(caps.maxFloorIncrement - runningTotal, 0);
                const finalValue = Math.min(clampedRoomValue, remaining);
                input.value = String(finalValue);
                runningTotal += finalValue;
            });
        };

        function openInventoryModalForDate(dateValue) {
            if (!dateValue || dateValue < todayDateKey) {
                alert('You can only set room availability for today or future dates.');
                return;
            }
            modalInventoryDate.value = dateValue;
            if (modalInventoryEndDate) {
                modalInventoryEndDate.value = dateValue;
            }
            syncInventoryRangeLabel();
            if (floorSelector) {
                floorSelector.value = floorSelector.value || 'all';
            }
            loadInventoryForFloors(floorSelector?.value || 'all');
            openModal(inventoryModal);
        }

        syncInventoryRangeLabel();

        modalInventoryDate?.addEventListener('change', () => {
            clampInventoryRange();
            syncInventoryRangeLabel();
            loadInventoryForFloors(floorSelector?.value || 'all');
        });

        modalInventoryEndDate?.addEventListener('change', () => {
            clampInventoryRange();
            syncInventoryRangeLabel();
        });

        closeInventoryBtn?.addEventListener('click', () => {
            closeModal(inventoryModal);
        });

        cancelInventoryBtn?.addEventListener('click', () => {
            closeModal(inventoryModal);
        });

        // Floor selector change
        floorSelector?.addEventListener('change', (e) => {
            loadInventoryForFloors(e.target.value);
        });

        function loadInventoryForFloors(floorRange) {
            const targetFloors = resolveTargetFloors(floorRange);
            if (targetFloors.length === 0) {
                roomInputs.forEach((input) => {
                    input.value = '0';
                });
                refreshCurrentRoomSetLabels([]);
                return;
            }

            if (inventoryAdditiveToggle?.checked) {
                roomInputs.forEach((input) => {
                    input.value = '0';
                });
                clampRoomInputsToLimits(targetFloors);
                refreshCurrentRoomSetLabels(targetFloors);
                return;
            }

            // Calculate average or total across selected floors for each room
            roomsData.forEach(room => {
                let total = 0;
                let count = 0;
                
                targetFloors.forEach(floorId => {
                    const key = `${modalInventoryDate.value}|${floorId}-${room.id}`;
                    if (inventoryData[key]) {
                        total += parseInt(inventoryData[key].available_rooms || 0);
                        count++;
                    }
                });

                const avg = count > 0 ? Math.round(total / count) : 0;
                const input = document.querySelector(`.room-input[data-room-id="${room.id}"]`);
                if (input) {
                    const roomLimit = getRoomLimit(Number(room.id));
                    input.value = String(Math.min(avg, roomLimit));
                }
            });

            clampRoomInputsToLimits(targetFloors);
            refreshCurrentRoomSetLabels(targetFloors);
        }

        inventoryAdditiveToggle?.addEventListener('change', () => {
            loadInventoryForFloors(floorSelector?.value || 'all');
        });

        // Plus/Minus buttons
        document.querySelectorAll('.room-btn-plus').forEach(btn => {
            btn.addEventListener('click', () => {
                const roomId = btn.dataset.roomId;
                const input = document.querySelector(`.room-input[data-room-id="${roomId}"]`);
                if (input) {
                    const currentValue = parseInt(input.value || 0, 10);
                    const selectedFloors = resolveTargetFloors(floorSelector?.value || 'all');
                    const caps = getDistributionCaps(selectedFloors);
                    const roomLimit = inventoryAdditiveToggle?.checked
                        ? (caps.maxRoomIncrements?.[Number(roomId)] ?? getRoomLimit(Number(roomId)))
                        : getRoomLimit(Number(roomId));
                    const currentTotal = getCurrentDistributionTotal();
                    const remainingForFloor = Math.max(caps.maxFloorIncrement - (currentTotal - currentValue), 0);
                    const nextValue = Math.min(currentValue + 1, roomLimit, remainingForFloor);
                    input.value = String(nextValue);
                }
                const selectedFloors = resolveTargetFloors(floorSelector?.value || 'all');
                refreshCurrentRoomSetLabels(selectedFloors);
            });
        });

        document.querySelectorAll('.room-btn-minus').forEach(btn => {
            btn.addEventListener('click', () => {
                const roomId = btn.dataset.roomId;
                const input = document.querySelector(`.room-input[data-room-id="${roomId}"]`);
                if (input) {
                    const val = parseInt(input.value || 0);
                    input.value = Math.max(0, val - 1);
                }
                const selectedFloors = resolveTargetFloors(floorSelector?.value || 'all');
                refreshCurrentRoomSetLabels(selectedFloors);
            });
        });

        prefillDefaultInventoryBtn?.addEventListener('click', () => {
            const selectedFloors = resolveTargetFloors(floorSelector?.value || 'all');
            const caps = getDistributionCaps(selectedFloors);
            roomInputs.forEach((input) => {
                const roomId = Number(input.getAttribute('data-room-id'));
                const roomLimit = inventoryAdditiveToggle?.checked
                    ? (caps.maxRoomIncrements?.[roomId] ?? getRoomLimit(roomId))
                    : getRoomLimit(roomId);
                input.value = String(roomLimit);
            });
            clampRoomInputsToLimits(selectedFloors);
            refreshCurrentRoomSetLabels(selectedFloors);
        });

        quickClearInventoryBtn?.addEventListener('click', () => {
            roomInputs.forEach((input) => {
                input.value = '0';
            });
            const selectedFloors = resolveTargetFloors(floorSelector?.value || 'all');
            refreshCurrentRoomSetLabels(selectedFloors);
        });

        // Form submission - convert to proper format
        inventoryForm?.addEventListener('submit', (e) => {
            e.preventDefault();

            if (!window.confirm('Are you sure you want to apply these inventory changes?')) {
                return;
            }

            if (!modalInventoryDate.value || modalInventoryDate.value < todayDateKey) {
                alert('You can only set room availability for today or future dates.');
                return;
            }

            const range = getInventoryRange();
            const startDate = new Date(range.start + 'T00:00:00');
            const endDate = new Date(range.end + 'T00:00:00');
            if (Number.isNaN(startDate.getTime()) || Number.isNaN(endDate.getTime())) {
                alert('Please choose a valid start and end date.');
                return;
            }
            if (endDate < startDate) {
                alert('End date must be the same as or after the start date.');
                return;
            }
            const diffDays = Math.floor((endDate - startDate) / 86400000);
            if (diffDays > maxInventoryRangeDays) {
                alert(`Date range cannot exceed ${maxInventoryRangeDays} days.`);
                return;
            }
            
            const formData = new FormData(inventoryForm);
            const floorRange = floorSelector.value;
            const targetFloors = resolveTargetFloors(floorRange);
            const isAdditiveMode = Boolean(inventoryAdditiveToggle?.checked);
            const inventoryDateKey = getInventoryDateKey();

            if (range.end) {
                formData.set('inventory_end_date', range.end);
            }

            if (targetFloors.length === 0) {
                alert('Please select floor 15, floor 16, or apply to both bookable floors.');
                return;
            }

            const roomInputValues = {};
            for (const room of roomsData) {
                const input = document.querySelector(`.room-input[data-room-id="${room.id}"]`);
                roomInputValues[room.id] = Number.parseInt(input?.value ?? '0', 10) || 0;
            }

            if (isAdditiveMode) {
                for (const floorId of targetFloors) {
                    let floorTotal = 0;
                    for (const room of roomsData) {
                        const roomLimit = getRoomLimit(Number(room.id));
                        const existing = Number(inventoryData?.[`${inventoryDateKey}|${floorId}-${room.id}`]?.available_rooms ?? 0);
                        const incoming = roomInputValues[room.id] ?? 0;
                        const updated = existing + incoming;
                        if (updated > roomLimit) {
                            alert(`Per-floor limit exceeded for ${room.name}. Existing ${existing}, adding ${incoming}, max ${roomLimit}.`);
                            return;
                        }
                        floorTotal += updated;
                    }
                    if (floorTotal > floorTotalLimit) {
                        alert(`Total rooms per floor cannot exceed ${floorTotalLimit}.`);
                        return;
                    }
                }
            } else {
                let distributionTotal = 0;
                for (const room of roomsData) {
                    const value = roomInputValues[room.id] ?? 0;
                    const roomLimit = getRoomLimit(Number(room.id));
                    if (value > roomLimit) {
                        alert(`Per-floor limit exceeded for ${room.name}. Max is ${roomLimit}.`);
                        return;
                    }
                    distributionTotal += value;
                }

                if (distributionTotal > floorTotalLimit) {
                    alert(`Total rooms per floor cannot exceed ${floorTotalLimit}.`);
                    return;
                }
            }

            // Build inventory array
            targetFloors.forEach(floorId => {
                roomsData.forEach(room => {
                    const value = roomInputValues[room.id] ?? 0;
                    formData.append(`inventory[${floorId}][${room.id}]`, value);
                });
            });

            if (isAdditiveMode) {
                formData.set('inventory_additive', '1');
            }

            // Submit form
            fetch(inventoryForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(async (response) => {
                if (!response.ok) {
                    let errorMessage = 'Inventory update request failed.';
                    try {
                        const errorPayload = await response.json();
                        if (errorPayload?.message) {
                            errorMessage = errorPayload.message;
                        } else if (errorPayload?.errors) {
                            const firstKey = Object.keys(errorPayload.errors)[0];
                            const firstError = firstKey ? errorPayload.errors[firstKey]?.[0] : null;
                            if (firstError) {
                                errorMessage = firstError;
                            }
                        }
                    } catch (_error) {
                        // Keep generic message when payload cannot be parsed.
                    }
                    throw new Error(errorMessage);
                }

                const payload = await response.json();

                // Update in-memory inventory map so labels stay accurate without full page reload.
                targetFloors.forEach((floorId) => {
                    roomsData.forEach((room) => {
                        const value = roomInputValues[room.id] ?? 0;
                        const key = `${inventoryDateKey}|${floorId}-${room.id}`;
                        const existing = Number(inventoryData?.[key]?.available_rooms ?? 0);
                        const updatedValue = isAdditiveMode ? existing + value : value;
                        inventoryData[key] = {
                            floor_id: floorId,
                            room_id: room.id,
                            inventory_date: inventoryDateKey,
                            available_rooms: updatedValue,
                        };
                    });
                });

                loadInventoryForFloors(floorRange);
                closeModal(inventoryModal);
                if (inventorySuccessMessage) {
                    inventorySuccessMessage.textContent = payload?.message || 'Room availability has been saved.';
                }
                openModal(inventorySuccessModal);
            }).catch(error => {
                console.error('Error:', error);
                alert(error?.message || 'Error saving inventory. Please try again.');
            });
        });

        // Pending queue modal
        const pendingModal = document.getElementById('pendingQueueModal');
        const openPendingBtn = document.getElementById('openPendingQueue');
        const closePendingBtn = document.getElementById('closePendingQueue');
        const checkoutQueueModal = document.getElementById('checkoutQueueModal');
        const openCheckoutBtn = document.getElementById('openCheckoutQueue');
        const closeCheckoutBtn = document.getElementById('closeCheckoutQueue');
        const enlargeCheckoutQueueBtn = document.getElementById('enlargeCheckoutQueue');
        const collapseCheckoutQueueBtn = document.getElementById('collapseCheckoutQueue');
        const checkoutQueueWidget = document.getElementById('checkoutQueueWidget');
        const bookingDetailPanel = document.getElementById('bookingDetailPanel');
        const bookingDetailsModal = document.getElementById('bookingDetailsModal');
        const closeBookingDetailsModal = document.getElementById('closeBookingDetailsModal');
        const closeBookingDetailsFooter = document.getElementById('closeBookingDetailsFooter');
        const detailsGuestName = document.getElementById('detailsGuestName');
        const detailsGuestEmail = document.getElementById('detailsGuestEmail');
        const detailsRoomName = document.getElementById('detailsRoomName');
        const detailsCheckIn = document.getElementById('detailsCheckIn');
        const detailsCheckOut = document.getElementById('detailsCheckOut');
        const detailsGuests = document.getElementById('detailsGuests');
        const detailsRooms = document.getElementById('detailsRooms');
        const detailsStatus = document.getElementById('detailsStatus');
        const detailsVerification = document.getElementById('detailsVerification');
        const detailsBookedAt = document.getElementById('detailsBookedAt');
        const idPreviewModal = document.getElementById('idPreviewModal');
        const idPreviewFrame = document.getElementById('idPreviewFrame');
        const idPreviewImage = document.getElementById('idPreviewImage');
        const idPreviewStage = document.getElementById('idPreviewStage');
        const idPreviewZoomOut = document.getElementById('idPreviewZoomOut');
        const idPreviewZoomIn = document.getElementById('idPreviewZoomIn');
        const idPreviewZoomFit = document.getElementById('idPreviewZoomFit');
        const idPreviewZoomValue = document.getElementById('idPreviewZoomValue');
        const idPreviewOpenLink = document.getElementById('idPreviewOpenLink');
        const closeIdPreviewModal = document.getElementById('closeIdPreviewModal');
        const closeIdPreviewFooter = document.getElementById('closeIdPreviewFooter');
        const roomSettingsToggles = document.querySelectorAll('.room-settings-toggle input[type="checkbox"]');
        const managedModals = [inventoryModal, inventorySuccessModal, pendingModal, checkoutQueueModal, bookingDetailsModal, idPreviewModal, dayDetailsModal].filter(Boolean);

        // Keep overlays at body level to avoid fixed-position issues from transformed ancestors.
        managedModals.forEach((modal) => {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        });

        const syncModalScrollLock = () => {
            const hasOpenModal = managedModals.some((modal) => !modal.classList.contains('is-hidden'));
            document.body.classList.toggle('admin-modal-open', hasOpenModal);
        };

        const openModal = (modal) => {
            if (!modal) return;
            modal.classList.remove('is-hidden');
            syncModalScrollLock();
        };

        const closeModal = (modal) => {
            if (!modal) return;
            modal.classList.add('is-hidden');
            syncModalScrollLock();
        };

        syncModalScrollLock();

        if (openPendingBtn) {
            openPendingBtn.addEventListener('click', () => {
                openModal(pendingModal);
            });
        }

        closeDayDetailsModal?.addEventListener('click', () => {
            closeModal(dayDetailsModal);
        });

        const escapeHtml = (value) => {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        const renderDayDetails = (button) => {
            if (!button || !dayDetailsModal) return;

            const dateKey = button.dataset.date || '';
            const statusCounts = button.dataset.statusCounts ? JSON.parse(button.dataset.statusCounts) : {};
            const bookings = button.dataset.bookings ? JSON.parse(button.dataset.bookings) : [];

            if (dateKey && dateKey < todayDateKey) {
                return;
            }

            if (dayDetailsDate) {
                dayDetailsDate.textContent = dateKey ? formatInventoryDateLabel(dateKey) : 'Selected date';
            }

            const pendingCount = (statusCounts.pending || 0) + (statusCounts.pending_verification || 0);
            const confirmedCount = (statusCounts.confirmed || 0);
            const checkedInCount = (statusCounts.checked_in || 0) + (statusCounts.checkout_scheduled || 0);
            const cancelledCount = (statusCounts.cancelled || 0);

            let totalAvailableAll = 0;
            let availabilityMarkup = '';
            roomsData.forEach((room) => {
                let total = 0;
                floorsData.forEach((floor) => {
                    const key = `${dateKey}|${floor.id}-${room.id}`;
                    total += Number(inventoryData?.[key]?.available_rooms ?? 0);
                });
                totalAvailableAll += total;
                availabilityMarkup += `
                    <div class="day-details-item">
                        <strong>${escapeHtml(room.name)}</strong>
                        <span>${total} room(s) available</span>
                    </div>
                `;
            });

            if (dayAvailabilityList) {
                dayAvailabilityList.innerHTML = availabilityMarkup || '<div class="day-details-item">No availability data set.</div>';
            }

            const summaryItems = [
                { label: 'Available', count: totalAvailableAll, dot: 'legend-dot', color: 'status-dot-available' },
                { label: 'Pending', count: pendingCount, dot: 'legend-dot', color: 'status-dot-pending' },
                { label: 'Confirmed', count: confirmedCount, dot: 'legend-dot', color: 'status-dot-confirmed' },
                { label: 'Checked in', count: checkedInCount, dot: 'legend-dot', color: 'status-dot-checked_in' },
                { label: 'Cancelled', count: cancelledCount, dot: 'legend-dot', color: 'status-dot-cancelled' },
            ];

            if (dayDetailsSummary) {
                dayDetailsSummary.innerHTML = summaryItems.map((item) => {
                    const countLabel = `: ${item.count}`;
                    return `
                        <div class="day-details-badge">
                            <span class="status-dot ${item.color}"></span>
                            <strong>${item.label}${countLabel}</strong>
                        </div>
                    `;
                }).join('');
            }

            if (dayBookingsList) {
                if (!bookings.length) {
                    dayBookingsList.innerHTML = '<div class="day-details-item">No bookings for this date.</div>';
                } else {
                    dayBookingsList.innerHTML = bookings.map((booking) => {
                        const statusLabel = (booking.status || '').replace(/_/g, ' ');
                        return `
                            <div class="day-details-item">
                                <strong>#${escapeHtml(booking.id)} · ${escapeHtml(booking.room)}</strong>
                                <span>${escapeHtml(booking.guest)} · ${escapeHtml(booking.check_in_time)} - ${escapeHtml(booking.check_out_time)}</span>
                                <span>Status: ${escapeHtml(statusLabel)} · Rooms: ${escapeHtml(booking.rooms_count)}</span>
                            </div>
                        `;
                    }).join('');
                }
            }

            openModal(dayDetailsModal);
        };

        document.querySelectorAll('.js-day-details').forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.stopPropagation();
                renderDayDetails(btn);
            });
        });

        if (closePendingBtn) {
            closePendingBtn.addEventListener('click', () => {
                closeModal(pendingModal);
            });
        }

        openCheckoutBtn?.addEventListener('click', () => {
            openModal(checkoutQueueModal);
        });

        closeCheckoutBtn?.addEventListener('click', () => {
            closeModal(checkoutQueueModal);
        });

        enlargeCheckoutQueueBtn?.addEventListener('click', () => {
            checkoutQueueWidget?.classList.remove('is-collapsed');
            openModal(checkoutQueueModal);
        });

        collapseCheckoutQueueBtn?.addEventListener('click', () => {
            checkoutQueueWidget?.classList.toggle('is-collapsed');
            if (checkoutQueueWidget?.classList.contains('is-collapsed')) {
                collapseCheckoutQueueBtn.textContent = 'Open';
            } else {
                collapseCheckoutQueueBtn.textContent = 'Close';
            }
        });

        roomSettingsToggles.forEach((toggle) => {
            const labelText = toggle.closest('.room-settings-toggle')?.querySelector('span');
            const syncLabel = () => {
                if (labelText) {
                    labelText.textContent = toggle.checked ? 'Enabled' : 'Disabled';
                }
            };
            syncLabel();
            toggle.addEventListener('change', syncLabel);
        });

        const closeDetailsModal = () => {
            closeModal(bookingDetailsModal);
        };

        const openBookingDetailsFromButton = (button) => {
            if (!button || !bookingDetailsModal) {
                return;
            }

            detailsGuestName.textContent = button.dataset.guestName || '-';
            detailsGuestEmail.textContent = button.dataset.guestEmail || '-';
            detailsRoomName.textContent = button.dataset.roomName || '-';
            detailsCheckIn.textContent = `${button.dataset.checkInDate || '-'} ${button.dataset.checkInTime || ''}`.trim();
            detailsCheckOut.textContent = `${button.dataset.checkOutDate || '-'} ${button.dataset.checkOutTime || ''}`.trim();
            detailsGuests.textContent = `${button.dataset.adults || '0'} adult(s), ${button.dataset.children || '0'} child(ren)`;
            detailsRooms.textContent = `${button.dataset.roomsCount || '0'} room(s)`;
            detailsStatus.textContent = button.dataset.status || '-';
            detailsVerification.textContent = button.dataset.verification || '-';
            detailsBookedAt.textContent = button.dataset.bookedAt || '-';

            openModal(bookingDetailsModal);
        };

        closeBookingDetailsModal?.addEventListener('click', closeDetailsModal);
        closeBookingDetailsFooter?.addEventListener('click', closeDetailsModal);

        const previewState = {
            zoom: 1,
            fitScale: 1,
            naturalWidth: 0,
            naturalHeight: 0,
            mode: 'image',
        };

        idPreviewImage?.addEventListener('dragstart', (event) => {
            event.preventDefault();
        });

        const updatePreviewZoomLabel = (label) => {
            if (!idPreviewZoomValue) return;
            idPreviewZoomValue.textContent = label ?? `${Math.round(previewState.zoom * 100)}%`;
        };

        const setPreviewMode = (mode) => {
            previewState.mode = mode;
            if (idPreviewImage) {
                idPreviewImage.style.display = mode === 'image' ? 'block' : 'none';
            }
            if (idPreviewFrame) {
                idPreviewFrame.style.display = mode === 'frame' ? 'block' : 'none';
            }
            const disabled = mode !== 'image';
            if (idPreviewZoomOut) idPreviewZoomOut.disabled = disabled;
            if (idPreviewZoomIn) idPreviewZoomIn.disabled = disabled;
            if (idPreviewZoomFit) idPreviewZoomFit.disabled = disabled;
            if (disabled) {
                updatePreviewZoomLabel('PDF');
            }
        };

        const applyPreviewZoom = (scale) => {
            if (!idPreviewImage || !previewState.naturalWidth || !previewState.naturalHeight) {
                return;
            }
            const clamped = Math.min(Math.max(scale, previewState.fitScale), 3);
            previewState.zoom = clamped;
            idPreviewImage.style.width = `${previewState.naturalWidth * clamped}px`;
            idPreviewImage.style.height = `${previewState.naturalHeight * clamped}px`;
            updatePreviewZoomLabel();
        };

        const applyPreviewZoomAtPoint = (nextZoom, clientX, clientY) => {
            if (!idPreviewStage || !idPreviewImage || previewState.mode !== 'image') {
                applyPreviewZoom(nextZoom);
                return;
            }
            if (!previewState.naturalWidth || !previewState.naturalHeight) {
                applyPreviewZoom(nextZoom);
                return;
            }

            const stageRect = idPreviewStage.getBoundingClientRect();
            const cursorX = clientX - stageRect.left;
            const cursorY = clientY - stageRect.top;
            const prevWidth = idPreviewImage.offsetWidth || idPreviewImage.getBoundingClientRect().width;
            const prevHeight = idPreviewImage.offsetHeight || idPreviewImage.getBoundingClientRect().height;
            const imageOffsetLeft = idPreviewImage.offsetLeft || 0;
            const imageOffsetTop = idPreviewImage.offsetTop || 0;
            const imageX = (idPreviewStage.scrollLeft + cursorX) - imageOffsetLeft;
            const imageY = (idPreviewStage.scrollTop + cursorY) - imageOffsetTop;
            const ratioX = prevWidth > 0 ? imageX / prevWidth : 0.5;
            const ratioY = prevHeight > 0 ? imageY / prevHeight : 0.5;
            const clampedRatioX = Math.min(Math.max(ratioX, 0), 1);
            const clampedRatioY = Math.min(Math.max(ratioY, 0), 1);

            applyPreviewZoom(nextZoom);

            requestAnimationFrame(() => {
                const newWidth = idPreviewImage.offsetWidth || idPreviewImage.getBoundingClientRect().width;
                const newHeight = idPreviewImage.offsetHeight || idPreviewImage.getBoundingClientRect().height;
                const newImageOffsetLeft = idPreviewImage.offsetLeft || 0;
                const newImageOffsetTop = idPreviewImage.offsetTop || 0;
                idPreviewStage.scrollLeft = newImageOffsetLeft + clampedRatioX * newWidth - cursorX;
                idPreviewStage.scrollTop = newImageOffsetTop + clampedRatioY * newHeight - cursorY;
            });
        };

        const fitPreviewToStage = () => {
            if (!idPreviewStage || !previewState.naturalWidth || !previewState.naturalHeight) {
                return;
            }
            const rect = idPreviewStage.getBoundingClientRect();
            const maxWidth = Math.max(rect.width - 24, 200);
            const maxHeight = Math.max(rect.height - 24, 200);
            const fitScale = Math.min(
                maxWidth / previewState.naturalWidth,
                maxHeight / previewState.naturalHeight,
                1
            );
            previewState.fitScale = fitScale;
            applyPreviewZoom(fitScale);
            idPreviewStage.scrollTop = 0;
            idPreviewStage.scrollLeft = 0;
        };

        const closePreviewModal = () => {
            closeModal(idPreviewModal);
            if (idPreviewFrame) {
                idPreviewFrame.removeAttribute('src');
            }
            if (idPreviewImage) {
                idPreviewImage.removeAttribute('src');
                idPreviewImage.style.width = '';
                idPreviewImage.style.height = '';
            }
            if (idPreviewStage) {
                idPreviewStage.classList.remove('is-dragging');
            }
            previewState.zoom = 1;
            previewState.fitScale = 1;
            previewState.naturalWidth = 0;
            previewState.naturalHeight = 0;
            updatePreviewZoomLabel('100%');
        };

        idPreviewZoomIn?.addEventListener('click', () => {
            applyPreviewZoom(previewState.zoom + 0.1);
        });

        idPreviewZoomOut?.addEventListener('click', () => {
            applyPreviewZoom(previewState.zoom - 0.1);
        });

        idPreviewZoomFit?.addEventListener('click', () => {
            fitPreviewToStage();
        });

        let isDraggingPreview = false;
        let dragStartX = 0;
        let dragStartY = 0;
        let dragScrollLeft = 0;
        let dragScrollTop = 0;

        const startPreviewDrag = (event) => {
            if (previewState.mode !== 'image' || !idPreviewStage) {
                return;
            }
            isDraggingPreview = true;
            idPreviewStage.classList.add('is-dragging');
            dragStartX = event.clientX;
            dragStartY = event.clientY;
            dragScrollLeft = idPreviewStage.scrollLeft;
            dragScrollTop = idPreviewStage.scrollTop;
        };

        const movePreviewDrag = (event) => {
            if (!isDraggingPreview || !idPreviewStage) {
                return;
            }
            const dx = event.clientX - dragStartX;
            const dy = event.clientY - dragStartY;
            idPreviewStage.scrollLeft = dragScrollLeft - dx;
            idPreviewStage.scrollTop = dragScrollTop - dy;
        };

        const endPreviewDrag = () => {
            if (!isDraggingPreview || !idPreviewStage) {
                return;
            }
            isDraggingPreview = false;
            idPreviewStage.classList.remove('is-dragging');
        };

        idPreviewStage?.addEventListener('mousedown', (event) => {
            if (event.button !== 0) {
                return;
            }
            startPreviewDrag(event);
        });

        idPreviewStage?.addEventListener('dragstart', (event) => {
            event.preventDefault();
        });

        window.addEventListener('mousemove', movePreviewDrag);
        window.addEventListener('mouseup', endPreviewDrag);
        idPreviewStage?.addEventListener('mouseleave', endPreviewDrag);

        idPreviewImage?.addEventListener('mousedown', (event) => {
            if (event.button === 0) {
                event.preventDefault();
            }
        });

        idPreviewStage?.addEventListener('dblclick', (event) => {
            if (previewState.mode !== 'image') {
                return;
            }
            if (event.shiftKey) {
                applyPreviewZoom(previewState.zoom - 0.2);
            } else {
                applyPreviewZoom(previewState.zoom + 0.2);
            }
        });

        idPreviewStage?.addEventListener('wheel', (event) => {
            if (previewState.mode !== 'image') {
                return;
            }
            event.preventDefault();
            const delta = Math.sign(event.deltaY);
            const step = 0.12;
            const nextZoom = previewState.zoom + (delta > 0 ? -step : step);
            applyPreviewZoomAtPoint(nextZoom, event.clientX, event.clientY);
        }, { passive: false });

        const openIdPreviewFromButton = (button) => {
            const previewUrl = button?.getAttribute('data-preview-url');
            if (!previewUrl || !idPreviewModal) {
                return;
            }

            const targetBookingId = button?.getAttribute('data-booking-id');
            const activeBookingId = bookingDetailPanel?.getAttribute('data-active-booking-id');
            const matchesActive = targetBookingId && activeBookingId && String(targetBookingId) === String(activeBookingId);

            const unlockVerification = (type) => {
                const scope = bookingDetailPanel || document;
                if (type === 'id') {
                    scope.querySelectorAll('[data-requires-id-view]').forEach((btn) => {
                        btn.removeAttribute('disabled');
                        btn.removeAttribute('title');
                    });
                    scope.querySelectorAll('[data-note="id"]').forEach((note) => note.remove());
                }
                if (type === 'payment') {
                    scope.querySelectorAll('[data-requires-payment-view]').forEach((btn) => {
                        btn.removeAttribute('disabled');
                        btn.removeAttribute('title');
                    });
                    scope.querySelectorAll('[data-note="payment"]').forEach((note) => note.remove());
                }
            };

            if (matchesActive) {
                if (previewUrl.includes('/id-document')) {
                    unlockVerification('id');
                } else if (previewUrl.includes('/payment-proof')) {
                    unlockVerification('payment');
                }
            }

            if (idPreviewOpenLink) {
                idPreviewOpenLink.setAttribute('href', previewUrl);
            }

            if (idPreviewFrame) {
                idPreviewFrame.removeAttribute('src');
            }

            if (idPreviewImage) {
                idPreviewImage.onerror = () => {
                    setPreviewMode('frame');
                    if (idPreviewFrame) {
                        idPreviewFrame.setAttribute('src', previewUrl);
                    }
                };
                idPreviewImage.onload = () => {
                    previewState.naturalWidth = idPreviewImage.naturalWidth || 0;
                    previewState.naturalHeight = idPreviewImage.naturalHeight || 0;
                    setPreviewMode('image');
                    fitPreviewToStage();
                };
                idPreviewImage.setAttribute('src', previewUrl);
            } else if (idPreviewFrame) {
                setPreviewMode('frame');
                idPreviewFrame.setAttribute('src', previewUrl);
            }

            openModal(idPreviewModal);
        };

        const setActiveBookingListItem = (bookingId) => {
            document.querySelectorAll('[data-booking-select]').forEach((link) => {
                const isActive = link.getAttribute('data-booking-id') === String(bookingId);
                link.classList.toggle('is-active', isActive);
            });
        };

        const syncSelectedBookingInUrl = (bookingId) => {
            try {
                const url = new URL(window.location.href);
                if (bookingId) {
                    url.searchParams.set('selected_booking', String(bookingId));
                } else {
                    url.searchParams.delete('selected_booking');
                }
                history.replaceState({}, '', url.toString());
            } catch (_error) {
                // No-op when URL APIs are unavailable.
            }
        };

        let isSwappingBookingDetail = false;

        const swapBookingDetail = async (bookingLink) => {
            if (!bookingLink || !bookingDetailPanel || isSwappingBookingDetail) {
                return;
            }

            const bookingId = bookingLink.getAttribute('data-booking-id');
            if (!bookingId) {
                return;
            }

            if (bookingDetailPanel.getAttribute('data-active-booking-id') === bookingId) {
                setActiveBookingListItem(bookingId);
                return;
            }

            const previousBookingId = bookingDetailPanel.getAttribute('data-active-booking-id') || '';
            setActiveBookingListItem(bookingId);
            isSwappingBookingDetail = true;
            bookingDetailPanel.classList.add('is-loading');

            try {
                const response = await fetch(bookingLink.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to load booking details.');
                }

                const html = await response.text();
                const parsedDoc = new DOMParser().parseFromString(html, 'text/html');
                const incomingPanel = parsedDoc.querySelector('#bookingDetailPanel');

                if (!incomingPanel) {
                    throw new Error('Could not find booking detail panel in response.');
                }

                bookingDetailPanel.innerHTML = incomingPanel.innerHTML;
                bookingDetailPanel.setAttribute('data-active-booking-id', bookingId);
                syncSelectedBookingInUrl(bookingId);
            } catch (error) {
                console.error('Error swapping booking detail:', error);
                if (previousBookingId) {
                    setActiveBookingListItem(previousBookingId);
                }
                alert(error?.message || 'Unable to load booking details. Please try again.');
            } finally {
                isSwappingBookingDetail = false;
                bookingDetailPanel.classList.remove('is-loading');
            }
        };

        document.addEventListener('click', (event) => {
            const bookingLink = event.target.closest('[data-booking-select]');
            if (bookingLink) {
                if (!event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey) {
                    event.preventDefault();
                    swapBookingDetail(bookingLink);
                }
                return;
            }

            const bookingDetailsTrigger = event.target.closest('.js-open-booking-details');
            if (bookingDetailsTrigger) {
                event.preventDefault();
                openBookingDetailsFromButton(bookingDetailsTrigger);
                return;
            }

            const previewIdTrigger = event.target.closest('.js-preview-id');
            if (previewIdTrigger) {
                event.preventDefault();
                openIdPreviewFromButton(previewIdTrigger);
            }
        });

        closeIdPreviewModal?.addEventListener('click', closePreviewModal);
        closeIdPreviewFooter?.addEventListener('click', closePreviewModal);
        closeInventorySuccessModal?.addEventListener('click', () => closeModal(inventorySuccessModal));
        inventorySuccessOk?.addEventListener('click', () => closeModal(inventorySuccessModal));

        // Close modals when clicking outside
        [inventoryModal, inventorySuccessModal, pendingModal, checkoutQueueModal, bookingDetailsModal, idPreviewModal, dayDetailsModal].forEach(modal => {
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) {
                    if (modal === idPreviewModal) {
                        closePreviewModal();
                    } else if (modal === bookingDetailsModal) {
                        closeDetailsModal();
                    } else {
                        closeModal(modal);
                    }
                }
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const topMostOpenModal = [...managedModals].reverse().find((modal) => !modal.classList.contains('is-hidden'));
                if (topMostOpenModal === idPreviewModal) {
                    closePreviewModal();
                    return;
                }
                if (topMostOpenModal === bookingDetailsModal) {
                    closeDetailsModal();
                    return;
                }
                if (topMostOpenModal) {
                    closeModal(topMostOpenModal);
                }
            }
        });

        // Fallback confirmation guard for admin mutation forms without explicit onsubmit prompts.
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            if (method !== 'POST') {
                return;
            }

            if (form.hasAttribute('onsubmit')) {
                return;
            }

            const action = form.getAttribute('action') || '';
            if (!action.includes('/admin/') || action.includes('/logout')) {
                return;
            }

            let confirmMessage = 'Are you sure you want to apply this change?';
            if (action.includes('/bookings/') && action.includes('/verify')) {
                confirmMessage = 'Verify customer ID for this booking?';
            } else if (action.includes('/bookings/') && action.includes('/confirm')) {
                confirmMessage = 'Confirm this booking now?';
            } else if (action.includes('/bookings/') && action.includes('/check-in')) {
                confirmMessage = 'Mark this guest as checked in?';
            } else if (action.includes('/bookings/') && action.includes('/cancel')) {
                confirmMessage = 'Are you sure you want to cancel this booking?';
            } else if (action.includes('/rooms')) {
                confirmMessage = 'Are you sure you want to save room settings changes?';
            } else if (action.includes('/inventory')) {
                confirmMessage = 'Are you sure you want to apply these inventory changes?';
            }

            if (!window.confirm(confirmMessage)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        });

        // Clear booking confirmation localStorage on logout
        const logoutForms = document.querySelectorAll('form[action*="logout"]');
        logoutForms.forEach(form => {
            form.addEventListener('submit', () => {
                localStorage.removeItem('acknowledgedBookings');
            });
        });

        let adminLiveRefreshTimer = null;
        window.PearlLiveRefresh = (payload = {}) => {
            if (payload?.scope === 'poll') {
                return;
            }
            if (document.body.classList.contains('admin-modal-open')) {
                if (!adminLiveRefreshTimer) {
                    adminLiveRefreshTimer = window.setTimeout(() => {
                        adminLiveRefreshTimer = null;
                        if (!document.body.classList.contains('admin-modal-open')) {
                            window.location.reload();
                        }
                    }, 2000);
                }
                return;
            }

            if (adminLiveRefreshTimer) {
                window.clearTimeout(adminLiveRefreshTimer);
                adminLiveRefreshTimer = null;
            }

            window.location.reload();
        };

        window.dispatchEvent(new Event('pearl:live-ready'));
    </script>
</body>
</html>

