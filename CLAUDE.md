# CLAUDE.md — gsap-elementor

## Project Overview

**gsap-elementor** is a WordPress plugin that integrates [GSAP (GreenSock Animation Platform)](https://gsap.com/) with the [Elementor](https://elementor.com/) page builder. It allows Elementor users to apply GSAP-powered animations to their page elements.

**Status:** Early development / greenfield project.

## Repository State

This repository is in its initial setup phase. There is no existing source code, build system, or configuration yet. AI assistants should be prepared to scaffold the project from scratch if asked.

## Expected Tech Stack

| Layer | Technology |
|-------|-----------|
| Runtime | WordPress (PHP 7.4+) |
| Page Builder | Elementor (widget/control API) |
| Animation | GSAP 3.x (JavaScript) |
| Frontend JS | Vanilla JS or lightweight module bundler |
| Build (if needed) | Webpack, Vite, or wp-scripts |
| Package Manager | npm or Composer (for PHP deps) |

## Expected Directory Structure

When the project is built out, expect a structure similar to:

```
gsap-elementor/
├── CLAUDE.md                 # This file
├── README.md                 # User-facing documentation
├── gsap-elementor.php        # Main plugin entry point (plugin header)
├── includes/                 # PHP classes and logic
│   ├── class-plugin.php      # Plugin bootstrap
│   ├── class-controls.php    # Elementor custom controls
│   └── class-widgets.php     # Elementor widget extensions
├── assets/
│   ├── js/                   # Frontend JavaScript (GSAP init, animations)
│   └── css/                  # Plugin styles
├── build/                    # Compiled/minified assets (if using bundler)
├── src/                      # Source JS/CSS (if using bundler)
├── languages/                # i18n translation files
├── composer.json              # PHP dependencies (optional)
├── package.json               # JS dependencies and build scripts
└── .gitignore
```

## Development Conventions

### PHP
- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
- Use `gsap_elementor_` prefix for all functions, hooks, and option names.
- Use `Gsap_Elementor` or `GSAP_Elementor` namespace/class prefix.
- All text strings must be translatable using the `gsap-elementor` text domain.
- Minimum PHP version: 7.4.

### JavaScript
- GSAP should be enqueued properly via `wp_enqueue_script` with dependencies declared.
- Avoid jQuery unless Elementor's API requires it; prefer vanilla JS.
- Use `gsapElementor` as the JS namespace/global object if needed.

### WordPress Plugin Standards
- Plugin header must be in the main PHP file (`gsap-elementor.php`).
- Use activation/deactivation hooks for setup/teardown.
- Check for Elementor's presence before initializing (`did_action('elementor/loaded')`).
- Register controls/widgets using Elementor's documented API hooks.

### Git & Branching
- Development branches follow the pattern `claude/claude-md-*`.
- Commit messages should be concise and descriptive.
- Do not commit `node_modules/`, `vendor/`, or build artifacts unless intentionally bundling for distribution.

## Key Elementor Integration Points

When implementing, these are the primary Elementor hooks and APIs to use:

- **`elementor/widgets/register`** — Register custom widgets.
- **`elementor/controls/register`** — Register custom controls.
- **`elementor/element/after_section_end`** — Inject animation controls into existing widgets/sections.
- **`elementor/frontend/after_enqueue_scripts`** — Enqueue GSAP and animation scripts on the frontend.
- **`elementor/editor/after_enqueue_scripts`** — Enqueue scripts in the Elementor editor.
- **`elementor/frontend/before_render`** / **`after_render`** — Add data attributes or markup for animation targets.

## Build & Development Commands

*(To be updated once the build system is configured.)*

| Command | Purpose |
|---------|---------|
| `npm install` | Install JS dependencies |
| `npm run build` | Build production assets |
| `npm run dev` | Watch mode for development |
| `composer install` | Install PHP dependencies (if applicable) |

## Testing

*(To be updated once tests are added.)*

- PHP tests: PHPUnit with WordPress test suite.
- JS tests: Jest or similar (if applicable).
- Manual testing requires a WordPress environment with Elementor active.

## Important Notes for AI Assistants

1. **Check for Elementor dependency** — The plugin must gracefully deactivate or show an admin notice if Elementor is not installed/active.
2. **GSAP licensing** — GSAP has a proprietary license. The free version covers most use cases, but plugins using GSAP Club features (MorphSVG, SplitText, etc.) require a paid license. Only use free GSAP plugins (ScrollTrigger, Draggable, etc.) unless the user specifies otherwise.
3. **Performance** — GSAP scripts should only load on pages where animations are actually used. Use conditional enqueuing.
4. **Elementor compatibility** — Target Elementor 3.x+ APIs. Avoid deprecated methods.
5. **No external CDN** — Bundle GSAP locally or use npm; do not load from external CDNs in a distributed plugin.
