# Homepage Content

> Enterprise-focused single-page mega landing. All solutions, technology, and company content consolidated into one page with anchor navigation.

---

## Section 1: Hero

### Layout
- **Structure**: Full-width, vertically centered content
- **Background**: `bg-gradient-to-br from-neutral-900 via-primary-900 to-neutral-800` with subtle grid pattern at low opacity
- **Spacing**: `pt-32 pb-20 lg:pt-40 lg:pb-28`

### Content

**Innov8tif Co-branding:**
- Innov8tif logo (white) + "presents" text
- Centered above the wordmark

**Wordmark:**
```
PIXAPROOF
```
- `text-5xl md:text-6xl lg:text-7xl font-bold text-white tracking-tight`

**Tagline:**
```
Verify Every Image. Eliminate Fraud.
```
- `text-2xl md:text-3xl font-medium text-primary-300`

**Subheadline:**
```
Document fraud costs enterprises millions annually. Manipulated photos slip through manual reviews. PixaProof's AI-powered verification detects tampering in seconds—protecting your business from fraudulent claims, fake documents, and compliance failures.
```
- `text-lg md:text-xl text-neutral-300`

**CTAs:**
- "Request Demo" → `/contact` (primary button, `size="lg"`)
- "Learn How It Works" → `#how-it-works` (outline button with down arrow icon)

---

## Section 2: Problem Statement (`#challenge`)

### Layout
- **Background**: `bg-neutral-50`
- **Spacing**: `py-20 lg:py-28`

### Part A: The Verification Gap
- Pre-label: "The Challenge" (`text-primary-600 font-semibold uppercase tracking-wider text-sm`)
- Headline: "The Verification Gap in User-Submitted Documentation"
- Body: Explains risk of user-submitted photos in financial, insurance, compliance contexts
- 4 problem cards in 2-column grid with warning icons:
  - Image metadata can be altered or removed
  - Generative AI enables synthetic documents at scale
  - Geolocation and timestamp data are trivially spoofed
  - No chain of custody from capture to submission

### Part B: The Cost of Compensating Controls
- Headline: "The Cost of Compensating Controls"
- 4 cost cards with red icons:
  - Physical site inspections
  - Extended processing timelines
  - Manual expert review queues
  - Incomplete evidence records

---

## Section 3: Solution Introduction (`#solution`)

### Layout
- **Background**: `bg-white`
- Centered text introducing PixaProof's approach

### Content
- Pre-label: "The Solution"
- Headline: "Security That Begins at the Shutter"
- Body: PixaProof addresses the gap between capture and verification

---

## Section 4: How It Works (`#how-it-works`)

### Layout
- **Background**: `bg-neutral-900` (dark section)
- 3-step horizontal flow

### Steps
1. **Capture** - SDK captures photo with cryptographic signing at point of capture
2. **Analyze** - AI runs 35+ tamper detection tests in <500ms
3. **Deliver** - Verified result with full audit trail returned via API

---

## Section 5: Use Cases (`#solutions`)

### Layout
- **Background**: `bg-neutral-50`
- Tabbed interface using `<x-vertical-tabs>`

### Tabs
| Tab | Title | Description |
|-----|-------|-------------|
| 1 | Loan Draw Inspections | Construction progress, property condition, collateral verification |
| 2 | Insurance Claims | Damage documentation, timestamped evidence, fraud prevention |
| 3 | KYC Onboarding | Identity document capture, anti-spoofing, live selfie verification |
| 4 | Asset Verification | Equipment inspection, inventory audits, condition reports |

Each tab shows industry-specific use case with benefits and process flow.

---

## Section 6: Technology Highlights (`#technology`)

### Layout
- **Background**: `bg-white`
- Bento grid showing PIEA capabilities

### Content
- Pre-label: "Technology"
- Headline: "PIEA™ — Photo Integrity Encoding Algorithm"
- Grid of technology cards highlighting:
  - Capture-level cryptographic signing
  - 35+ tamper detection tests
  - AI deepfake detection
  - Metadata integrity verification
  - Gallery injection prevention
  - <500ms verification latency

---

## Section 7: Company Credibility (`#about`)

### Layout
- **Background**: `bg-neutral-50`

### Content
- Innov8tif background: 14+ years, identity verification expertise
- Certifications: ISO 27001:2022, ISO 27002:2022, SOC 2 Type II
- Stats: 500+ clients, 3 patents, 10+ countries
- Datasonic Group Berhad / Innov8tif connection

---

## Section 8: FAQ (`#faq`)

### Layout
- **Background**: `bg-white`
- Accordion using Alpine.js (`x-data`, `x-show`, `x-collapse`)

### Questions
1. How does PixaProof differ from traditional forensic tools?
2. What platforms does the SDK support?
3. What happens when manipulation is detected?
4. How is data handled and protected?

---

## Section 9: Final CTA

### Layout
- **Background**: `bg-neutral-900`
- Centered content

### Content
**Headline:**
```
Ready to Eliminate Image Fraud?
```
- `text-3xl md:text-4xl font-bold text-white`

**Subheadline:**
```
See how PixaProof verifies authenticity at the point of capture. Request a demo with our solutions team.
```

**CTA:**
- "Request Demo" → `/contact` (primary button, `size="lg"`)
- Email fallback: `sales@innov8tif.com`

---

## Design Notes

### Color Scheme
- **Light sections**: `bg-neutral-50` or `bg-white`, `text-neutral-900` headings
- **Dark sections**: `bg-neutral-900`, `text-white` headings, `text-neutral-300` body
- **Hero**: Gradient with `primary-900` tint
- **Accent**: `text-primary-600` labels, `text-amber-500` warning icons

### Typography
- Headlines: `font-bold`, tight tracking
- Body: `text-neutral-700` (light bg), `text-neutral-300` (dark bg)
- Labels: `uppercase tracking-wider text-sm font-semibold text-primary-600`

### Interactive Elements
- FAQ accordion: Alpine.js `x-collapse` transitions
- Use Cases: Vertical tab switching
- Hover effects on cards: Border color transitions

---

*Updated: 2026-02-09 - Rewritten for enterprise mega landing (replaces Community Edition consumer page)*
