/**
 * GSAP Elementor — Frontend Animation Handler
 *
 * Reads data-gsap-widget and data-gsap-config from each widget
 * and initializes the corresponding GSAP animation.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ScrollToPlugin } from 'gsap/ScrollToPlugin';
import { ScrollSmoother } from 'gsap/ScrollSmoother';
import { SplitText } from 'gsap/SplitText';
import { DrawSVGPlugin } from 'gsap/DrawSVGPlugin';
import { MorphSVGPlugin } from 'gsap/MorphSVGPlugin';
import { MotionPathPlugin } from 'gsap/MotionPathPlugin';
import { Flip } from 'gsap/Flip';
import { Draggable } from 'gsap/Draggable';
import { InertiaPlugin } from 'gsap/InertiaPlugin';
import { Observer } from 'gsap/Observer';
import { TextPlugin } from 'gsap/TextPlugin';
import { ScrambleTextPlugin } from 'gsap/ScrambleTextPlugin';
import { Physics2DPlugin } from 'gsap/Physics2DPlugin';
import { CustomEase } from 'gsap/CustomEase';

gsap.registerPlugin(
	ScrollTrigger,
	ScrollToPlugin,
	ScrollSmoother,
	SplitText,
	DrawSVGPlugin,
	MorphSVGPlugin,
	MotionPathPlugin,
	Flip,
	Draggable,
	InertiaPlugin,
	Observer,
	TextPlugin,
	ScrambleTextPlugin,
	Physics2DPlugin,
	CustomEase
);

/**
 * Main initialization — runs on DOMContentLoaded and in Elementor preview.
 */
function initGsapWidgets(container) {
	const root = container || document;
	const widgets = root.querySelectorAll('[data-gsap-widget]');

	widgets.forEach((el) => {
		// Skip already-initialized widgets
		if (el.dataset.gsapInit) return;
		el.dataset.gsapInit = '1';

		const type = el.dataset.gsapWidget;
		let config;
		try {
			config = JSON.parse(el.dataset.gsapConfig || '{}');
		} catch (e) {
			console.warn('GSAP Elementor: invalid config for', type, e);
			return;
		}

		switch (type) {
			case 'gsap_animate':
				initAnimate(el, config);
				break;
			case 'gsap_scroll_trigger':
				initScrollTrigger(el, config);
				break;
			case 'gsap_scroll_to':
				initScrollTo(el, config);
				break;
			case 'gsap_split_text':
				initSplitText(el, config);
				break;
			case 'gsap_scramble_text':
				initScrambleText(el, config);
				break;
			case 'gsap_text_typewriter':
				initTypewriter(el, config);
				break;
			case 'gsap_draw_svg':
				initDrawSVG(el, config);
				break;
			case 'gsap_morph_svg':
				initMorphSVG(el, config);
				break;
			case 'gsap_motion_path':
				initMotionPath(el, config);
				break;
			case 'gsap_flip':
				initFlip(el, config);
				break;
			case 'gsap_draggable':
				initDraggable(el, config);
				break;
			case 'gsap_scroll_smoother':
				initScrollSmoother(el, config);
				break;
			case 'gsap_observer':
				initObserver(el, config);
				break;
			case 'gsap_physics2d':
				initPhysics2D(el, config);
				break;
			default:
				console.warn('GSAP Elementor: unknown widget type', type);
		}
	});
}

/* ======================================
 * Widget Initializers
 * ====================================== */

/**
 * GSAP Animate — Core tween (to / from / fromTo)
 */
function initAnimate(el, config) {
	const target = el.querySelector('.gsap-animate-target');
	if (!target) return;

	const tweenVars = {
		duration: config.duration || 1,
		delay: config.delay || 0,
		ease: config.ease || 'power2.out',
		repeat: config.repeat || 0,
		yoyo: !!config.yoyo,
	};

	if (config.stagger) {
		tweenVars.stagger = config.stagger;
	}

	const transformTo = buildTransform(config.to);

	if (config.scrollTrigger) {
		tweenVars.scrollTrigger = buildScrollTrigger(el, config.scrollTrigger);
	}

	if (config.method === 'from') {
		gsap.from(target, { ...transformTo, ...tweenVars });
	} else if (config.method === 'fromTo') {
		const transformFrom = buildTransform(config.from);
		gsap.fromTo(target, transformFrom, { ...transformTo, ...tweenVars });
	} else {
		gsap.to(target, { ...transformTo, ...tweenVars });
	}
}

