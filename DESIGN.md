# DESIGN.md — NikahInfo System & UI Design Tokens

Documenting Design Tokens, Color Palette, Typography, Components, and Layout Architecture fetched from Google Stitch MCP for **NikahInfo** (Satu Data Pernikahan & Demografi Indonesia).

---

## 🎨 Design Tokens & System Overview

The visual identity of **NikahInfo** reflects a premier, authoritative civic & demographic data management portal (BPS & Kemenag RI integration). It balances institutional authority with modern, accessible UI/UX.

---

## 🎨 1. Color System

### Primary & Brand Colors
| Role | Color Name | Hex Code | Usage |
|------|------------|----------|-------|
| **Primary Base** | Deep Navy / Anchor | `#00236F` / `#1E3A8A` | Sidebar fills, main brand anchors, header titles, active status buttons |
| **Primary Container** | Navy Surface | `#1E3A8A` | Main navigation container, primary focus elements |
| **Primary Light / Fixed**| Soft Blue Pill | `#DCE1FF` / `#B6C4FF` | Active tags, secondary badges |
| **Secondary Accent** | Ocean Blue | `#0058BE` / `#3B82F6` | Metric highlights, chart primary series, focus rings |
| **Tertiary Accent** | Emerald / Teal | `#00312C` / `#0D9488` / `#89F5E7` | Trend positive indicators, realtime status, live data badges |
| **Background** | Light Slate Canvas | `#F8F9FF` / `#F1F5F9` | Application canvas background |
| **Surface Lowest** | Pure White Card | `#FFFFFF` | Card containers, table backdrops, modal backgrounds |
| **Surface Container** | Light Blue Tint | `#E5EEFF` / `#DCE9FF` | Secondary card fills, subtle hover states |
| **On-Surface (Text)** | Deep Charcoal/Slate | `#0B1C30` / `#0F172A` | Primary headings, table text, KPI values |
| **Text Secondary** | Muted Slate | `#444651` / `#64748B` | Helper copy, subheadings, labels |
| **Outline / Border** | Border Hairline | `#757682` / `#E2E8F0` | Structural card borders, input borders |

### Semantic Feedback Tokens
- **Positive Trend / Terverifikasi:** Emerald Green (`#059669`) / Pill fill (`#ECFDF5` / `#89F5E7`)
- **Warning / Menunggu Validasi:** Amber Gold (`#D97706`) / Pill fill (`#FFFBEB`)
- **Error / Discrepancy:** Crimson Red (`#BA1A1A`) / Pill fill (`#FFDAD6`)

---

## 🔤 2. Typography

- **Headlines & Titles:** `Plus Jakarta Sans` (Weights: 600, 700)
- **Body & Controls:** `Inter` (Weights: 400, 500, 600)
- **Numeric & Tabular Data:** `Inter` with `font-feature-settings: "tnum" 1` for tabular alignment.
- **Icons:** `Material Symbols Outlined` (Google Fonts)

| Token Name | Font Family | Size | Weight | Line Height |
|------------|-------------|------|--------|-------------|
| `display-lg` | Plus Jakarta Sans | 32px | 700 | 40px |
| `headline-lg` | Plus Jakarta Sans | 24px | 600 | 32px |
| `headline-md` | Plus Jakarta Sans | 20px | 600 | 28px |
| `headline-sm` | Plus Jakarta Sans | 16px | 600 | 24px |
| `kpi-value` | Inter (Tabular) | 28px | 700 | 34px |
| `body-lg` | Inter | 16px | 400 | 24px |
| `body-md` | Inter | 14px | 400 | 20px |
| `body-sm` | Inter | 12px | 400 | 16px |
| `label-md` | Inter | 13px | 500 | 18px |
| `label-sm` | Inter | 11px | 500 | 14px |

---

## 📐 3. Layout & Spacing Architecture

- **Sidebar Width:** `260px` (`w-64`), fixed left navigation with brand header and navigation items.
- **Header Height:** `64px` (`h-16`), sticky top bar with breadcrumbs, quick search, live status indicators, user profile.
- **Container Padding:** `1.5rem` (`p-6`) on desktop, `1rem` (`p-4`) on mobile viewports.
- **Grid Layout:** 12-column responsive layout (`grid-cols-1 md:grid-cols-2 lg:grid-cols-4` for KPI cards, bento grid for charts & maps).
- **Border Radius:**
  - KPI & Data Cards: `0.75rem` (12px / `rounded-xl`)
  - Buttons & Inputs: `0.5rem` (8px / `rounded-lg`)
  - Status Pills & Badges: `9999px` (`rounded-full`)

---

## 🧩 4. Key UI Components (Laravel Blade Components)

1. **`x-sidebar`** (`resources/views/components/sidebar.blade.php`)
   - Brand logo & portal title (`NikahInfo`)
   - Navigation links (Dashboard Utama, Kelola Data Pernikahan, Kelola Provinsi, Kelola Faktor & Sumber, Kelola Pengguna, Profil Saya)
   - Realtime data sync status indicator & logout button

2. **`x-navbar`** (`resources/views/components/navbar.blade.php`)
   - Breadcrumb navigation (`Portal / Ringkasan Analitik Nasional`)
   - Search input for provinces, registry, or reports
   - Live Hub badge & User Profile dropdown (`Drs. Hendra Irawan, M.Si`)

3. **`x-kpi-card`** (`resources/views/components/kpi-card.blade.php`)
   - Stat card displaying metric title, icon, numerical value, unit badge, and trend indicator (+/- percentage shift).

4. **`x-interactive-map`** (`resources/views/components/interactive-map.blade.php`)
   - SVG Interactive Choropleth map of Indonesia's 38 provinces with volume tooltips, metric selector dropdown, and map controls.

5. **`x-registration-form` / `x-filter-bar`** (`resources/views/components/filter-bar.blade.php`)
   - Filter controls by year range (2023–2025), province selector, and search button.

6. **`x-data-table`** (`resources/views/components/data-table.blade.php`)
   - Structured tabular view for national marriage records with status badges (*Terverifikasi*, *Menunggu Validasi*, *Data Belum Lengkap*).

7. **`x-modal`** (`resources/views/components/modal.blade.php`)
   - Reusable modal component for adding/editing marriage records, provincial details, and user management.

8. **`x-footer`** (`resources/views/components/footer.blade.php`)
   - Standard institutional portal footer with versioning and ministry copyright notes.

---

## 🛠️ 5. Tailwind CSS Configuration Extension

```js
// tailwind.config.js
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#00236f',
          container: '#1e3a8a',
          fixed: '#dce1ff',
          'fixed-dim': '#b6c4ff',
        },
        secondary: {
          DEFAULT: '#0058be',
          container: '#2170e4',
          fixed: '#d8e2ff',
          'fixed-dim': '#adc6ff',
        },
        tertiary: {
          DEFAULT: '#00312c',
          container: '#004942',
          fixed: '#89f5e7',
          'fixed-dim': '#6bd8cb',
        },
        surface: {
          DEFAULT: '#f8f9ff',
          dim: '#cbdbf5',
          bright: '#f8f9ff',
          variant: '#d3e4fe',
          container: '#e5eeff',
          'container-low': '#eff4ff',
          'container-lowest': '#ffffff',
          'container-high': '#dce9ff',
        },
        'on-surface': '#0b1c30',
        'on-surface-variant': '#444651',
        outline: '#757682',
        'outline-variant': '#c5c5d3',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        headline: ['Plus Jakarta Sans', 'sans-serif'],
      }
    },
  },
  plugins: [],
}
```
