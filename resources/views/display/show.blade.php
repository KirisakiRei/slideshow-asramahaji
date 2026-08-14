<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Signage</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-page: #f7f1e8;
            --bg-page-soft: #fbf8f3;

            --surface-primary: #fffdf9;
            --surface-secondary: #f5ede3;
            --surface-muted: #eee3d5;
            --surface-highlight: #faf4eb;

            --accent-primary: #b58b57;
            --accent-primary-hover: #9e7648;
            --accent-soft: #d9c09d;
            --accent-pale: #eee0cc;

            --text-primary: #2f281f;
            --text-secondary: #625749;
            --text-muted: #968878;
            --text-disabled: #b9ac9d;
            --text-on-accent: #fffaf4;

            --border-soft: rgba(143, 111, 74, 0.13);
            --border-medium: rgba(143, 111, 74, 0.22);
            --border-strong: rgba(143, 111, 74, 0.34);

            --placeholder-bg: #f0e8de;
            --placeholder-bg-light: #f6f0e8;
            --placeholder-icon: #b7a691;

            --shadow-xs: 0 2px 6px rgba(76, 57, 37, 0.04);
            --shadow-sm: 0 6px 18px rgba(76, 57, 37, 0.06);
            --shadow-md: 0 14px 34px rgba(76, 57, 37, 0.08);
            --shadow-lg: 0 24px 60px rgba(76, 57, 37, 0.10);

            --radius-xs: 12px;
            --radius-sm: 18px;
            --radius-md: 28px;
            --radius-lg: 40px;
            --radius-xl: 52px;
            --radius-pill: 999px;

            --space-1: 8px;
            --space-2: 12px;
            --space-3: 16px;
            --space-4: 24px;
            --space-5: 32px;
            --space-6: 40px;
            --space-7: 48px;
            --space-8: 64px;
            --space-9: 80px;
            --space-10: 96px;
            --space-11: 120px;

            --page-padding-x: 36px;
            --page-padding-y: 42px;
            --content-gap: 32px;

            --header-bg-start: #fffdf9;
            --header-bg-middle: #faf5ed;
            --header-bg-end: #f3e7d7;
            --header-pattern: rgba(142, 107, 68, 0.18);
            --header-border: rgba(143, 111, 74, 0.12);
            --header-arc: rgba(181, 139, 87, 0.17);
            --header-arc-soft: rgba(216, 190, 156, 0.10);
            --header-arc-faint: rgba(226, 207, 183, 0.07);
            --header-arch-border: rgba(181, 139, 87, 0.11);
            --header-arch-inner: rgba(181, 139, 87, 0.08);

            --ease-premium: cubic-bezier(0.22, 1, 0.36, 1);
            --transition-fast: 180ms var(--ease-premium);
            --transition-normal: 320ms var(--ease-premium);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        body {
            background: #ded8d0;
            color: var(--text-primary);
            font-family: "Inter", "Manrope", "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-synthesis: none;
            -webkit-font-smoothing: antialiased;
            text-rendering: geometricPrecision;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signage {
            position: relative;
            width: 1080px;
            height: 1920px;
            flex-shrink: 0;
            transform-origin: center center;
            overflow: hidden;
            display: grid;
            grid-template-rows: auto minmax(0, 1.4fr) minmax(0, 1fr) auto auto;
            gap: var(--content-gap);
            padding: var(--page-padding-y) var(--page-padding-x) 0;
            background:
                radial-gradient(circle at 90% 1%, rgba(202, 171, 129, 0.24), transparent 17%),
                linear-gradient(180deg, #faf7f1 0%, #f7f1e8 65%, #f4ecdf 100%);
        }

        .signage::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0.18;
            background-image: radial-gradient(rgba(115, 88, 58, 0.16) 0.6px, transparent 0.6px);
            background-size: 12px 12px;
            mask-image: linear-gradient(to bottom, black, transparent 70%);
            -webkit-mask-image: linear-gradient(to bottom, black, transparent 70%);
        }

        /* ------------------------------------------------------------------
           Header — layered ornament system (TITLE > LOGO > LABEL > ORNAMENT)
           Canvas: 1080×1920. Ornament concentrated on right ~40%.
           ------------------------------------------------------------------ */
        .signage-header {
            position: relative;
            z-index: 2;
            isolation: isolate;
            overflow: hidden;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            column-gap: 36px;
            /* Full-bleed top; soft dissolve into page below */
            margin:
                calc(var(--page-padding-y) * -1)
                calc(var(--page-padding-x) * -1)
                -8px;
            padding:
                36px
                var(--page-padding-x)
                48px;
            background:
                radial-gradient(
                    ellipse 55% 90% at 92% 18%,
                    rgba(191, 151, 101, 0.14) 0%,
                    rgba(191, 151, 101, 0.05) 38%,
                    transparent 68%
                ),
                linear-gradient(
                    180deg,
                    #fffdf9 0%,
                    #faf6ef 38%,
                    #f7f1e8 62%,
                    rgba(247, 241, 232, 0.35) 82%,
                    transparent 100%
                );
        }

        /* Concentric arcs — crop into top-right, frame the logo zone */
        .signage-header::before {
            content: "";
            position: absolute;
            width: 520px;
            aspect-ratio: 1;
            top: -280px;
            right: -160px;
            border-radius: 50%;
            border: 12px solid rgba(181, 139, 87, 0.14);
            box-shadow:
                0 0 0 28px rgba(216, 190, 156, 0.09),
                0 0 0 58px rgba(226, 207, 183, 0.06);
            z-index: 0;
            pointer-events: none;
        }

        /* Geometric line pattern — right side only, soft mask */
        .signage-header::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 46%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.10;
            background-image:
                linear-gradient(30deg, var(--header-pattern) 1px, transparent 1px),
                linear-gradient(150deg, var(--header-pattern) 1px, transparent 1px);
            background-size: 32px 56px, 32px 56px;
            mask-image: radial-gradient(
                ellipse at 100% 28%,
                black 0%,
                rgba(0, 0, 0, 0.55) 36%,
                transparent 70%
            );
            -webkit-mask-image: radial-gradient(
                ellipse at 100% 28%,
                black 0%,
                rgba(0, 0, 0, 0.55) 36%,
                transparent 70%
            );
        }

        /* Architectural arch — soft frame behind logo, non-literal */
        .header-arch {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-42%);
            width: 280px;
            height: 340px;
            border: 1.5px solid rgba(181, 139, 87, 0.10);
            border-radius: 50% 50% 18% 18% / 36% 36% 10% 10%;
            pointer-events: none;
            z-index: 0;
        }

        .header-arch::before {
            content: "";
            position: absolute;
            inset: 18px;
            border: 1px solid rgba(181, 139, 87, 0.07);
            border-radius: inherit;
        }

        .header-content,
        .header-brand {
            position: relative;
            z-index: 2;
        }

        .header-content {
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 8px 22px;
            border-radius: var(--radius-pill);
            background: linear-gradient(135deg, #c3a27a, #ab8456);
            color: var(--text-on-accent);
            font-size: 16px;
            font-weight: 650;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .signage-title {
            max-width: 640px;
            margin: 18px 0 0;
            color: var(--text-primary);
            font-size: 52px;
            font-weight: 730;
            line-height: 1.02;
            letter-spacing: -0.04em;
            overflow-wrap: break-word;
            text-wrap: balance;
        }

        /* Accent divider: bar + diamond */
        .header-accent {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .header-accent::before {
            content: "";
            width: 88px;
            height: 4px;
            border-radius: var(--radius-pill);
            background: linear-gradient(90deg, #a98050, #d5b88f);
        }

        .header-accent::after {
            content: "";
            width: 8px;
            height: 8px;
            transform: rotate(45deg);
            border: 1.5px solid rgba(181, 139, 87, 0.55);
            flex-shrink: 0;
        }

        /* Brand block: logo + institution text (right side of header) */
        .header-brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 16px;
            max-width: 420px;
            min-width: 0;
        }

        /* Logo — natural aspect, no card, no forced square */
        .logo-placeholder {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: auto;
            flex-shrink: 0;
            color: var(--placeholder-icon);
        }

        .logo-placeholder img {
            display: block;
            width: auto;
            max-width: 110px;
            max-height: 96px;
            height: auto;
            object-fit: contain;
        }

        .logo-mark {
            width: 56px;
            height: 56px;
            opacity: 0.75;
        }

        .logo-label {
            font-size: 16px;
            font-weight: 650;
            letter-spacing: 0.28em;
            text-indent: 0.28em;
        }

        .logo-text {
            min-width: 0;
            color: var(--text-primary);
            font-size: 22px;
            font-weight: 750;
            line-height: 1.15;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            white-space: pre-line;
        }

        .hero-card {
            position: relative;
            z-index: 2;
            min-height: 0;
            display: grid;
            grid-template-rows: minmax(0, 1fr) auto;
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-medium);
            background: var(--surface-primary);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .hero-media {
            min-height: 0;
            margin: 14px;
            border-radius: calc(var(--radius-xl) - 14px);
            overflow: hidden;
            position: relative;
            background: linear-gradient(145deg, #f3ebe2, #ede4d9);
        }

        .slideshow-frame {
            position: absolute;
            inset: 0;
        }

        .slideshow-frame .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            will-change: transform, opacity;
        }

        .slideshow-frame .slide img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: center;
        }

        .slideshow-frame .slide video {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
            background: #000;
        }

        /* Fill modes: contain shows the whole photo with a blurred backdrop */
        .slideshow-frame .slide--contain img {
            object-fit: contain;
            position: relative;
            z-index: 1;
        }

        .slideshow-frame .slide-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: blur(28px) saturate(1.15);
            transform: scale(1.15);
        }

        .media-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 24px;
            color: var(--placeholder-icon);
            text-align: center;
            background:
                radial-gradient(circle at center, rgba(255, 255, 255, 0.46), transparent 40%),
                linear-gradient(145deg, #f3ebe2, #ede4d9);
        }

        .media-placeholder svg {
            width: 64px;
            height: auto;
            opacity: 0.9;
        }

        .media-placeholder-label {
            font-size: 26px;
            font-weight: 600;
            letter-spacing: 0.26em;
            text-indent: 0.26em;
        }

        .error-message {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 28px;
            font-weight: 600;
            line-height: 1.4;
            background:
                radial-gradient(circle at center, rgba(255, 255, 255, 0.46), transparent 40%),
                linear-gradient(145deg, #f3ebe2, #ede4d9);
        }

        .carousel-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            min-height: 64px;
            padding: 0 24px;
            overflow: hidden;
        }

        .carousel-dot {
            width: 14px;
            aspect-ratio: 1;
            flex-shrink: 0;
            border: none;
            border-radius: 50%;
            background: #ddd5cb;
            transition: transform var(--transition-fast), background var(--transition-fast);
        }

        .carousel-dot.active {
            background: var(--accent-primary);
            transform: scale(1.12);
        }

        .signage-content {
            position: relative;
            z-index: 2;
            min-height: 0;
            display: grid;
            grid-template-columns: minmax(260px, 0.27fr) minmax(0, 1fr);
            gap: 34px;
        }

        .facilities {
            min-height: 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .section-chip {
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: 14px;
            padding: 12px 22px;
            border-radius: var(--radius-pill);
            background: linear-gradient(135deg, #b59469, #9f774d);
            color: var(--text-on-accent);
            font-size: 16px;
            font-weight: 650;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .facility-list {
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-rows: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .facility-card {
            min-height: 0;
            display: grid;
            grid-template-rows: minmax(0, 1fr) auto;
            overflow: hidden;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-medium);
            background: var(--surface-primary);
            box-shadow: var(--shadow-sm);
        }

        /* Media fills the entire card when caption is hidden */
        .facility-card--full {
            grid-template-rows: minmax(0, 1fr);
        }

        .facility-media {
            min-height: 0;
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, #f3ebe2, #ede4d9);
            color: var(--placeholder-icon);
        }

        .facility-media-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .facility-media-placeholder svg {
            width: 28px;
            height: auto;
        }

        .facility-media-placeholder span {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.22em;
            text-indent: 0.22em;
        }

        .facility-caption {
            padding: 12px 14px;
            background: linear-gradient(180deg, #f1e3d0, #ebdbc6);
            color: var(--text-primary);
            font-size: 17px;
            font-weight: 620;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mini-layer {
            position: absolute;
            inset: 0;
        }

        .mini-layer img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .mini-layer video {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #000;
            display: block;
        }

        .mini-layer--contain img {
            object-fit: contain;
            position: relative;
            z-index: 1;
        }

        .mini-slide-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: blur(18px) saturate(1.15);
            transform: scale(1.2);
        }

        .event-card {
            min-width: 0;
            min-height: 0;
            padding: 28px;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
            gap: 24px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-soft);
            background: linear-gradient(145deg, rgba(255, 253, 249, 0.9), rgba(248, 241, 232, 0.9));
            box-shadow: var(--shadow-md);
        }

        .event-header {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
            gap: 32px;
        }

        .event-label {
            display: inline-flex;
            align-items: center;
            color: var(--accent-primary);
            font-size: 16px;
            font-weight: 650;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .event-title {
            margin: 18px 0 0;
            color: var(--text-primary);
            font-size: 34px;
            line-height: 1.05;
            font-weight: 720;
            letter-spacing: -0.032em;
            overflow-wrap: break-word;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px 22px;
            margin-top: 22px;
            color: var(--text-secondary);
            font-size: 15px;
            font-weight: 500;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .meta-item svg {
            width: 18px;
            height: auto;
            flex-shrink: 0;
            color: var(--accent-primary);
        }

        .meta-item span {
            overflow-wrap: break-word;
        }

        .meta-divider {
            width: 1px;
            height: 1.25em;
            background: var(--border-strong);
            flex-shrink: 0;
        }

        .event-logo {
            width: 120px;
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: var(--placeholder-icon);
        }

        .event-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 16px;
            display: block;
        }

        .event-logo .logo-mark {
            width: 42%;
        }

        .event-logo .logo-label {
            font-size: 14px;
            letter-spacing: 0.28em;
            text-indent: 0.28em;
        }

        .event-media {
            min-height: 0;
            position: relative;
            overflow: hidden;
            border-radius: calc(var(--radius-lg) - 12px);
            background: linear-gradient(145deg, #f3ebe2, #ede4d9);
        }

        .signage-footer {
            position: relative;
            z-index: 2;
            margin-left: calc(var(--page-padding-x) * -1);
            margin-right: calc(var(--page-padding-x) * -1);
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 38px;
            padding: 36px var(--page-padding-x);
            border-top: 1px solid var(--border-medium);
            background:
                radial-gradient(circle at 10% 100%, rgba(190, 152, 105, 0.12), transparent 35%),
                linear-gradient(90deg, #f2e4d1, #f7efe4);
        }

        .footer-info {
            min-width: 0;
        }

        .footer-title {
            color: var(--text-primary);
            font-size: 34px;
            font-weight: 720;
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .footer-subtitle {
            margin-top: 10px;
            color: var(--text-secondary);
            font-size: 18px;
            font-weight: 620;
        }

        .footer-support {
            margin-top: 14px;
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 500;
            line-height: 1.4;
            max-width: 60ch;
        }

        .clock-block {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            align-items: center;
            gap: 18px;
            padding-left: 30px;
            border-left: 1px dashed var(--border-strong);
        }

        .clock-icon {
            width: 44px;
            height: auto;
            color: var(--accent-primary);
            opacity: 0.9;
        }

        .clock-time-row {
            display: flex;
            align-items: baseline;
            gap: 14px;
        }

        .clock-time {
            color: var(--text-primary);
            font-size: 48px;
            line-height: 1;
            font-weight: 680;
            letter-spacing: -0.04em;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .clock-wib {
            color: var(--accent-primary);
            font-size: 18px;
            font-weight: 650;
            letter-spacing: 0.12em;
            white-space: nowrap;
        }

        .clock-date {
            margin-top: 8px;
            color: var(--text-secondary);
            font-size: 16px;
            font-weight: 550;
        }

        /* Preview mode badge — only rendered on /display/{id} */
        .preview-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            z-index: 30;
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            background: rgba(47, 40, 31, 0.82);
            color: #f7f1e8;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.03em;
            box-shadow: var(--shadow-sm);
        }

        .marquee {
            position: relative;
            z-index: 2;
            margin-left: calc(var(--page-padding-x) * -1);
            margin-right: calc(var(--page-padding-x) * -1);
            margin-top: calc(var(--content-gap) * -1);
            height: 56px;
            overflow: hidden;
            background: linear-gradient(90deg, #3a3027, #2f281f 60%, #3a3027);
            color: #f7f1e8;
        }

        .marquee-viewport {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .marquee-layer {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: max-content;
            display: flex;
            align-items: center;
            will-change: transform;
            animation: marqueeScroll linear infinite;
        }

        .marquee-item {
            display: inline-flex;
            align-items: center;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        .marquee-text {
            margin-right: 48px;
        }

        .marquee-sep {
            color: var(--accent-soft);
            font-size: 15px;
            margin-right: 48px;
        }

        @keyframes marqueeScroll {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-50%);
            }
        }

        .slide-enter {
            animation: slideFadeIn 700ms var(--ease-premium) both;
        }

        @keyframes slideFadeIn {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>
</head>
<body>
    <div class="signage" id="signage" data-status-hash="{{ $statusHash }}">
        @if(!empty($previewGroupName))
            <div class="preview-badge">Preview: {{ $previewGroupName }}</div>
        @endif
        <!-- Header -->
        <header class="signage-header slide-enter">
            <div class="header-arch" aria-hidden="true"></div>

            <div class="header-content">
                <span class="eyebrow">{{ $config['eyebrow'] ?: 'Event Saat Ini' }}</span>
                <h1 class="signage-title">{{ $config['title'] ?: 'Event Title' }}</h1>
                <div class="header-accent" aria-hidden="true"></div>
            </div>

            @php
                $logoText = trim((string) ($config['logo_text'] ?? ''));
            @endphp
            <div class="header-brand">
                <div class="logo-placeholder">
                    @if(!empty($config['logo']))
                        <img src="{{ asset('storage/' . $config['logo']) }}" alt="Logo">
                    @else
                        <svg class="logo-mark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21h18M4 21V10m5 11V10m6 11V10m5 11V10M2 10l10-7 10 7"/>
                        </svg>
                        @if($logoText === '')
                            <span class="logo-label">LOGO</span>
                        @endif
                    @endif
                </div>
                @if($logoText !== '')
                    <div class="logo-text">{{ $logoText }}</div>
                @endif
            </div>
        </header>

        <!-- Hero Slideshow -->
        <section class="hero-card slide-enter">
            <div class="hero-media">
                @if($error)
                    <div class="error-message">{{ $error }}</div>
                @else
                    <div class="slideshow-frame" id="slideshow-frame">
                        <div class="slide" id="slide-a"></div>
                        <div class="slide" id="slide-b"></div>
                    </div>
                @endif
            </div>
            @if(!$error)
                <div class="carousel-dots" id="carousel-dots">
                    @foreach(array_slice($slides, 0, 12) as $i => $slide)
                        <span class="carousel-dot {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"></span>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Lower Content -->
        <section class="signage-content slide-enter">
            <!-- Facility Sidebar -->
            <div class="facilities">
                <span class="section-chip">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18M4 21V10m5 11V10m6 11V10m5 11V10M2 10l10-7 10 7"/>
                    </svg>
                    {{ $config['section_chip'] ?: 'Fasilitas' }}
                </span>
                @php
                    $showFacilityCaptions = (bool) ($config['show_facility_captions'] ?? true);
                @endphp
                <div class="facility-list">
                    @forelse($facilitySlots as $slot => $slotData)
                        @php
                            $facilityCaption = trim((string) ($slotData['facility']->caption ?? ''));
                            $showThisCaption = $showFacilityCaptions && $facilityCaption !== '';
                        @endphp
                        <div class="facility-card {{ $showThisCaption ? '' : 'facility-card--full' }}">
                            <div class="facility-media mini-slideshow" data-slides="{{ json_encode($slotData['slides']) }}">
                                @if(empty($slotData['slides']))
                                    <div class="facility-media-placeholder">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>IMAGE</span>
                                    </div>
                                @endif
                            </div>
                            @if($showThisCaption)
                                <div class="facility-caption">{{ $facilityCaption }}</div>
                            @endif
                        </div>
                    @empty
                        @for($slot = 1; $slot <= 3; $slot++)
                            <div class="facility-card facility-card--full">
                                <div class="facility-media">
                                    <div class="facility-media-placeholder">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>IMAGE</span>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    @endforelse
                </div>
            </div>

            <!-- Next Event Card -->
            <div class="event-card">
                <div class="event-header">
                    <div>
                        <span class="event-label">{{ $config['next_event_label'] ?: 'Event Selanjutnya' }}</span>
                        <h2 class="event-title">{{ $config['next_event_title'] ?: 'Event Berikutnya' }}</h2>
                        <div class="event-meta">
                            @php
                                $meta = [
                                    'organizer' => ['penyelenggara', '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
                                    'date' => ['tanggal', '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                                    'time' => ['waktu', '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>'],
                                    'location' => ['lokasi', '<path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>'],
                                    'category' => ['kategori', '<path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><circle cx="7" cy="7" r="1"/>'],
                                ];
                                $items = collect($meta)->filter(fn ($v, $k) => !empty($config['next_event_' . $k]));
                            @endphp
                            @foreach($items as $key => [$label, $icon])
                                @if(!$loop->first)
                                    <span class="meta-divider"></span>
                                @endif
                                <span class="meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                                    <span>{{ $config['next_event_' . $key] }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="event-logo">
                        @if(!empty($config['logo']))
                            <img src="{{ asset('storage/' . $config['logo']) }}" alt="Logo">
                        @else
                            <svg class="logo-mark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18M4 21V10m5 11V10m6 11V10m5 11V10M2 10l10-7 10 7"/>
                            </svg>
                            <span class="logo-label">LOGO</span>
                        @endif
                    </div>
                </div>
                <div class="event-media mini-slideshow" data-slides="{{ json_encode($eventSlides) }}">
                    @if(empty($eventSlides))
                        <div class="media-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="media-placeholder-label">PHOTO</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="signage-footer slide-enter">
            <div class="footer-info">
                @if($config['footer_title'])
                    <div class="footer-title">{{ $config['footer_title'] }}</div>
                @endif
                @if($config['footer_subtitle'])
                    <div class="footer-subtitle">{{ $config['footer_subtitle'] }}</div>
                @endif
                @if($config['footer_support'])
                    <div class="footer-support">{{ $config['footer_support'] }}</div>
                @endif
            </div>
            <div class="clock-block">
                <svg class="clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 3"/>
                    <path d="M3 12h2m14 0h2M12 3v2m0 14v2"/>
                </svg>
                <div>
                    <div class="clock-time-row">
                        <span class="clock-time" id="clock-time">--:--:--</span>
                        <span class="clock-wib">WIB</span>
                    </div>
                    <div class="clock-date" id="clock-date">--</div>
                </div>
            </div>
        </footer>

        @if($runningTexts->isNotEmpty())
            <div class="marquee" id="marquee"
                 data-items="{{ $runningTexts->map(fn ($t) => $t->text)->values()->toJson() }}">
                <div class="marquee-viewport">
                    <div class="marquee-layer" id="marquee-track"></div>
                </div>
            </div>
        @endif
    </div>

    <script>
        (function() {
            // ------------------------------------------------------------------
            // Display runtime guards — keep kiosk alive even if one subsystem fails
            // ------------------------------------------------------------------
            function safeCall(label, fn) {
                try {
                    return fn();
                } catch (err) {
                    if (typeof console !== 'undefined' && console.error) {
                        console.error('[display] ' + label, err);
                    }
                    return null;
                }
            }

            // ------------------------------------------------------------------
            // Recovery helpers — never let a persistent failure turn the kiosk
            // into a reload storm.
            // ------------------------------------------------------------------
            const RELOAD_MIN_GAP_MS = 30000;   // at most one reload per 30s
            const RELOAD_MAX_ATTEMPTS = 5;     // per page lifetime
            const IMG_LOAD_STALL_MS = 15000;   // photo hang -> skip / recover
            const VIDEO_LOAD_STALL_MS = 20000; // video hang -> skip / recover
            let lastReloadAt = 0;
            let reloadAttempts = 0;
            function scheduleReload(delayMs) {
                const now = Date.now();
                if (reloadAttempts >= RELOAD_MAX_ATTEMPTS) return;
                if (now - lastReloadAt < RELOAD_MIN_GAP_MS) return;
                reloadAttempts++;
                lastReloadAt = now;
                setTimeout(function() { location.reload(); }, delayMs || 0);
            }

            // Global guards: an uncaught script error usually means the
            // slideshow engine died. Log it, then attempt one rate-limited
            // reload. Resource errors (broken img/video) are ignored here —
            // they carry their own onerror recovery.
            window.addEventListener('error', function(event) {
                if (event && event.target && event.target !== window) return;
                if (typeof console !== 'undefined' && console.error) {
                    console.error('[display] uncaught error', event.error || event.message);
                }
                scheduleReload(3000);
            });
            window.addEventListener('unhandledrejection', function(event) {
                if (typeof console !== 'undefined' && console.error) {
                    console.error('[display] unhandled rejection', event.reason);
                }
                scheduleReload(3000);
            });

            // Soft recovery: if the page is in a fatal error state, auto-reload.
            @if(!empty($error) && str_contains((string) $error, 'sementara tidak tersedia'))
            setTimeout(function() { scheduleReload(0); }, 15000);
            @endif

            // Scale the fixed 1080x1920 canvas to always fit the viewport.
            const signage = document.getElementById('signage');
            function fitDisplay() {
                if (!signage) return;
                const scale = Math.min(window.innerWidth / 1080, window.innerHeight / 1920);
                signage.style.transform = 'scale(' + scale + ')';
            }
            window.addEventListener('resize', fitDisplay);
            window.addEventListener('orientationchange', fitDisplay);
            safeCall('fitDisplay', fitDisplay);

            // Running text marquee
            safeCall('marquee', function() {
                const marquee = document.getElementById('marquee');
                if (!marquee) return;

                let texts = [];
                try {
                    texts = JSON.parse(marquee.dataset.items || '[]');
                } catch (e) {
                    texts = [];
                }
                if (!Array.isArray(texts) || !texts.length) return;

                const track = document.getElementById('marquee-track');
                if (!track || !track.parentElement) return;

                function makeItem(text) {
                    const item = document.createElement('span');
                    item.className = 'marquee-item';
                    const txt = document.createElement('span');
                    txt.className = 'marquee-text';
                    txt.textContent = String(text == null ? '' : text);
                    const sep = document.createElement('span');
                    sep.className = 'marquee-sep';
                    sep.textContent = '\u2022';
                    item.appendChild(txt);
                    item.appendChild(sep);
                    return item;
                }

                // One continuous stream: all texts in order, repeated enough to
                // always fill the viewport with an even repeat count so the
                // -50% translate loops back seamlessly.
                const sequence = texts.map(makeItem);
                sequence.forEach(function(item) { track.appendChild(item); });
                const sequenceWidth = track.scrollWidth || 1;
                const viewportW = track.parentElement.clientWidth || 1;
                let repeats = 1;
                while (sequenceWidth * repeats < viewportW) repeats++;
                if (repeats % 2 !== 0) repeats++;

                for (let i = 1; i < repeats; i++) {
                    sequence.forEach(function(item) { track.appendChild(item.cloneNode(true)); });
                }

                // Constant speed: 60px per second.
                track.style.animationDuration = (sequenceWidth * repeats / 2 / 60) + 's';
            });

            // Real-time clock
            const clockOffsetSec = {{ (int) ($config['clock_offset'] ?? 0) }};
            function tickClock() {
                try {
                    const now = new Date(Date.now() + clockOffsetSec * 1000);
                    const time = document.getElementById('clock-time');
                    const date = document.getElementById('clock-date');
                    if (time) {
                        time.textContent = now.toLocaleTimeString('id-ID', {
                            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
                        });
                    }
                    if (date) {
                        date.textContent = now.toLocaleDateString('id-ID', {
                            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
                        });
                    }
                } catch (e) {}
            }
            tickClock();
            setInterval(tickClock, 1000);

            // Auto-refresh: reload when any display content changes upstream.
            const statusHash = signage ? signage.dataset.statusHash : '';
            let statusFailCount = 0;
            function checkStatus() {
                if (!statusHash || statusHash.indexOf('error') === 0 || statusHash.indexOf('fallback') === 0) {
                    // Error/fallback hashes: soft reload periodically to recover.
                    statusFailCount++;
                    if (statusFailCount >= 6) {
                        scheduleReload(0);
                    }
                    return;
                }
                fetch('/display/status', { cache: 'no-store' })
                    .then(function(response) {
                        if (!response.ok) throw new Error('status ' + response.status);
                        return response.json();
                    })
                    .then(function(data) {
                        statusFailCount = 0;
                        if (!data || !data.hash) return;
                        // Ignore transient error hashes from the status endpoint.
                        if (data.hash === 'error' || data.hash === 'unavailable') return;
                        if (data.hash !== statusHash) {
                            location.reload();
                        }
                    })
                    .catch(function() {
                        statusFailCount++;
                        // After repeated poll failures, rate-limited reload.
                        if (statusFailCount >= 12) {
                            scheduleReload(0);
                        }
                    });
            }
            setInterval(checkStatus, 5000);
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) checkStatus();
            });

            // Shared transition effects for main + mini slideshows
            const TRANSITIONS = {
                'fade': {
                    enter: { opacity: '0', transform: 'none' },
                    active: { opacity: '1', transform: 'none' },
                    exit: { opacity: '0', transform: 'none' },
                },
                'slide-left': {
                    enter: { opacity: '1', transform: 'translateX(100%)' },
                    active: { opacity: '1', transform: 'translateX(0)' },
                    exit: { opacity: '1', transform: 'translateX(-100%)' },
                },
                'slide-right': {
                    enter: { opacity: '1', transform: 'translateX(-100%)' },
                    active: { opacity: '1', transform: 'translateX(0)' },
                    exit: { opacity: '1', transform: 'translateX(100%)' },
                },
                'slide-up': {
                    enter: { opacity: '1', transform: 'translateY(100%)' },
                    active: { opacity: '1', transform: 'translateY(0)' },
                    exit: { opacity: '1', transform: 'translateY(-100%)' },
                },
                'slide-down': {
                    enter: { opacity: '1', transform: 'translateY(-100%)' },
                    active: { opacity: '1', transform: 'translateY(0)' },
                    exit: { opacity: '1', transform: 'translateY(100%)' },
                },
                'zoom-in': {
                    enter: { opacity: '0', transform: 'scale(0.5)' },
                    active: { opacity: '1', transform: 'scale(1)' },
                    exit: { opacity: '0', transform: 'scale(1.2)' },
                },
                'zoom-out': {
                    enter: { opacity: '0', transform: 'scale(1.5)' },
                    active: { opacity: '1', transform: 'scale(1)' },
                    exit: { opacity: '0', transform: 'scale(0.5)' },
                },
            };

            function getTransition(name) {
                return TRANSITIONS[name] || TRANSITIONS.fade;
            }

            function applyState(el, state, animate, ms) {
                el.style.transition = animate
                    ? 'transform ' + ms + 'ms ease-in-out, opacity ' + ms + 'ms ease-in-out'
                    : 'none';
                el.style.opacity = state.opacity;
                el.style.transform = state.transform;
            }

            // Mini slideshows (facility cards + next event media)
            // Video: play full native duration via `ended` (not group slide_duration).
            // Photo: use group slide_duration timer.
            function initMiniSlideshow(container) {
                let slides = [];
                try {
                    slides = JSON.parse(container.dataset.slides || '[]');
                } catch (e) {
                    return;
                }
                if (!slides.length) return;

                container.innerHTML = '<div class="mini-layer"></div><div class="mini-layer"></div>';
                const layers = container.querySelectorAll('.mini-layer');
                let active = layers[0];
                let inactive = layers[1];
                let index = 0;
                let timer = null;
                let transitioning = false;

                function clearTimer() {
                    if (timer) {
                        clearTimeout(timer);
                        timer = null;
                    }
                }

                function detachVideo(video) {
                    if (!video) return;
                    video.onended = null;
                    video.onerror = null;
                    video.onloadedmetadata = null;
                    try { video.pause(); } catch (e) {}
                }

                function setContent(layer, slide) {
                    layer.innerHTML = '';
                    if (slide.type === 'video') {
                        layer.classList.remove('mini-layer--contain');
                        const video = document.createElement('video');
                        video.src = slide.url;
                        video.muted = true;
                        video.playsInline = true;
                        video.preload = 'auto';
                        video.loop = false;
                        video.setAttribute('playsinline', '');
                        layer.appendChild(video);
                        return video;
                    }

                    layer.classList.remove('mini-layer--contain');
                    const img = document.createElement('img');
                    img.src = slide.url;
                    img.alt = '';

                    if (slide.fill === 'contain') {
                        layer.classList.add('mini-layer--contain');
                        const bg = document.createElement('div');
                        bg.className = 'mini-slide-bg';
                        bg.style.backgroundImage = 'url("' + slide.url + '")';
                        layer.appendChild(bg);
                    } else {
                        img.style.objectPosition = (slide.focusX || 50) + '% ' + (slide.focusY || 50) + '%';
                    }

                    layer.appendChild(img);
                    return img;
                }

                function armVideoAdvance(video) {
                    if (!video) {
                        timer = setTimeout(next, 1000);
                        return;
                    }

                    video.loop = false;
                    video.onended = function() {
                        clearMetaStall();
                        clearTimer();
                        next();
                    };
                    video.onerror = function() {
                        clearMetaStall();
                        clearTimer();
                        next();
                    };

                    // Safety net if `ended` never fires (decode stall, bad metadata).
                    function armSafetyFromDuration() {
                        if (!isFinite(video.duration) || video.duration <= 0) return;
                        clearTimer();
                        timer = setTimeout(function() {
                            if (!video.ended) {
                                next();
                            }
                        }, Math.ceil(video.duration + 1.5) * 1000);
                    }

                    // If metadata never arrives (hanging network/server), skip.
                    let metaStallTimer = setTimeout(function() {
                        if (!video.readyState || video.readyState < 1) {
                            clearTimer();
                            next();
                        }
                    }, VIDEO_LOAD_STALL_MS);
                    function clearMetaStall() {
                        if (metaStallTimer) {
                            clearTimeout(metaStallTimer);
                            metaStallTimer = null;
                        }
                    }

                    video.onloadedmetadata = function() {
                        video.onloadedmetadata = null;
                        clearMetaStall();
                        armSafetyFromDuration();
                    };
                    if (video.readyState >= 1) {
                        clearMetaStall();
                        armSafetyFromDuration();
                    }

                    try { video.currentTime = 0; } catch (e) {}
                    video.play().catch(function() {
                        clearMetaStall();
                        clearTimer();
                        timer = setTimeout(next, 3000);
                    });
                }

                function startCurrent() {
                    clearTimer();
                    const slide = slides[index];

                    if (slide.type === 'video') {
                        armVideoAdvance(active.querySelector('video'));
                        return;
                    }

                    timer = setTimeout(next, (slide.duration || 5) * 1000);
                }

                function next() {
                    if (transitioning) return;
                    transitioning = true;
                    clearTimer();
                    detachVideo(active.querySelector('video'));

                    index = (index + 1) % slides.length;
                    const slide = slides[index];
                    const el = setContent(inactive, slide);
                    const t = getTransition(slide.transition);

                    function run() {
                        applyState(inactive, t.enter, false, 700);
                        void inactive.offsetWidth;
                        applyState(inactive, t.active, true, 700);
                        applyState(active, t.exit, true, 700);
                        setTimeout(function() {
                            applyState(active, t.enter, false, 700);
                            active.innerHTML = '';
                            const tmp = active;
                            active = inactive;
                            inactive = tmp;
                            transitioning = false;
                            // Keep transitioned media — do not recreate (critical for video).
                            startCurrent();
                        }, 750);
                    }

                    if (slide.type === 'video') {
                        el.pause();
                        try { el.currentTime = 0; } catch (e) {}
                        let videoStall = setTimeout(function() {
                            if (!el.readyState || el.readyState < 2) {
                                transitioning = false;
                                next();
                            }
                        }, VIDEO_LOAD_STALL_MS);
                        if (el.readyState >= 2) {
                            clearTimeout(videoStall);
                            run();
                        } else {
                            el.onloadeddata = function() {
                                el.onloadeddata = null;
                                clearTimeout(videoStall);
                                run();
                            };
                        }
                        el.onerror = function() {
                            clearTimeout(videoStall);
                            transitioning = false;
                            next();
                        };
                        return;
                    }

                    let imgStall = setTimeout(function() {
                        if (!el.complete) {
                            transitioning = false;
                            next();
                        }
                    }, IMG_LOAD_STALL_MS);
                    if (el.complete) {
                        clearTimeout(imgStall);
                        run();
                    } else {
                        el.onload = function() {
                            clearTimeout(imgStall);
                            run();
                        };
                    }
                    el.onerror = function() {
                        clearTimeout(imgStall);
                        transitioning = false;
                        next();
                    };
                }

                // Single media: video loops; photo stays static.
                if (slides.length === 1) {
                    const only = slides[0];
                    const el = setContent(active, only);
                    active.style.opacity = '1';
                    inactive.style.opacity = '0';
                    if (only.type === 'video') {
                        el.loop = true;
                        el.play().catch(function() {});
                    }
                    return;
                }

                setContent(active, slides[0]);
                active.style.opacity = '1';
                inactive.style.opacity = '0';
                startCurrent();
            }

            document.querySelectorAll('.mini-slideshow').forEach(initMiniSlideshow);

            @if(!$error)
            const slides = @json($slides);
            const TRANSITION_MS = 800;

            if (!slides || slides.length === 0) return;

            const slideA = document.getElementById('slide-a');
            const slideB = document.getElementById('slide-b');
            const dots = Array.from(document.querySelectorAll('#carousel-dots .carousel-dot'));

            function setActiveDot(index) {
                dots.forEach(function(dot, i) {
                    dot.classList.toggle('active', i === index);
                });
            }

            // Audio is blocked by browsers until user interacts.
            // Start muted (so autoplay works), unmute after first click/tap/key.
            let audioUnlocked = false;
            function unlockAudio() {
                if (audioUnlocked) return;
                audioUnlocked = true;
                document.querySelectorAll('video').forEach(function(video) {
                    video.muted = false;
                    video.volume = 1;
                });
            }
            document.addEventListener('click', unlockAudio);
            document.addEventListener('keydown', unlockAudio);
            document.addEventListener('touchstart', unlockAudio);

            function createVideo(src) {
                const video = document.createElement('video');
                video.src = src;
                video.muted = !audioUnlocked;
                video.volume = 1;
                video.playsInline = true;
                video.preload = 'auto';
                video.controls = false;
                video.loop = false;
                video.setAttribute('playsinline', '');
                return video;
            }

            function loadSlideContent(container, slide) {
                container.innerHTML = '';
                if (slide.type === 'video') {
                    container.classList.remove('slide--contain');
                    const video = createVideo(slide.url);
                    container.appendChild(video);
                    return video;
                }

                container.classList.remove('slide--contain');
                const img = document.createElement('img');
                img.src = slide.url;
                img.alt = 'Slideshow';

                if (slide.fill === 'contain') {
                    container.classList.add('slide--contain');
                    const bg = document.createElement('div');
                    bg.className = 'slide-bg';
                    bg.style.backgroundImage = 'url("' + slide.url + '")';
                    container.appendChild(bg);
                } else {
                    img.style.objectPosition = (slide.focusX || 50) + '% ' + (slide.focusY || 50) + '%';
                }

                container.appendChild(img);
                return img;
            }

            // Single slide special case
            if (slides.length === 1) {
                const only = slides[0];
                const t = getTransition(only.transition);
                const el = loadSlideContent(slideA, only);
                applyState(slideA, t.active, false, TRANSITION_MS);

                // Single media has nothing to advance to: show a static
                // message instead of a frozen frame. The status poll will
                // reload the page once the content is fixed upstream.
                function showHeroPlaceholder(message) {
                    applyState(slideA, { opacity: '1', transform: 'none' }, false, 0);
                    slideA.innerHTML = '';
                    const box = document.createElement('div');
                    box.className = 'error-message';
                    box.textContent = message;
                    slideA.appendChild(box);
                }

                if (only.type === 'video') {
                    el.loop = true;
                    el.onerror = function() { showHeroPlaceholder('Media tidak tersedia'); };
                    const p = el.play();
                    if (p && p.catch) {
                        p.catch(function() { showHeroPlaceholder('Media tidak tersedia'); });
                    }
                    setTimeout(function() {
                        if (el.readyState === 0 && !el.error) {
                            showHeroPlaceholder('Media tidak tersedia');
                        }
                    }, VIDEO_LOAD_STALL_MS);
                } else {
                    el.onerror = function() { showHeroPlaceholder('Media tidak tersedia'); };
                    el.onload = function() {
                        if (el.naturalWidth === 0) showHeroPlaceholder('Media tidak tersedia');
                    };
                    setTimeout(function() {
                        if (!el.complete) showHeroPlaceholder('Media tidak tersedia');
                    }, IMG_LOAD_STALL_MS);
                }
                return;
            }

            let currentIndex = 0;
            let activeContainer = slideA;
            let inactiveContainer = slideB;
            let isTransitioning = false;
            let slideTimer = null;

            loadSlideContent(slideA, slides[0]);
            applyState(slideA, getTransition(slides[0].transition).active, false, TRANSITION_MS);
            applyState(slideB, getTransition(slides[0].transition).enter, false, TRANSITION_MS);

            function clearSlideTimer() {
                if (slideTimer) {
                    clearTimeout(slideTimer);
                    slideTimer = null;
                }
            }

            function armMainVideoAdvance(video) {
                if (!video) {
                    slideTimer = setTimeout(nextSlide, 1000);
                    return;
                }

                // Video plays to its native end — group slide_duration is ignored.
                video.loop = false;
                video.onended = function() {
                    clearMetaStall();
                    clearSlideTimer();
                    nextSlide();
                };
                video.onerror = function() {
                    clearMetaStall();
                    clearSlideTimer();
                    nextSlide();
                };

                // Safety net if `ended` never fires.
                function armSafetyFromDuration() {
                    if (!isFinite(video.duration) || video.duration <= 0) return;
                    clearSlideTimer();
                    slideTimer = setTimeout(function() {
                        if (!video.ended) {
                            nextSlide();
                        }
                    }, Math.ceil(video.duration + 1.5) * 1000);
                }

                // If metadata never arrives (hanging network/server), skip.
                let metaStallTimer = setTimeout(function() {
                    if (!video.readyState || video.readyState < 1) {
                        clearSlideTimer();
                        nextSlide();
                    }
                }, VIDEO_LOAD_STALL_MS);
                function clearMetaStall() {
                    if (metaStallTimer) {
                        clearTimeout(metaStallTimer);
                        metaStallTimer = null;
                    }
                }

                video.onloadedmetadata = function() {
                    video.onloadedmetadata = null;
                    clearMetaStall();
                    armSafetyFromDuration();
                };
                if (video.readyState >= 1) {
                    clearMetaStall();
                    armSafetyFromDuration();
                }

                try { video.currentTime = 0; } catch (e) {}
                video.muted = !audioUnlocked;

                const playPromise = video.play();
                if (playPromise && playPromise.catch) {
                    playPromise.catch(function() {
                        video.muted = true;
                        video.play().catch(function() {
                            clearSlideTimer();
                            slideTimer = setTimeout(nextSlide, 3000);
                        });
                    });
                }
            }

            function startCurrentSlide() {
                const slide = slides[currentIndex];
                clearSlideTimer();

                if (slide.type === 'video') {
                    armMainVideoAdvance(activeContainer.querySelector('video'));
                    return;
                }

                // Photos / static media use group slide_duration.
                slideTimer = setTimeout(nextSlide, (slide.duration || 5) * 1000);
            }

            function nextSlide() {
                if (isTransitioning) return;
                isTransitioning = true;
                clearSlideTimer();

                const currentVideo = activeContainer.querySelector('video');
                if (currentVideo) {
                    currentVideo.onended = null;
                    currentVideo.onerror = null;
                    currentVideo.onloadedmetadata = null;
                    currentVideo.pause();
                }

                currentIndex = (currentIndex + 1) % slides.length;
                setActiveDot(currentIndex);
                const nextSlideData = slides[currentIndex];
                const t = getTransition(nextSlideData.transition);
                const el = loadSlideContent(inactiveContainer, nextSlideData);

                function doTransition() {
                    applyState(inactiveContainer, t.enter, false, TRANSITION_MS);
                    void inactiveContainer.offsetWidth;

                    applyState(inactiveContainer, t.active, true, TRANSITION_MS);
                    applyState(activeContainer, t.exit, true, TRANSITION_MS);

                    setTimeout(function() {
                        applyState(activeContainer, t.enter, false, TRANSITION_MS);

                        const temp = activeContainer;
                        activeContainer = inactiveContainer;
                        inactiveContainer = temp;

                        isTransitioning = false;
                        startCurrentSlide();
                    }, TRANSITION_MS + 50);
                }

                if (nextSlideData.type === 'video') {
                    el.pause();
                    try { el.currentTime = 0; } catch (e) {}
                    let videoStall = setTimeout(function() {
                        isTransitioning = false;
                        setTimeout(nextSlide, 1000);
                    }, VIDEO_LOAD_STALL_MS);
                    if (el.readyState >= 2) {
                        clearTimeout(videoStall);
                        doTransition();
                    } else {
                        el.onloadeddata = function() {
                            el.onloadeddata = null;
                            clearTimeout(videoStall);
                            doTransition();
                        };
                    }
                    el.onerror = function() {
                        clearTimeout(videoStall);
                        isTransitioning = false;
                        setTimeout(nextSlide, 1000);
                    };
                    return;
                }

                let imgStall = setTimeout(function() {
                    isTransitioning = false;
                    setTimeout(nextSlide, 1000);
                }, IMG_LOAD_STALL_MS);
                if (el.complete) {
                    clearTimeout(imgStall);
                    doTransition();
                } else {
                    el.onload = function() {
                        clearTimeout(imgStall);
                        doTransition();
                    };
                }
                el.onerror = function() {
                    clearTimeout(imgStall);
                    isTransitioning = false;
                    setTimeout(nextSlide, 1000);
                };
            }

            startCurrentSlide();
            @endif
        })();
    </script>
</body>
</html>