/**
 * ScrollTrigger — Dedicated scroll-based animation.
 */
function initScrollTrigger(el, config) {
	const target = el.querySelector('.gsap-scroll-trigger-target');
	if (!target) return;

	const stConfig = buildScrollTrigger(el, config.scrollTrigger);

	const tweenVars = {
		duration: config.duration || 1,
		delay: config.delay || 0,
		ease: config.ease || 'power2.out',
		repeat: config.repeat || 0,
		yoyo: !!config.yoyo,
		scrollTrigger: stConfig,
	};

	let animProps = {};

	switch (config.animType) {
		case 'parallax':
			animProps = { y: config.parallaxSpeed || 100 };
			break;
		case 'fade_in':
			animProps = { opacity: 0 };
			gsap.from(target, { ...animProps, ...tweenVars });
			return;
		case 'slide_up':
			animProps = { y: 60, opacity: 0 };
			gsap.from(target, { ...animProps, ...tweenVars });
			return;
		case 'slide_left':
			animProps = { x: 60, opacity: 0 };
			gsap.from(target, { ...animProps, ...tweenVars });
			return;
		case 'zoom_in':
			animProps = { scale: 0.5, opacity: 0 };
			gsap.from(target, { ...animProps, ...tweenVars });
			return;
		case 'custom':
			animProps = buildTransform(config.transform);
			break;
		default:
			animProps = { y: config.parallaxSpeed || 100 };
	}

	gsap.to(target, { ...animProps, ...tweenVars });
}

/**
 * ScrollTo — Smooth scroll button.
 */
function initScrollTo(el, config) {
	const btn = el.querySelector('.gsap-scroll-to-btn');
	if (!btn) return;

	btn.addEventListener('click', () => {
		const targetVal = config.target;
		const scrollTarget = isNaN(targetVal) ? targetVal : parseFloat(targetVal);

		const vars = {
			duration: config.duration || 1,
			ease: config.ease || 'power2.inOut',
			autoKill: config.autoKill !== false,
		};

		if (config.axis === 'x') {
			vars.scrollTo = { x: scrollTarget, offsetX: config.offset || 0 };
		} else {
			vars.scrollTo = { y: scrollTarget, offsetY: config.offset || 0 };
		}

		gsap.to(window, vars);
	});
}

/**
 * SplitText — Character/word/line animation.
 */
function initSplitText(el, config) {
	const target = el.querySelector('.gsap-split-text-target');
	if (!target) return;

	const split = new SplitText(target, {
		type: config.splitType || 'chars',
		mask: config.mask ? 'lines' : undefined,
	});

	const elements = split[config.animateTarget || 'chars'];
	if (!elements || !elements.length) return;

	let fromVars = {};

	switch (config.preset) {
		case 'fade_up':
			fromVars = { y: 30, opacity: 0 };
			break;
		case 'fade_down':
			fromVars = { y: -30, opacity: 0 };
			break;
		case 'fade_left':
			fromVars = { x: 30, opacity: 0 };
			break;
		case 'fade_right':
			fromVars = { x: -30, opacity: 0 };
			break;
		case 'scale_up':
			fromVars = { scale: 0, opacity: 0 };
			break;
		case 'rotate_in':
			fromVars = { rotation: 90, opacity: 0, transformOrigin: '0% 50%' };
			break;
		case 'blur_in':
			fromVars = { opacity: 0, filter: 'blur(10px)' };
			break;
		case 'custom':
			fromVars = buildTransform(config.transform);
			if (config.transform && config.transform.blur) {
				fromVars.filter = `blur(${config.transform.blur}px)`;
			}
			break;
		default:
			fromVars = { y: 30, opacity: 0 };
	}

	const tweenVars = {
		...fromVars,
		duration: config.duration || 1,
		delay: config.delay || 0,
		ease: config.ease || 'power2.out',
		stagger: config.stagger || { each: 0.03, from: 'start' },
		repeat: config.repeat || 0,
		yoyo: !!config.yoyo,
	};

	if (config.scrollTrigger) {
		tweenVars.scrollTrigger = buildScrollTrigger(el, config.scrollTrigger);
	}

	gsap.from(elements, tweenVars);
}

/**
 * ScrambleText — Decode effect.
 */
