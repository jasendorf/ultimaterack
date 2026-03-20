# UltimateRack Rewrite Plan

## Stack

- **Next.js 14** (App Router) — React frontend + API routes in one deployable unit
- **TypeScript** throughout
- **Canvas API** (browser) — live preview as user builds rack
- **node-canvas** (server) — high-fidelity PNG generation on the API route
- **Tailwind CSS** — styling
- **Papa Parse** — CSV parsing for ribbon set definitions

Single `npm run build && npm start` to deploy. No database needed.

---

## Ribbon Set Format (CSV + PNGs)

Each ribbon set lives in `public/ribbon-sets/<set-name>/`:

```
public/ribbon-sets/us-army/
  ribbons.csv
  heroism-medal.png
  good-conduct-medal.png
  ...
```

**ribbons.csv** columns:

```csv
id,name,image,device1,device2,army,navy,marines,airforce,coastguard,rightSideArmy
HM,Heroism Medal,heroism-medal.png,star,oak_leaf,12,15,14,13,16,0
GCM,Good Conduct Medal,good-conduct-medal.png,loop,,20,22,21,23,24,0
```

- `device1`/`device2`: string keys from a device registry (not magic numbers)
- Precedence columns: integer sort order per service branch (lower = higher precedence)
- Empty cells = not applicable to that service

**Discovery**: API route scans `public/ribbon-sets/*/ribbons.csv` at startup and caches the manifest. No registration file needed — drop a folder in and it appears.

---

## Device/Accouterment System

Device images stay in `public/images/devices/` (the existing PNGs: stars, oak leaves, numerals, etc.).

A single `device-registry.ts` maps device keys to rendering behavior:

```ts
export const devices = {
  star:           { type: 'div5', images: { bronze: 'bronze_star.png', silver: 'silver_star.png' } },
  oak_leaf:       { type: 'div5', images: { bronze: 'bronze_oak_leaf.png', silver: 'silver_oak_leaf.png' } },
  loop:           { type: 'indexed', pattern: 'good_conduct_clasp_{n}.png' },
  numeral:        { type: 'digits', pattern: 'numeral{d}.png' },
  V_device:       { type: 'single', image: 'v_device.png', position: 'center' },
  arrowhead:      { type: 'single', image: 'arrowhead.png', position: 'center' },
  // ... etc for all 21 current types
} as const;
```

This replaces the magic-number array and makes adding new device types self-documenting.

---

## UI — Single Page, Live Preview

Replace the 3-step wizard with a single page:

```
┌─────────────────────────────────────────────────────────┐
│  UltimateRack                          [Army ▼] [3 ▼]  │
│                                                         │
│  ┌─────────────────────┐  ┌──────────────────────────┐  │
│  │ Ribbon Selector      │  │ Live Rack Preview        │  │
│  │                      │  │                          │  │
│  │ [🔍 Search ribbons] │  │  ┌────┬────┬────┐       │  │
│  │                      │  │  │ ▓▓ │ ▓▓ │ ▓▓ │       │  │
│  │ ▸ US Army Awards     │  │  ├────┼────┼────┤       │  │
│  │   ☑ Bronze Star      │  │  │ ▓▓ │ ▓▓ │ ▓▓ │       │  │
│  │     Awards: [2] [V]  │  │  ├────┴────┘    │       │  │
│  │   ☑ Purple Heart     │  │  │ ▓▓ │              │  │
│  │     Awards: [1]      │  │  └────┘              │  │
│  │   ☐ Good Conduct     │  │                          │  │
│  │   ☐ Army Commend...  │  │  [Download PNG]          │  │
│  │                      │  │  [Copy Embed Code]       │  │
│  │ ▸ Joint Service      │  │                          │  │
│  │ ▸ US Navy Awards     │  └──────────────────────────┘  │
│  └─────────────────────┘                                 │
└─────────────────────────────────────────────────────────┘
```

**Key UX improvements over the original:**

1. **No wizard steps** — everything on one page, rack updates live
2. **Search/filter** — type to find ribbons across all sets instead of scanning tables
3. **Collapsible ribbon sets** — expand only what you need
4. **Inline configuration** — check a ribbon, configure awards/devices right there
5. **Live canvas preview** — see the rack build in real-time as you select
6. **Service selector at top** — changing service re-sorts the rack instantly
7. **Responsive** — works on mobile (stacked layout)

---

## Project Structure

