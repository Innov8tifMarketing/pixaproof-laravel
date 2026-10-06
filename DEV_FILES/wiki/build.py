#!/usr/bin/env python3
"""Build the codebase wiki.

    python3 DEV_FILES/wiki/provenance.py   # when files were added or moved (needs the stock installs)
    python3 DEV_FILES/wiki/build.py

Reads src/*.html (page bodies) and diagrams/*.dot (Graphviz sources), renders
each diagram to SVG with `dot`, inlines the SVG wherever a page says
{{diagram:name}}, wraps every page in the shared shell (sidebar + styles) and
writes the result next to this script.

Inlining (rather than <img src=...svg>) keeps the diagram nodes clickable: a
node with URL="03-content-seo.html" or URL="../../app/Http/Middleware/SecurityHeaders.php"
resolves relative to the page, exactly like an ordinary <a href>.

Markup in page bodies:
    {{diagram:name}}       diagrams/name.dot, rendered and inlined
    [[path]] [[path:12]]   a link to a repo file; a missing file fails the build
    {{L}} {{S}} {{A}} {{C}} {{A:label}}   provenance badges
    {{provenance}}         the per-file table from provenance.json

Edit a .dot file or a page, re-run, refresh the browser.
"""

import html
import json
import re
import subprocess
import sys
from collections import defaultdict
from pathlib import Path

ROOT = Path(__file__).resolve().parent
SRC = ROOT / "src"
DIAGRAMS = ROOT / "diagrams"

# Order is the reading order; it drives the sidebar and the prev/next links.
PAGES = [
    ("index.html", "0. Start here"),
    ("00-provenance.html", "0b. Stock vs custom"),
    ("01-request-lifecycle.html", "1. A request, end to end"),
    ("02-frontend.html", "2. Templates & frontend"),
    ("03-content-seo.html", "3. Content & SEO"),
    ("04-database.html", "4. Database"),
    ("05-control-panel.html", "5. Control panel"),
    ("06-security.html", "6. Security headers & CSP"),
    ("07-background-work.html", "7. Commands, queue, scheduler"),
    ("08-infrastructure.html", "8. Deployment & infrastructure"),
    ("09-testing-quality.html", "9. Tests & quality gates"),
    ("10-whiteboard.html", "10. Whiteboard defense"),
]

PROVENANCE = {
    "L": ("laravel", "Laravel"),
    "S": ("statamic", "Statamic"),
    "A": ("addon", "Addon"),
    "C": ("custom", "Custom"),
}


def render_svg(name: str) -> str:
    dot_file = DIAGRAMS / f"{name}.dot"
    if not dot_file.exists():
        sys.exit(f"missing diagram: {dot_file}")
    svg = subprocess.run(
        ["dot", "-Tsvg", str(dot_file)], check=True, capture_output=True, text=True
    ).stdout
    # Standalone copy for "open full size" (links inside it resolve from diagrams/,
    # so those are rewritten one level up).
    (DIAGRAMS / f"{name}.svg").write_text(svg.replace('xlink:href="../../', 'xlink:href="../../../'))
    # Drop the XML prolog and DOCTYPE; they are invalid inside an HTML body.
    svg = svg[svg.index("<svg"):]
    # Scale down to the column when too wide, but never up past natural size.
    m = re.match(r'<svg width="([\d.]+)pt" height="[\d.]+pt"', svg)
    natural_px = round(float(m.group(1)) * 4 / 3) if m else 900
    svg = re.sub(r'<svg width="[^"]+" height="[^"]+"', f'<svg style="max-width:min(100%, {natural_px}px)"', svg, count=1)
    return (f'<figure class="diagram" id="fig-{name}">{svg}<figcaption>'
            f'<a href="diagrams/{name}.svg" target="_blank">open full size</a> · '
            f'source: <a href="diagrams/{name}.dot">diagrams/{name}.dot</a></figcaption></figure>')


def source_link(match: re.Match) -> str:
    """[[app/Models/User.php]] or [[app/Models/User.php:40]] -> a link into the repo.

    The line suffix is shown but not linked. Missing paths fail the build so the
    wiki cannot drift silently.
    """
    ref = match.group(1)
    path = ref.split(":")[0]
    if not (ROOT.parent.parent / path).exists():
        sys.exit(f"broken source reference: {ref}")
    return f'<a class="src" href="../../{path}"><code>{ref}</code></a>'