function initScrambleText(el, config) {
	const target = el.querySelector('.gsap-scramble-text-target');
	if (!target) return;

	const text = target.textContent;

	const tweenVars = {
		duration: config.duration || 2,
		delay: config.delay || 0,
		ease: config.ease || 'none',
		repeat: config.repeat || 0,
		yoyo: !!config.yoyo,
		scrambleText: {
			text: text,
			chars: config.scrambleChars || 'upperCase',
			revealDelay: config.revealDelay || 0,
			speed: config.speed || 1,
		},
	};

	if (config.newClass) tweenVars.scrambleText.newClass = config.newClass;
	if (config.oldClass) tweenVars.scrambleText.oldClass = config.oldClass;

	if (config.scrollTrigger) {
		tweenVars.scrollTrigger = buildScrollTrigger(el, config.scrollTrigger);
	}

	// Clear text first, then animate in
	target.textContent = '';
	gsap.to(target, tweenVars);
}

/**
 * Typewriter — TextPlugin typing effect.
 */
function initTypewriter(el, config) {
	const target = el.querySelector('.gsap-typewriter-target');
	const cursor = el.querySelector('.gsap-typewriter-cursor');
	if (!target) return;

	const tl = gsap.timeline({
		repeat: config.loop ? -1 : 0,
		repeatDelay: config.loopDelay || 1,
	});

	if (config.scrollTrigger) {
		tl.scrollTrigger = ScrollTrigger.create({
			trigger: el,
			...config.scrollTrigger,
			animation: tl,
		});
	}

	target.textContent = '';

	tl.to(target, {
		duration: config.duration || 2,
		delay: config.delay || 0.5,
		ease: config.ease || 'none',
		text: {
			value: config.text,
			delimiter: config.delimiter || '',
		},
	});

	if (config.loop) {
		tl.to(target, {
			duration: config.duration || 2,
			delay: 1,
			ease: config.ease || 'none',
			text: { value: '', delimiter: config.delimiter || '' },
		});
	}

	// Blinking cursor
	if (cursor && config.cursor) {
		gsap.to(cursor, {
			opacity: 0,
			duration: 0.5,
			repeat: -1,
			yoyo: true,
			ease: 'steps(1)',
		});
	}
}

/**
 * DrawSVG — Stroke drawing animation.
 */
function initDrawSVG(el, config) {
	const target = el.querySelector('.gsap-draw-svg-target');
	if (!target) return;

	const shapes = target.querySelectorAll('path, circle, rect, polygon, line, polyline, ellipse');
	if (!shapes.length) return;

	const tweenVars = {
		drawSVG: config.drawFrom || '0%',
		duration: config.duration || 1,
		delay: config.delay || 0,
		ease: config.ease || 'power2.out',
		repeat: config.repeat || 0,
		yoyo: !!config.yoyo,
		stagger: 0.2,
	};

	if (config.scrollTrigger) {
		tweenVars.scrollTrigger = buildScrollTrigger(el, config.scrollTrigger);
	}

	// Set initial state to nothing drawn, then animate to full
	gsap.set(shapes, { drawSVG: config.drawFrom || '0%' });
	gsap.to(shapes, {
		...tweenVars,
		drawSVG: config.drawTo || '100%',
	});
}

/**
 * MorphSVG — Shape morphing.
 */
function initMorphSVG(el, config) {
	const startShape = el.querySelector('#gsap-morph-start') ||
		el.querySelector('.gsap-morph-svg-target svg *:first-child');
	if (!startShape) return;

	const hiddenShapes = el.querySelectorAll('[class^="gsap-morph-shape-"]');
	if (!hiddenShapes.length || !config.shapes || !config.shapes.length) return;

	const tl = gsap.timeline({
		repeat: config.repeat || 0,
		yoyo: !!config.yoyo,
		delay: config.delay || 0,
	});

	if (config.scrollTrigger) {
		tl.scrollTrigger = ScrollTrigger.create({
			trigger: el,
			...buildScrollTrigger(el, config.scrollTrigger),
			animation: tl,
		});
	}

	config.shapes.forEach((shape, i) => {
		const shapeContainer = hiddenShapes[i];
		if (!shapeContainer) return;

		const targetShape = shapeContainer.querySelector('path, circle, rect, polygon, ellipse, polyline, line');
		if (!targetShape) return;

		const morphVars = {
			duration: config.duration || 1,
			ease: config.ease || 'power2.out',
			morphSVG: {
				shape: targetShape,
				type: config.morphType || 'rotational',
				origin: config.origin || '50% 50%',
			},
		};

		if (shape.fill) {
			morphVars.fill = shape.fill;
		}

		tl.to(startShape, morphVars);
	});
}

