# PixaProof Website Structure

## Architecture
Single-page mega landing with anchor navigation. All content consolidated into the homepage (`/`), with only Contact and Privacy as separate pages.

## Primary Navigation

```
Home                    → / (scroll to top)
Solutions               → /#solutions (anchor)
Technology              → /#how-it-works (anchor; the #technology section is hidden)
About                   → /#about (anchor)
FAQ                     → /#faq (anchor)
Request Demo            → /contact (separate page, primary CTA)
```

The navbar links come from the Statamic navigation `main` (edited in the control panel → Navigation);
"Request Demo" is a button in `components/navbar.blade.php`. On the contact/privacy pages the anchor links
navigate back to `/#section`.

---

## Page Hierarchy

### Active Pages
Statamic entries in the `pages` collection:

| Route | Entry / Template | Description |
|-------|------------------|-------------|
| `/` | `home` / `home` | Enterprise mega landing (9 sections) |
| `/contact` | `contact` / `contact` | Contact: email the sales team (mailto), offices |
| `/privacy` | `privacy` / `default` | Privacy policy (markdown in the entry, editable in the CP) |

### Homepage Sections (Anchor Navigation)
| Section | Anchor | Key Content |
|---------|--------|-------------|
| Hero | (top) | Innov8tif co-branding, tagline, CTAs |
| The Challenge | `#challenge` | Verification Gap, Cost of Compensating Controls |
| Solution | `#solution` | PixaProof introduction |
| How It Works | `#how-it-works` | Capture → Analyze → Deliver (3-step flow) |
| Use Cases | `#solutions` | Tabbed: Loan Draw Inspections, Insurance Claims, Field Operations & Assets, Property Inspections |
| Technology | `#technology` | PIEA capabilities bento grid — hidden (`@if (false)`) |
| Company | `#about` | Innov8tif background, certifications, stats |
| FAQ | `#faq` | Accordion Q&A |
| Final CTA | (bottom) | "Ready to Eliminate Image Fraud?" |

### Archived Pages (301 Redirects)
All legacy multi-page URLs redirect to homepage anchors or the contact page through the Marketing
Toolkit's redirects (control panel → Tools → SEO → Redirects; source list in `database/seo/redirects.csv`).
See `architecture.md` for the full map.

---

## Key CTAs

| Location | Primary CTA | Secondary CTA |
|----------|-------------|---------------|
| Hero | Request Demo → `/contact` | Learn How It Works → `#how-it-works` |
| Final CTA | Request Demo → `/contact` | Email: sales@innov8tif.com |
| Navbar | Request Demo → `/contact` | — |

---

## Footer Sections

### Navigation Links (Statamic navigation `footer`)
- Solutions
- Technology (→ `/#how-it-works`)
- About

### Company
- Contact
- Privacy Policy

### Contact
- sales@innov8tif.com

### Legal
- Privacy Policy
- © Innov8tif Solutions Pte. Ltd.

---

## Trust Elements

### Certifications (displayed in Company section)
- ISO 27001:2022
- ISO 27002:2022
- SOC 2 Type II (In Progress)
- GDPR Compliant

### Stats (displayed in Company section)
- 500+ Enterprise clients
- 3 Granted patents
- 14+ Years expertise
- 10+ Countries served

---

*Updated: 2026-02-09 - Rewritten for single-page mega landing architecture*
*Updated: 2026-10-06 - Statamic conversion: pages are entries, navs from Statamic, redirects from the Marketing Toolkit; contact is mailto; Technology links go to #how-it-works*
