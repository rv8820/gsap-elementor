# CLAUDE.md — gsap-elementor

## Project Overview

**gsap-elementor** is a WordPress plugin that integrates [GSAP (GreenSock Animation Platform)](https://gsap.com/) with the [Elementor](https://elementor.com/) page builder. It provides 14 standalone Elementor widgets — one for each major GSAP feature — with live editor preview support.

**Author:** [Craft](https://craft.com.sg)
**License:** GPL-2.0-or-later
**Status:** v1.0.0 — all widgets implemented

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Runtime | WordPress (PHP 7.4+) |
| Page Builder | Elementor 3.5+ / Elementor Pro compatible |
| Animation | GSAP 3.x (all plugins bundled via npm) |
| Frontend JS | Vanilla JS (ES modules bundled by Webpack) |
| Build | Webpack 5 + Babel |
| Package Manager | npm |

## Directory Structure

```
gsap-elementor/
├── CLAUDE.md                            # This file
├── gsap-elementor.php                   # Main plugin entry point (plugin header)
├── package.json                         # npm config — GSAP + webpack deps
├── webpack.config.js                    # Webpack config — 2 entry points
├── .gitignore
├── includes/
│   ├── class-plugin.php                 # Singleton bootstrap, hooks, category registration
│   ├── class-assets.php                 # Script/style enqueuing (frontend + editor + preview)
│   ├── class-widgets-manager.php        # Auto-loads and registers all 14 widgets
│   └── widgets/
│       ├── class-widget-base.php        # Abstract base — shared controls (easing, transform, ScrollTrigger)
│       ├── class-widget-gsap-animate.php       # Core tween (to/from/fromTo + stagger)
│       ├── class-widget-scroll-trigger.php     # Scroll-triggered animations (pin, scrub, parallax)
│       ├── class-widget-scroll-to.php          # Smooth scroll button
│       ├── class-widget-split-text.php         # SplitText (chars/words/lines + presets)
│       ├── class-widget-scramble-text.php      # ScrambleText decode effect
│       ├── class-widget-text-typewriter.php    # TextPlugin typewriter + cursor
│       ├── class-widget-draw-svg.php           # DrawSVG stroke animation (preset shapes + custom)
│       ├── class-widget-morph-svg.php          # MorphSVG shape morphing (repeater-based targets)
│       ├── class-widget-motion-path.php        # MotionPath (preset paths + custom SVG d attr)
│       ├── class-widget-flip.php               # Flip layout transitions (grid filter/toggle/shuffle)
│       ├── class-widget-draggable.php          # Draggable + Inertia
│       ├── class-widget-scroll-smoother.php    # ScrollSmoother (smooth scroll + data-speed/data-lag)
│       ├── class-widget-observer.php           # Observer gesture/event-driven animation
│       └── class-widget-physics2d.php          # Physics2D particle/gravity effects
├── src/
│   └── js/
│       ├── frontend.js              # All GSAP imports + per-widget initializers (reads data-gsap-config)
│       └── editor.js                # Elementor editor integration (live preview reinit on settings change)
├── assets/
│   ├── js/
│   │   ├── gsap-frontend.js        # Webpack output — bundled GSAP + all widget handlers
│   │   └── gsap-editor.js          # Webpack output — editor panel script
│   └── css/
│       └── gsap-frontend.css        # Widget base styles
└── languages/                       # i18n (empty, ready for translations)
```

## Architecture

### Data Flow
1. **PHP widget** renders HTML with `data-gsap-widget="widget_name"` and `data-gsap-config='{ JSON }'`
2. **`frontend.js`** queries all `[data-gsap-widget]` elements, parses config, and dispatches to the correct initializer
3. **`editor.js`** listens for Elementor panel changes and reinitializes widgets in the preview iframe for live preview

### Widget Pattern
Every widget follows the same pattern:
- Extends `Widget_GSAP_Base` (which extends Elementor's `Widget_Base`)
- Uses `register_controls()` to define Elementor panel UI
- Uses `render()` to output HTML with `data-gsap-widget` and `data-gsap-config` attributes
- The JS frontend handler reads the config and runs the appropriate GSAP code

### Shared Base Controls
`Widget_GSAP_Base` provides reusable methods:
- `register_animation_controls()` — duration, delay, ease, repeat, yoyo
- `register_transform_controls($prefix)` — x, y, rotation, scale, opacity
- `register_scroll_trigger_controls()` — start, end, scrub, pin, markers, toggleActions
- `get_easing_options()` — full GSAP easing list
- `render_data_attrs($data)` — outputs `data-gsap-widget` and `data-gsap-config`

## Widgets Reference

| Widget | Class | GSAP Plugin(s) |
|--------|-------|----------------|
| GSAP Animate | `Widget_GSAP_Animate` | Core (to/from/fromTo) |
| GSAP ScrollTrigger | `Widget_Scroll_Trigger` | ScrollTrigger |
| GSAP Scroll To | `Widget_Scroll_To` | ScrollToPlugin |
| GSAP SplitText | `Widget_Split_Text` | SplitText |
| GSAP ScrambleText | `Widget_Scramble_Text` | ScrambleTextPlugin |
| GSAP Typewriter | `Widget_Text_Typewriter` | TextPlugin |
| GSAP DrawSVG | `Widget_Draw_SVG` | DrawSVGPlugin |
| GSAP MorphSVG | `Widget_Morph_SVG` | MorphSVGPlugin |
| GSAP MotionPath | `Widget_Motion_Path` | MotionPathPlugin |
| GSAP Flip | `Widget_Flip` | Flip |
| GSAP Draggable | `Widget_Draggable` | Draggable, InertiaPlugin |
| GSAP ScrollSmoother | `Widget_Scroll_Smoother` | ScrollSmoother, ScrollTrigger |
| GSAP Observer | `Widget_Observer` | Observer |
| GSAP Physics2D | `Widget_Physics2D` | Physics2DPlugin |

## Build & Development Commands

| Command | Purpose |
|---------|---------|
| `npm install` | Install JS dependencies (GSAP + webpack toolchain) |
| `npm run build` | Build production assets to `assets/js/` |
| `npm run dev` | Watch mode for development |

Build outputs:
- `assets/js/gsap-frontend.js` — ~256 KB minified (GSAP core + all plugins + widget handlers)
- `assets/js/gsap-editor.js` — ~1 KB minified (Elementor editor integration)

## Development Conventions

### PHP
- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
- All classes are in the `Gsap_Elementor` namespace.
- Use `gsap_elementor_` prefix for functions, hooks, and option names.
- All strings use the `gsap-elementor` text domain.
- Minimum PHP version: 7.4.

### JavaScript
- GSAP is bundled via npm — no external CDN.
- All widget handlers are in `src/js/frontend.js` and dispatched by `data-gsap-widget` attribute.
- Use vanilla JS only — no jQuery.
- `window.gsapElementor.init()` is the public API for reinitializing widgets.

### Adding a New Widget
1. Create `includes/widgets/class-widget-{name}.php` extending `Widget_GSAP_Base`
2. Add the mapping to `Widgets_Manager::get_widgets()`
3. Add the corresponding `case` in `src/js/frontend.js` → `initGsapWidgets()` switch
4. Create the `init{Name}(el, config)` function in frontend.js
5. Run `npm run build`

### Git & Branching
- Development branches: `claude/claude-md-*`
- Do not commit `node_modules/`
- Built assets (`assets/js/*.js`) ARE committed for distribution

## Key Elementor Integration Points

- **`elementor/widgets/register`** — Registers all 14 widgets via `Widgets_Manager::register()`
- **`elementor/frontend/after_enqueue_scripts`** — Enqueues `gsap-frontend.js` + CSS
- **`elementor/editor/after_enqueue_scripts`** — Enqueues `gsap-editor.js` for panel integration
- **`elementor/preview/enqueue_scripts`** — Enqueues frontend scripts in preview iframe
- **`elementor/elements/categories_registered`** — Adds "GSAP Animations" widget category

## Important Notes for AI Assistants

1. **Elementor dependency** — Plugin shows admin notice and bails if Elementor < 3.5.0 is not active.
2. **GSAP licensing** — GSAP is now fully free (including SplitText, MorphSVG, etc.) since Webflow's sponsorship. All plugins are safe to use.
3. **Performance** — The full GSAP bundle is ~256 KB. Future optimization could use code splitting per widget type.
4. **Elementor Pro compatibility** — Widgets register in their own category and don't conflict with Pro widgets.
5. **Live preview** — `editor.js` watches for setting changes and reinitializes animations in the preview iframe via `window.gsapElementor.init()`.
6. **No external CDN** — GSAP is bundled via npm into `gsap-frontend.js`.