/**
 * MotionPath — Animate along an SVG path.
 */
function initMotionPath(el, config) {
	const target = el.querySelector('.gsap-motion-path-element');
	if (!target) return;

	const tweenVars = {
		duration: config.duration || 2,
		delay: config.delay || 0,
		ease: config.ease || 'power2.out',
		repeat: config.repeat || 0,
		yoyo: !!config.yoyo,
		motionPath: {
			path: config.pathData,
			autoRotate: config.autoRotate !== false,
			alignOrigin: config.alignOrigin || [0.5, 0.5],
			start: config.start || 0,
			end: config.end || 1,
		},
	};

	if (config.scrollTrigger) {
		tweenVars.scrollTrigger = buildScrollTrigger(el, config.scrollTrigger);
	}

	gsap.to(target, tweenVars);
}

/**
 * Flip — Layout transitions.
 */
function initFlip(el, config) {
	if (config.layoutType === 'grid') {
		initFlipGrid(el, config);
	} else if (config.layoutType === 'toggle') {
		initFlipToggle(el, config);
	} else if (config.layoutType === 'shuffle') {
		initFlipShuffle(el, config);
	}
}

function initFlipGrid(el, config) {
	const filterBtns = el.querySelectorAll('.gsap-flip-filter-btn');
	const items = el.querySelectorAll('.gsap-flip-item');

	filterBtns.forEach((btn) => {
		btn.addEventListener('click', () => {
			const filter = btn.dataset.filter;

			filterBtns.forEach((b) => b.classList.remove('gsap-flip-filter-active'));
			btn.classList.add('gsap-flip-filter-active');

			const state = Flip.getState(items);

			items.forEach((item) => {
				const cat = item.dataset.category;
				if (filter === 'all' || cat === filter) {
					item.style.display = '';
				} else {
					item.style.display = 'none';
				}
			});

			Flip.from(state, {
				duration: config.duration || 0.5,
				ease: config.ease || 'power1.inOut',
				stagger: config.stagger || 0.05,
				absolute: config.absolute !== false,
				scale: !!config.scale,
				onEnter: config.fade ? (elements) =>
					gsap.fromTo(elements, { opacity: 0, scale: 0.8 }, { opacity: 1, scale: 1, duration: config.duration || 0.5 }) : undefined,
				onLeave: config.fade ? (elements) =>
					gsap.to(elements, { opacity: 0, scale: 0.8, duration: config.duration || 0.5 }) : undefined,
			});
		});
	});
}

function initFlipToggle(el, config) {
	const btn = el.querySelector('.gsap-flip-toggle-btn');
	const container = el.querySelector('.gsap-flip-toggle-container');
	const stateA = el.querySelector('.gsap-flip-state-a');
	const stateB = el.querySelector('.gsap-flip-state-b');
	if (!btn || !container || !stateA || !stateB) return;

	btn.addEventListener('click', () => {
		const currentState = container.dataset.state;
		const targets = container.querySelectorAll('*');
		const state = Flip.getState(targets);

		if (currentState === 'a') {
			container.innerHTML = stateB.innerHTML;
			container.dataset.state = 'b';
		} else {
			container.innerHTML = stateA.innerHTML;
			container.dataset.state = 'a';
		}

		Flip.from(state, {
			duration: config.duration || 0.5,
			ease: config.ease || 'power1.inOut',
			absolute: config.absolute !== false,
		});
	});
}

function initFlipShuffle(el, config) {
	const btn = el.querySelector('.gsap-flip-shuffle-btn');
	const grid = el.querySelector('.gsap-flip-grid');
	if (!btn || !grid) return;

	btn.addEventListener('click', () => {
		const items = Array.from(grid.children);
		const state = Flip.getState(items);

		// Fisher-Yates shuffle
		for (let i = items.length - 1; i > 0; i--) {
			const j = Math.floor(Math.random() * (i + 1));
			grid.appendChild(items[j]);
		}

		Flip.from(state, {
			duration: config.duration || 0.5,
			ease: config.ease || 'power1.inOut',
			stagger: config.stagger || 0.05,
			absolute: config.absolute !== false,
		});
	});
}