def badge(origin: str, detail: str | None = None) -> str:
    cls, label = PROVENANCE[origin]
    if detail:
        label = f"{label}: {detail}"
    return f'<span class="prov {cls}" title="{label}">{label}</span>'


def provenance_badge(match: re.Match) -> str:
    """{{L}} {{S}} {{C}} {{A}} or {{A:Toolkit}} -> a coloured badge."""
    return badge(match.group(1), match.group(2))


def provenance_table() -> str:
    """Every tracked file grouped by top-level folder, from provenance.json."""
    data_file = ROOT / "provenance.json"
    if not data_file.exists():
        sys.exit("missing provenance.json: run provenance.py first")
    rows = json.loads(data_file.read_text())

    totals = defaultdict(int)
    for row in rows:
        totals[(row["origin"], row["status"])] += 1
    summary = "".join(
        f"<tr><td class=\"p\">{badge(origin)}</td><td>{status}</td><td>{count}</td></tr>"
        for (origin, status), count in sorted(totals.items())
    )

    groups = defaultdict(list)
    for row in rows:
        top = row["path"].split("/")[0] if "/" in row["path"] else "(root)"
        groups[top].append(row)

    sections = []
    for top in sorted(groups):
        items = groups[top]
        body = "".join(
            f"<tr><td><code>{html.escape(r['path'])}</code></td><td class=\"p\">{badge(r['origin'])}</td>"
            f"<td>{r['status']}</td><td>{html.escape(r['reason'])}</td></tr>"
            for r in items
        )
        sections.append(
            f"<details><summary><code>{html.escape(top)}/</code> · {len(items)} files</summary>"
            f"<table><tr><th>File</th><th class=\"p\">Origin</th><th>Status</th><th>Detail</th></tr>{body}</table></details>"
        )

    return (f"<table><tr><th class=\"p\">Origin</th><th>Status</th><th>Files</th></tr>{summary}</table>"
            + "".join(sections))


def shell(title: str, body: str, current: str) -> str:
    nav = []
    for href, label in PAGES:
        cls = ' class="current"' if href == current else ""
        nav.append(f'<li><a href="{href}"{cls}>{label}</a></li>')
    idx = [p[0] for p in PAGES].index(current)
    prev_link = f'<a href="{PAGES[idx-1][0]}">← {PAGES[idx-1][1]}</a>' if idx > 0 else "<span></span>"
    next_link = f'<a href="{PAGES[idx+1][0]}">{PAGES[idx+1][1]} →</a>' if idx < len(PAGES) - 1 else "<span></span>"
    return f"""<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{title} · pixaproof.com wiki</title>
<link rel="stylesheet" href="assets/wiki.css">
</head>
<body>
<nav class="sidebar">
  <div class="brand">pixaproof.com<br><small>codebase wiki</small></div>
  <ol>{''.join(nav)}</ol>
  <p class="prov-legend"><span class="prov laravel">Laravel</span> framework default<br>
  <span class="prov statamic">Statamic</span> Statamic default<br>
  <span class="prov addon">Addon</span> a package we installed<br>
  <span class="prov custom">Custom</span> written in this repo<br>
  Diagram boxes use the same colours. <a href="00-provenance.html">Full map</a></p>
  <p class="legend">Paths like <code>app/Http/Middleware/SecurityHeaders.php</code> are relative to the repo root.
  Links open the file in your browser; <kbd>gf</kbd> on the path in your editor works too.</p>
</nav>
<main>
{body}
<footer class="pager">{prev_link}{next_link}</footer>
</main>
</body>
</html>
"""


def main() -> None:
    for href, label in PAGES:
        src = SRC / href
        if not src.exists():
            sys.exit(f"missing page: {src}")
        body = src.read_text()
        body = re.sub(r"\{\{diagram:([\w-]+)\}\}", lambda m: render_svg(m.group(1)), body)
        body = body.replace("{{provenance}}", provenance_table()) if "{{provenance}}" in body else body
        body = re.sub(r"\[\[([^\]]+)\]\]", source_link, body)
        body = re.sub(r"\{\{([LSAC])(?::([^}]+))?\}\}", provenance_badge, body)
        (ROOT / href).write_text(shell(label, body, href))
        print(f"built {href}")


if __name__ == "__main__":
    main()
