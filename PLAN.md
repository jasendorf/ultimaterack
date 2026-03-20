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

## Precedence Engine — Multi-Service Support

### The Problem

The original app only supports a single service branch selection. Real service members often serve across multiple branches (e.g., Air Force → Army → TX National Guard), and the precedence order of their ribbons depends on *which service they were in when each ribbon was earned*. A soldier who was previously an airman has a different ribbon order than an airman who was previously a soldier.

### The Solution: Service History + Per-Ribbon Service Tag

**Service History Input:**
The user enters an ordered list of their service periods:

```
1. US Air Force    (2005–2010)
2. US Army         (2010–2015)
3. TX Army Guard   (2015–present)
```

The *current* (most recent) service determines the **base precedence order** — this is the service whose regulations govern how the rack is arranged.

**Per-Ribbon Service Tag:**
Each ribbon the user selects is automatically tagged with a service based on which ribbon set it came from. The user can override this if needed (e.g., a Joint Service ribbon earned while in the Army).

**Ordering Algorithm:**

1. Ribbons are grouped by the service under which they were earned
2. Within each group, ribbons are sorted by that service's precedence numbers (from the CSV)
3. Groups are arranged according to military regulations for the current service:
   - Current service ribbons first, in precedence order
   - Previous service ribbons follow, in their own precedence order
   - The order of previous-service groups follows the service history (most recent first)
4. Certain ribbons (Joint, DoD-level) have cross-service precedence rules that override grouping

**Data Source:** The precedence numbers in the CSV already encode the per-service sort order. The engine just needs to know *which column to use* for each ribbon based on service history context.

**Goal:** The user should trust the order. The system is the authority — not the service member guessing from memory. Where regulations are ambiguous, we surface a note explaining the choice.

---

## UI — Two-Step SPA with Live Preview

Given the volume (dozens of ribbon sets, scores of ribbons each), a two-step flow within a single-page app prevents overwhelm while keeping things smooth (no page reloads).

### Step 1: Service Profile

```
┌──────────────────────────────────────────────────────┐
│  UltimateRack                                        │
│                                                      │
│  Tell us about your service                          │
│                                                      │
│  Service History (drag to reorder):                  │
│  ┌──────────────────────────────────────────────┐    │
│  │ 1. [US Air Force ▼]  2005 – 2010  [✕]       │    │
│  │ 2. [US Army ▼]       2010 – 2015  [✕]       │    │
│  │ 3. [TX Natl Guard ▼] 2015 – present [✕]     │    │
│  │                       [+ Add service period] │    │
│  └──────────────────────────────────────────────┘    │
│                                                      │
│  Ribbon Sets to Include:                             │
│  (auto-selected based on service history,            │
│   user can add/remove)                               │
│                                                      │
│  ☑ US Air Force Awards (47)                          │
│  ☑ US Army Awards (52)                               │
│  ☑ TX National Guard Awards (18)                     │
│  ☑ Joint Service Awards (23)     ← auto-included     │
│  ☐ US Navy Awards (41)                               │
│  ☐ US Marine Corps Awards (38)                       │
│  ...                                                 │
│                                                      │
│                              [Build My Rack →]       │
└──────────────────────────────────────────────────────┘
```

**Key behaviors:**
- Entering service history auto-checks the relevant ribbon sets
- Joint/DoD-level sets auto-included always
- Dates are optional but help with device eligibility and ordering notes
- User can manually check additional sets if needed

### Step 2: Ribbon Selection + Live Preview

```
┌───────────────────────────────────────────────────────────┐
│  UltimateRack                    [← Back]  [3 across ▼]  │
│                                                           │
│  ┌──────────────────────────┐ ┌────────────────────────┐  │
│  │ [🔍 Search all ribbons]  │ │ Live Rack Preview      │  │
│  │                          │ │                        │  │
│  │ ▸ US Air Force (0 sel.)  │ │  ┌────┬────┬────┐     │  │
│  │ ▾ US Army (3 selected)   │ │  │ ▓▓ │ ▓▓ │ ▓▓ │     │  │
│  │   ☑ Bronze Star Medal    │ │  ├────┼────┼────┤     │  │
│  │     × 2  ☑V              │ │  │ ▓▓ │ ▓▓ │ ▓▓ │     │  │
│  │     earned in: Army      │ │  ├────┴────┘          │  │
│  │   ☑ Purple Heart         │ │  │ ▓▓ │               │  │
│  │     × 1                  │ │  └────┘               │  │
│  │     earned in: Army      │ │                        │  │
│  │   ☑ Army Commendation    │ │  Ordered per AR 670-1 │  │
│  │     × 1  ☑OLC            │ │  for Army (current    │  │
│  │     earned in: Army      │ │  service), with USAF  │  │
│  │   ☐ Army Achievement     │ │  ribbons per AFI      │  │
│  │   ☐ Good Conduct Medal   │ │  36-2803.             │  │
│  │ ▸ Joint Service (1 sel.) │ │                        │  │
│  │ ▸ TX Natl Guard (0 sel.) │ │  [Download PNG]        │  │
│  │                          │ │  [Copy Embed Code]     │  │
│  └──────────────────────────┘ └────────────────────────┘  │
└───────────────────────────────────────────────────────────┘
```