/**
 * Draggable
 */
function initDraggable(el, config) {
	const target = el.querySelector('.gsap-draggable-target');
	if (!target) return;

	const dragConfig = {
		type: config.type || 'x,y',
		edgeResistance: config.edgeResistance || 0.65,
		lockAxis: !!config.lockAxis,
		inertia: !!config.inertia,
	};

	if (config.bounds) {
		if (config.bounds === 'parent') {
			dragConfig.bounds = target.parentElement;
		} else if (config.bounds === 'window') {
			dragConfig.bounds = window;
		} else {
			dragConfig.bounds = config.bounds;
		}
	}

	if (config.snap) {
		if (config.type === 'rotation') {
			dragConfig.snap = { rotation: (val) => Math.round(val / config.snap) * config.snap };
		} else {
			const snapVal = config.snap;
			dragConfig.snap = {
				x: (val) => Math.round(val / snapVal) * snapVal,
				y: (val) => Math.round(val / snapVal) * snapVal,
			};
		}
	}

	Draggable.create(target, dragConfig);
}

/**
 * ScrollSmoother
 */
function initScrollSmoother(el, config) {
	// ScrollSmoother needs a specific DOM structure.
	// We set data-speed and data-lag attributes on child elements.
	// The actual smoother should be instantiated once per page.
	if (window._gsapScrollSmoother) return;

	window._gsapScrollSmoother = ScrollSmoother.create({
		smooth: config.smooth || 1,
		effects: config.effects !== false,
		normalizeScroll: !!config.normalizeScroll,
		smoothTouch: config.smoothTouch || false,
	});
}

/**
 * Observer — Gesture / scroll event driven animation.
 */
function initObserver(el, config) {
	const target = el.querySelector('.gsap-observer-target');
	if (!target) return;

	const getAnimation = (direction) => {
		const animType = direction === 'up' ? config.animUp : config.animDown;
		const dur = config.duration || 0.6;
		const ease = config.ease || 'power2.out';

		switch (animType) {
			case 'slide_up':
				return { y: -30, duration: dur, ease };
			case 'slide_down':
				return { y: 30, duration: dur, ease };
			case 'slide_left':
				return { x: -30, duration: dur, ease };
			case 'slide_right':
				return { x: 30, duration: dur, ease };
			case 'scale_down':
				return { scale: 0.9, duration: dur, ease };
			case 'scale_up':
				return { scale: 1.1, duration: dur, ease };
			case 'fade_out':
				return { opacity: 0.5, duration: dur, ease };
			case 'fade_in':
				return { opacity: 1, duration: dur, ease };
			case 'rotate_left':
				return { rotation: -15, duration: dur, ease };
			case 'rotate_right':
				return { rotation: 15, duration: dur, ease };
			default:
				return null;
		}
	};

	Observer.create({
		target: el,
		type: (config.eventTypes || ['wheel', 'touch', 'pointer']).join(','),
		tolerance: config.tolerance || 10,
		preventDefault: config.preventDefault !== false,
		onUp: () => {
			const anim = getAnimation('up');
			if (anim) {
				gsap.to(target, anim);
				// Return to original state
				gsap.to(target, { x: 0, y: 0, scale: 1, opacity: 1, rotation: 0, duration: 0.4, delay: 0.3, ease: 'power2.out' });
			}
		},
		onDown: () => {
			const anim = getAnimation('down');
			if (anim) {
				gsap.to(target, anim);
				gsap.to(target, { x: 0, y: 0, scale: 1, opacity: 1, rotation: 0, duration: 0.4, delay: 0.3, ease: 'power2.out' });
			}
		},
	});
}

/**
 * Physics2D — Particle/physics animation.
 */