```
ultimaterack/
├── src/
│   ├── app/
│   │   ├── layout.tsx              # Root layout
│   │   ├── page.tsx                # Main (and only) page
│   │   └── api/
│   │       ├── ribbon-sets/
│   │       │   └── route.ts        # GET: returns manifest of all ribbon sets
│   │       └── generate-rack/
│   │           └── route.ts        # POST: generates high-fidelity PNG
│   │
│   ├── components/
│   │   ├── RackBuilder.tsx         # Top-level orchestrator
│   │   ├── ServiceSelector.tsx     # Service branch + ribbons-per-row controls
│   │   ├── RibbonSelector.tsx      # Left panel: search + ribbon set accordion
│   │   ├── RibbonSetGroup.tsx      # Collapsible group of ribbons from one set
│   │   ├── RibbonEntry.tsx         # Single ribbon: checkbox, awards count, devices
│   │   ├── DeviceSelector.tsx      # Device checkboxes/inputs for a ribbon
│   │   ├── RackPreview.tsx         # Right panel: live canvas rendering
│   │   └── ExportControls.tsx      # Download PNG / copy embed code
│   │
│   ├── lib/
│   │   ├── types.ts                # Shared types (Ribbon, RibbonSet, Device, RackConfig)
│   │   ├── device-registry.ts      # Device definitions and rendering rules
│   │   ├── csv-parser.ts           # Parse ribbons.csv → RibbonSet[]
│   │   ├── rack-layout.ts          # Layout algorithm (shared between client/server)
│   │   ├── rack-renderer.ts        # Canvas rendering logic (shared between client/server)
│   │   └── precedence.ts           # Service-based sorting
│   │
│   └── styles/
│       └── globals.css             # Tailwind + custom styles
│
├── public/
│   ├── ribbon-sets/                # Drop-in ribbon set folders
│   │   ├── sample/
│   │   │   ├── ribbons.csv
│   │   │   └── *.png
│   │   └── [additional sets]/
│   │
│   └── images/
│       └── devices/                # Device overlay PNGs (existing assets)
│           ├── bronze_star.png
│           ├── silver_star.png
│           └── ...
│
├── scripts/
│   └── migrate-ribbon-sets.ts      # One-time: convert PHP arrays → CSV
│
├── package.json
├── tsconfig.json
├── tailwind.config.ts
└── next.config.ts
```

---

## Implementation Steps

### Phase 1: Project scaffolding
- `npx create-next-app` with TypeScript + Tailwind + App Router
- Define core types in `types.ts`
- Implement device registry
- Migrate existing sample ribbon set PHP → CSV (write migration script)
- Copy existing device/ribbon PNGs into `public/`

### Phase 2: Data layer
- CSV parser that loads ribbon sets from `public/ribbon-sets/*/ribbons.csv`
- API route `GET /api/ribbon-sets` — scans ribbon set folders, parses CSVs, returns JSON manifest
- Precedence sorting logic
- Rack layout algorithm (rows, centering of partial top row)

### Phase 3: UI components
- `RackBuilder` page layout (two-panel: selector + preview)
- `ServiceSelector` (service branch dropdown, ribbons-per-row)
- `RibbonSelector` with search input and collapsible `RibbonSetGroup`s
- `RibbonEntry` with checkbox, award count, device toggles
- State management via React context or `useReducer` — tracks selected ribbons, their configs, and service branch

### Phase 4: Live preview
- `RackPreview` component with `<canvas>` element
- `rack-renderer.ts` — shared rendering logic:
  - Load ribbon base image
  - Composite device overlays using Canvas 2D API
  - Arrange in grid per layout algorithm
- Re-renders on every state change (debounced)

### Phase 5: Server-side export
- API route `POST /api/generate-rack` — accepts rack config JSON
- Uses `node-canvas` (same rendering logic) to produce high-quality PNG
- Returns the image as a downloadable response
- `ExportControls` component: download button + embed code textarea

### Phase 6: Polish
- Responsive layout (mobile: stacked panels)
- Tooltip on hover showing ribbon name
- Keyboard navigation in ribbon list
- Error states (missing images, malformed CSV)
- Retain cloth/background swatch selector for preview

---

## Migration Path

1. **Ribbon set conversion**: `scripts/migrate-ribbon-sets.ts` reads the existing PHP array files via regex, outputs `ribbons.csv` per set
2. **Image assets**: Copy `images/` device PNGs and `ribbon_sets/*/` ribbon PNGs into `public/` structure
3. **Device mapping**: Translate the numeric `$device_types` array into the named `device-registry.ts`

The old PHP app can remain functional alongside the new app during transition.

---

## What Stays the Same

- All existing ribbon and device PNG assets (reused as-is)
- The compositing logic (translated from PHP/GD to Canvas API)
- The layout algorithm (grid, centering partial top row, service-based sort)
- 21 device types and their rendering behavior
- The concept of drop-in ribbon sets (now CSV instead of PHP include)

## What Changes

| Before | After |
|--------|-------|
| PHP 4.3 + GD library | Next.js + Canvas API + node-canvas |
| 3-step form wizard | Single page, live preview |
| PHP include files for ribbon sets | CSV + PNGs (no code required) |
| Magic number device indices | Named device keys |
| Server generates everything | Client preview + server export |
| No search, scan tables manually | Search/filter across all ribbon sets |
| Global variables, `$$var` | TypeScript types, React state |
| `die("nope")` error handling | Proper validation + user-facing errors |
| Path traversal vulnerabilities | No user-supplied file paths |