**Key behaviors:**
- Search filters across all included ribbon sets instantly
- Ribbon sets collapsed by default, show "(N selected)" count
- Selecting a ribbon expands inline config: award count, device checkboxes, service tag
- Service tag auto-set from ribbon set but overridable
- Live preview updates on every change (debounced)
- Preview shows ordering citation (which regulation governs the sort)
- Ribbon sets sorted by relevance: current service first, then previous services in reverse chronological order, then joint/DoD

---

## Project Structure

```
ultimaterack/
├── src/
│   ├── app/
│   │   ├── layout.tsx                 # Root layout
│   │   ├── page.tsx                   # Two-step SPA page
│   │   └── api/
│   │       ├── ribbon-sets/
│   │       │   └── route.ts           # GET: returns manifest of all ribbon sets
│   │       └── generate-rack/
│   │           └── route.ts           # POST: generates high-fidelity PNG
│   │
│   ├── components/
│   │   ├── RackBuilder.tsx            # Top-level orchestrator (manages step 1 ↔ 2)
│   │   ├── ServiceProfileStep.tsx     # Step 1: service history + set selection
│   │   ├── ServiceHistoryInput.tsx    # Ordered list of service periods
│   │   ├── RibbonSetPicker.tsx        # Checkbox list of ribbon sets
│   │   ├── RibbonSelectionStep.tsx    # Step 2: ribbon selection + preview
│   │   ├── RibbonSelector.tsx         # Left panel: search + ribbon set accordion
│   │   ├── RibbonSetGroup.tsx         # Collapsible group of ribbons from one set
│   │   ├── RibbonEntry.tsx            # Single ribbon: checkbox, awards count, devices
│   │   ├── DeviceSelector.tsx         # Device checkboxes/inputs for a ribbon
│   │   ├── RackPreview.tsx            # Right panel: live canvas rendering
│   │   └── ExportControls.tsx         # Download PNG / copy embed code
│   │
│   ├── lib/
│   │   ├── types.ts                   # Shared types (Ribbon, RibbonSet, Device, RackConfig, ServicePeriod)
│   │   ├── device-registry.ts         # Device definitions and rendering rules
│   │   ├── csv-parser.ts              # Parse ribbons.csv → RibbonSet[]
│   │   ├── rack-layout.ts             # Layout algorithm (shared between client/server)
│   │   ├── rack-renderer.ts           # Canvas rendering logic (shared between client/server)
│   │   └── precedence.ts              # Multi-service precedence engine
│   │
│   └── styles/
│       └── globals.css                # Tailwind + custom styles
│
├── public/
│   ├── ribbon-sets/                   # Drop-in ribbon set folders
│   │   ├── sample/
│   │   │   ├── ribbons.csv
│   │   │   └── *.png
│   │   └── [additional sets]/
│   │
│   └── images/
│       └── devices/                   # Device overlay PNGs (existing assets)
│
├── scripts/
│   └── migrate-ribbon-sets.ts         # One-time: convert PHP arrays → CSV
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
- Define core types in `types.ts` (including `ServicePeriod`, `ServiceHistory`)
- Implement device registry
- Migrate existing sample ribbon set PHP → CSV (write migration script)
- Copy existing device/ribbon PNGs into `public/`

### Phase 2: Data layer
- CSV parser that loads ribbon sets from `public/ribbon-sets/*/ribbons.csv`
- API route `GET /api/ribbon-sets` — scans ribbon set folders, parses CSVs, returns JSON manifest
- Multi-service precedence engine (`precedence.ts`)
- Rack layout algorithm (rows, centering of partial top row)

### Phase 3: Step 1 UI — Service Profile
- `ServiceHistoryInput` — add/remove/reorder service periods
- `RibbonSetPicker` — auto-checked based on service history, manually adjustable
- Transition to Step 2

### Phase 4: Step 2 UI — Ribbon Selection
- `RibbonSelector` with search input and collapsible `RibbonSetGroup`s
- `RibbonEntry` with checkbox, award count, device toggles, service tag
- State management via `useReducer` — tracks selected ribbons, configs, service history

### Phase 5: Live preview + export
- `RackPreview` component with `<canvas>` element
- `rack-renderer.ts` — shared rendering logic (compositing ribbons + devices)
- Re-renders on every state change (debounced)
- API route `POST /api/generate-rack` for high-quality PNG export
- `ExportControls`: download button + embed code textarea

### Phase 6: Polish
- Responsive layout (mobile: stacked, step 2 preview below selector)
- Tooltip on hover showing ribbon name in preview
- Ordering citation text (which regulation governs the sort)
- Error states (missing images, malformed CSV)
- Cloth/background swatch selector for preview
- Keyboard navigation in ribbon list

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
- The layout algorithm (grid, centering partial top row)
- 21 device types and their rendering behavior
- The concept of drop-in ribbon sets (now CSV instead of PHP include)

## What Changes

| Before | After |
|--------|-------|
| PHP 4.3 + GD library | Next.js + Canvas API + node-canvas |
| 3-step form wizard with page reloads | Two-step SPA with live preview |
| Single service branch selection | Multi-service history with per-ribbon tagging |
| Precedence by one service only | Precedence engine respecting full service history |
| PHP include files for ribbon sets | CSV + PNGs (no code required) |
| Magic number device indices | Named device keys |
| Server generates everything | Client preview + server export |
| No search, scan tables manually | Search/filter across all ribbon sets |
| Global variables, `$$var` | TypeScript types, React state |
| `die("nope")` error handling | Proper validation + user-facing errors |
| Path traversal vulnerabilities | No user-supplied file paths |
| No ordering explanation | Citation of governing regulation |