function initPhysics2D(el, config) {
	const container = el.querySelector('.gsap-physics2d-container');
	const template = el.querySelector('.gsap-physics2d-template');
	if (!container || !template) return;

	const colors = config.colorPalette || ['#ff6b6b', '#6c63ff', '#0ae448', '#ffd93d', '#ff8a5c'];

	const launchParticles = () => {
		// Clear previous
		container.querySelectorAll('.gsap-physics2d-particle').forEach((p) => p.remove());

		const numParticles = config.numParticles || 20;
		const particles = [];

		for (let i = 0; i < numParticles; i++) {
			const p = template.firstElementChild.cloneNode(true);
			p.classList.add('gsap-physics2d-particle');
			p.style.position = 'absolute';
			p.style.left = '50%';
			p.style.bottom = '0';

			if (config.randomizeColors) {
				const color = colors[Math.floor(Math.random() * colors.length)];
				p.style.backgroundColor = color;
			}

			container.appendChild(p);
			particles.push(p);
		}

		const angleMin = config.angleMin || 200;
		const angleMax = config.angleMax || 340;

		gsap.to(particles, {
			physics2D: {
				velocity: config.velocity || 300,
				angle: () => gsap.utils.random(angleMin, angleMax),
				gravity: config.gravity || 500,
				friction: config.friction || 0.02,
			},
			duration: 3,
			opacity: 0,
			delay: 'random(0, 0.2)',
		});
	};

	if (config.trigger === 'click') {
		const btn = el.querySelector('.gsap-physics2d-trigger');
		if (btn) {
			btn.addEventListener('click', launchParticles);
		}
	} else if (config.trigger === 'load') {
		launchParticles();
	} else if (config.trigger === 'scroll') {
		ScrollTrigger.create({
			trigger: el,
			start: 'top 80%',
			onEnter: launchParticles,
			once: true,
		});
	}
}

/* ======================================
 * Universal Injected Animation Handler
 * Picks up data-gsap-anim on ANY Elementor element.
 * ====================================== */

function initInjectedAnimations(container) {
	const root = container || document;
	const elements = root.querySelectorAll('[data-gsap-anim]');

	elements.forEach((el) => {
		if (el.dataset.gsapAnimInit) return;
		el.dataset.gsapAnimInit = '1';

		let config;
		try {
			config = JSON.parse(el.dataset.gsapAnim);
		} catch (e) {
			console.warn('GSAP Elementor: invalid injected config', e);
			return;
		}

		// If SplitText is enabled, delegate to the split handler
		if (config.splitText) {
			initInjectedSplitText(el, config);
			return;
		}

		// Build the "from" vars based on preset or custom
		const fromVars = buildPresetFromVars(config);
		if (!fromVars) return;

		// Build tween options
		const tweenVars = {
			...fromVars,
			duration: config.duration || 1,
			delay: config.delay || 0,
			ease: config.ease || 'power2.out',
			repeat: config.repeat || 0,
			yoyo: !!config.yoyo,
		};

		// Stagger: animate children instead of the element itself
		if (config.stagger) {
			const targets = el.querySelectorAll(config.stagger.target || '> *');
			if (targets.length) {
				tweenVars.stagger = {
					each: config.stagger.each || 0.15,
					from: config.stagger.from || 'start',
				};

				if (config.scrollTrigger) {
					tweenVars.scrollTrigger = buildScrollTrigger(el, config.scrollTrigger);
				}

				gsap.from(targets, tweenVars);
				return;
			}
		}

		// ScrollTrigger
		if (config.scrollTrigger) {
			tweenVars.scrollTrigger = buildScrollTrigger(el, config.scrollTrigger);
		}

		gsap.from(el, tweenVars);
	});
}

/**
 * Build "from" variables from a preset name or custom values.
 */
function buildPresetFromVars(config) {
	const preset = config.preset || 'fade_up';

	switch (preset) {
		case 'fade_up':
			return { y: 40, opacity: 0 };
		case 'fade_down':
			return { y: -40, opacity: 0 };
		case 'fade_left':
			return { x: 40, opacity: 0 };
		case 'fade_right':
			return { x: -40, opacity: 0 };
		case 'zoom_in':
			return { scale: 0.6, opacity: 0 };
		case 'zoom_out':
			return { scale: 1.4, opacity: 0 };
		case 'rotate_in':
			return { rotation: 15, opacity: 0, transformOrigin: 'center center' };
		case 'flip_x':
			return { rotationX: 90, opacity: 0, transformPerspective: 800 };
		case 'flip_y':
			return { rotationY: 90, opacity: 0, transformPerspective: 800 };
		case 'blur_in':
			return { opacity: 0, filter: 'blur(12px)' };
		case 'bounce_in':
			return { scale: 0.3, opacity: 0, ease: 'bounce.out' };
		case 'slide_masked':
			return { y: '100%', opacity: 0, clipPath: 'inset(100% 0 0 0)' };
		case 'custom':
			return buildCustomFromVars(config.custom);
		default:
			return { y: 40, opacity: 0 };
	}
}

/**
 * Build custom "from" vars from the custom config object.
 */
function buildCustomFromVars(custom) {
	if (!custom) return { opacity: 0 };

	const vars = {};
	if (custom.x !== 0) vars.x = custom.x;
	if (custom.y !== 0) vars.y = custom.y;
	if (custom.rotation !== 0) vars.rotation = custom.rotation;
	if (custom.scaleX !== undefined && custom.scaleX !== 1) vars.scaleX = custom.scaleX;
	if (custom.scaleY !== undefined && custom.scaleY !== 1) vars.scaleY = custom.scaleY;
	if (custom.opacity !== undefined) vars.opacity = custom.opacity;
	if (custom.blur && custom.blur > 0) vars.filter = `blur(${custom.blur}px)`;
	if (custom.skewX !== 0) vars.skewX = custom.skewX;
	if (custom.skewY !== 0) vars.skewY = custom.skewY;

	// Default: at least fade if nothing else set
	if (Object.keys(vars).length === 0) {
		vars.opacity = 0;
	}

	return vars;
}

/**
 * Injected SplitText handler — finds text inside any element and splits it.
 */
function initInjectedSplitText(el, config) {
	const splitConfig = config.splitText;

	// Find the text element
	let textEl;
	if (splitConfig.selector) {
		textEl = el.querySelector(splitConfig.selector);
	}
	if (!textEl) {
		textEl = el.querySelector('h1, h2, h3, h4, h5, h6, p, .elementor-heading-title, .elementor-widget-container');
	}
	if (!textEl) return;

	const split = new SplitText(textEl, {
		type: splitConfig.type || 'chars',
	});

	const targets = split[splitConfig.animate || 'chars'];
	if (!targets || !targets.length) return;

	const fromVars = buildPresetFromVars(config);
	if (!fromVars) return;

	const tweenVars = {
		...fromVars,
		duration: config.duration || 1,
		delay: config.delay || 0,
		ease: config.ease || 'power2.out',
		stagger: {
			each: splitConfig.stagger || 0.03,
			from: 'start',
		},
		repeat: config.repeat || 0,
		yoyo: !!config.yoyo,
	};

	if (config.scrollTrigger) {
		tweenVars.scrollTrigger = buildScrollTrigger(el, config.scrollTrigger);
	}

	gsap.from(targets, tweenVars);
}

/* ======================================
 * Helpers
 * ====================================== */

function buildTransform(t) {
	if (!t) return {};
	const result = {};
	if (t.x !== undefined && t.x !== 0) result.x = t.x;
	if (t.y !== undefined && t.y !== 0) result.y = t.y;
	if (t.rotation !== undefined && t.rotation !== 0) result.rotation = t.rotation;
	if (t.scale !== undefined && t.scale !== 1) result.scale = t.scale;
	if (t.opacity !== undefined && t.opacity !== 1) result.opacity = t.opacity;
	return result;
}

function buildScrollTrigger(el, stConfig) {
	if (!stConfig) return undefined;
	return {
		trigger: el,
		start: stConfig.start || 'top 80%',
		end: stConfig.end || 'bottom 20%',
		scrub: stConfig.scrub || false,
		pin: stConfig.pin || false,
		pinSpacing: stConfig.pinSpacing !== false,
		markers: stConfig.markers || false,
		toggleActions: stConfig.toggleActions || 'play none none none',
	};
}

/* ======================================
 * Bootstrap
 * ====================================== */

/**
 * Combined init — standalone widgets + injected animations.
 */
function initAll(container) {
	initGsapWidgets(container);
	initInjectedAnimations(container);
}

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => {
	initAll();
});

// Elementor frontend hooks (for live preview in editor)
// Must wait until elementorFrontend is available — it loads after our script.
function registerElementorHooks() {
	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', (scope) => {
			initAll(scope[0] || scope);
		});
	}
}

// Try immediately, and also on the Elementor init event
registerElementorHooks();
document.addEventListener('elementor/frontend/init', registerElementorHooks);

// Export for editor use
window.gsapElementor = {
	init: initAll,
	initWidgets: initGsapWidgets,
	initInjected: initInjectedAnimations,
};
