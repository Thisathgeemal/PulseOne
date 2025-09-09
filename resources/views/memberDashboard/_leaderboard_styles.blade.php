<style>
	:root {
		--lb-gold: #FFD700;
		--lb-silver: #C0C0C0;
		--lb-bronze: #CD7F32;
		--lb-red: #DC2626;
		--lb-dark: #1F2937;
	}

	.lb-champion-container {
		background: linear-gradient(135deg, #DC2626 0%, #B91C1C 50%, #991B1B 100%);
		position: relative;
		overflow: hidden;
		border-radius: 20px;
	/* increase top padding so the title stays above the crown/avatar */
	padding-top: 4.5rem;
	}

/* floating circular particles inside the red box (subtle) */
.lb-champion-container .lb-float-dot {
	position: absolute;
	width: 14px;
	height: 14px;
	border-radius: 50%;
	background: rgba(255,255,255,0.06);
	filter: blur(6px);
	pointer-events: none;
	animation: floatUp 6s linear infinite;
}
.lb-champion-container .lb-float-dot.small { width: 8px; height: 8px; opacity: 0.6; filter: blur(4px); }
.lb-champion-container .lb-float-dot.alt { background: rgba(255,255,255,0.04); }

@keyframes floatUp {
	0% { transform: translateY(30px) scale(0.9); opacity: 0 }
	20% { opacity: 0.15 }
	60% { opacity: 0.25 }
	100% { transform: translateY(-40px) scale(1); opacity: 0 }
}

	.lb-champion-container::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
				radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
		animation: shimmer 8s ease-in-out infinite;
	}

	@keyframes shimmer {
		0%, 100% { opacity: 0.3; }
		50% { opacity: 0.7; }
	}

	.lb-title {
		position: absolute;
		top: -40px; /* nudged up to sit above the crown more */
		left: 50%;
		transform: translateX(-50%);
		background: linear-gradient(135deg, #1F2937, #374151);
		color: white;
		font-size: 1.8rem;
		font-weight: 800;
		padding: 1rem 3rem;
		border-radius: 15px;
		box-shadow: 0 15px 35px rgba(0,0,0,0.3);
		z-index: 10;
		letter-spacing: 2px;
		border: 3px solid rgba(255,255,255,0.1);
	}

	/* Podium Avatar Styles - All Same Size */
	.lb-podium-avatar,
	.lb-avatar-side {
		width: 140px;
		height: 140px;
		/* default neutral border removed; ring classes below will set colored borders */
		border: none;
		transition: none !important;
		position: relative;
	}

/* make hover affect entire avatar including ring and show glow */
.lb-podium-avatar,
.lb-champion-avatar,
.lb-avatar-side { transition: transform 0.35s ease, box-shadow 0.35s ease, filter 0.35s ease; will-change: transform, box-shadow, filter; }

/* ensure the colored ring (inset shadow) scales and glows together with the avatar
   use a pseudo element to create an outer glow that can be animated separately */
/* remove any pseudo-element glow for avatars */
.lb-podium-avatar::after,
.lb-champion-avatar::after,
.lb-avatar-side::after { display: none !important; }

/* keep inset ring colors but no outer glow */
.lb-podium-avatar.lb-ring-1::after { display:none !important; }
.lb-podium-avatar.lb-ring-2::after { display:none !important; }
.lb-podium-avatar.lb-ring-3::after { display:none !important; }

.lb-podium-avatar:hover,
.lb-champion-avatar:hover,
.lb-avatar-side:hover {
	transform: scale(1.1);
	filter: drop-shadow(0 10px 28px rgba(0,0,0,0.35));
}
.lb-podium-avatar:hover::after,
.lb-champion-avatar:hover::after,
.lb-avatar-side:hover::after {
	opacity: 1;
	transform: scale(1.06);
}

/* ensure initials for 2nd/3rd are bigger by default when no image */
.lb-podium-avatar > div, .lb-champion-avatar > div { font-size: 2.1rem; }
.lb-podium-avatar.lb-ring-2 > div, .lb-podium-avatar.lb-ring-3 > div { font-size: 2.4rem; font-weight: 800; }

/* ensure inner images and fallback initials are clipped to a circle
   since we removed overflow-hidden to allow the colored border to show */
.lb-podium-avatar > img,
.lb-champion-avatar > img {
	border-radius: 9999px;
	width: 100%;
	height: 100%;
	display: block;
	object-fit: cover;
}

.lb-podium-avatar > div,
.lb-champion-avatar > div {
	border-radius: 9999px;
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	text-align: center;
	padding: 0;
	margin: 0;
	line-height: 1;
}

	.lb-podium-avatar:hover,
	.lb-avatar-side:hover {
		transform: scale(1.08);
		box-shadow: 0 0 50px rgba(255,255,255,0.4);
	}

	/* Champion avatar slightly larger */
	.lb-champion-avatar,
	.lb-avatar-champion {
		width: 160px;
		height: 160px;
		/* default champion border removed; ring classes override this */
		border: none;
	}

	/* Colored outer borders for top-3 avatars (match the badge colors) */
	.lb-podium-avatar.lb-ring-1,
	.lb-champion-avatar.lb-ring-1 {
		border: 6px solid var(--lb-ring-1) !important;
	}
	.lb-podium-avatar.lb-ring-2 {
		border: 6px solid var(--lb-ring-2) !important;
	}
	.lb-podium-avatar.lb-ring-3 {
		border: 6px solid var(--lb-ring-3) !important;
	}

	/* slightly larger for champion to read as prominent */
	.lb-champion-avatar.lb-ring-1 {
		border-width: 8px !important;
	}

	/* Podium Number Badges - Positioned like reference image */
	.lb-podium-badge,
	.lb-podium-number {
		position: absolute;
		bottom: -18px;
		left: 50%;
		transform: translateX(-50%);
		width: 36px;
		height: 36px;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		font-weight: 800;
		font-size: 1rem;
		box-shadow: 0 6px 14px rgba(0,0,0,0.3);
		z-index: 60;
		/* remove default white stroke so badges can appear clean with their own colors */
		border: none;
		background: #111;
		color: #fff;
	}

	/* Gold badge for 1st place (when using images we override inline) */
	.lb-podium-badge-1,
	.lb-podium-number.lb-champion-number {
		background: transparent;
		color: #fff;
		width: auto;
		height: auto;
		font-size: 1.2rem;
		bottom: -22px;
		box-shadow: none;
	}

/* slightly reduce champion typography and score blocks to fit all three stats */
.lb-champion-container h2,
.lb-champion-container .text-4xl {
	font-size: 1.5rem; /* smaller */
}
.lb-score-display .text-3xl { font-size: 1.35rem; }
.lb-score-display { padding: 0.6rem 1rem; border-radius: 10px; }

/* champion-specific progress size to be slightly bigger than side small bars */
.lb-progress-large { height: 10px; border-radius: 5px; }
.lb-progress-large .lb-progress-fill::after { animation-duration: 1.8s; }

/* tweak champion compact small stats wrapper */
.lb-champion-compact-stats { max-width: 420px; }
.lb-champion-small .lb-score-display-sm { background: rgba(255,255,255,0.12); padding: 10px 14px; border-radius: 12px; }
.lb-champion-small .text-2xl { font-size: 1.6rem; }
.lb-champion-small .text-sm { font-size: 0.9rem; }

/* Strong override: fully disable avatar hover/transform/glow for this page */
.lb-podium-avatar, .lb-champion-avatar, .lb-avatar-side, .lb-member-avatar {
	/* keep the ring/border visuals intact; only remove hover transforms */
	transform: none !important;
	transition: none !important;
}
.lb-podium-avatar::after, .lb-champion-avatar::after, .lb-avatar-side::after { display:none !important; }

/* Push 2nd and 3rd columns slightly away from the champion center */
.lb-champion-container .flex > div.text-center:first-child {
	margin-right: 4rem; /* more space to left of champion */
}
.lb-champion-container .flex > div.text-center:last-child {
	margin-left: 4rem; /* more space to right of champion */
}

@media (max-width: 1024px) {
	.lb-champion-container .flex > div.text-center:first-child,
	.lb-champion-container .flex > div.text-center:last-child {
		margin-right: 1rem;
		margin-left: 1rem;
	}
}

@media (max-width: 768px) {
	.lb-champion-container .flex > div.text-center:first-child,
	.lb-champion-container .flex > div.text-center:last-child {
		margin-right: 0;
		margin-left: 0;
	}
}

/* small stat progress bars used under 2nd/3rd */
.lb-progress-small { height: 6px; border-radius: 3px; }
.lb-progress-small .lb-progress-fill::after { animation-duration: 1.6s; }
.lb-score-display-sm .text-xl { font-size: 1rem; }

/* compact small stats container styles */
.lb-small-stats { max-width: 160px; margin: 0 auto; }
.lb-small-stats .lb-score-display-sm { background: rgba(255,255,255,0.08); padding: 6px 10px; border-radius: 8px; }

/* reduce spacing of champion's 3 stat tiles */
.lb-champion-container .grid.grid-cols-3 > div { padding: 0.6rem; }
.lb-champion-container .text-3xl { font-size: 1.6rem; }
.lb-champion-container .text-sm { font-size: 0.8rem; }

/* ensure podium badges for 2/3 sit a bit closer and smaller */
.lb-podium-badge-2, .lb-podium-badge-3 { width: 34px; height: 34px; font-size: 0.95rem; bottom: -18px; }

	/* Silver badge for 2nd place */
	.lb-podium-badge-2 { background: transparent; color: #fff; box-shadow: none; }

	/* Bronze badge for 3rd place */
	.lb-podium-badge-3 { background: transparent; color: #fff; box-shadow: none; }

	/* Crown positioning adjustment for new layout */
	.lb-crown-inline {
		position: absolute;
		top: -54px;
		left: 50%;
		transform: translateX(-50%);
		width: 80px;
		height: 56px;
		z-index: 6;
		filter: drop-shadow(0 8px 14px rgba(0,0,0,0.35));
		pointer-events: none;
	}

	/* Wheat laurel wreath behind champion */
	.lb-wheat-wreath {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		width: 240px;
		height: 240px;
		z-index: 1;
		opacity: 0.7;
		filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
		pointer-events: none;
		background-image: url('{{ asset("images/wheat.png") }}');
		background-size: contain;
		background-repeat: no-repeat;
		background-position: center;
	}

/* Responsive adjustments */
@media (max-width: 1024px) {
	.lb-podium-avatar {
		width: 120px;
		height: 120px;
	}
	
	.lb-champion-avatar {
		width: 140px;
		height: 140px;
	}
	
	.lb-crown-inline {
		width: 64px;
		height: 44px;
			top: -35px;
	}

		/* slightly adjust title placement on medium screens */
		.lb-title { top: -30px; }
	
	.lb-podium-badge,
	.lb-podium-number {
		width: 32px;
		height: 32px;
		font-size: 0.9rem;
	}
	
	.lb-podium-badge-1,
	.lb-podium-number.lb-champion-number {
		width: 38px;
		height: 38px;
		font-size: 1rem;
	}
}

@media (max-width: 768px) {
	.lb-podium-avatar {
		width: 100px;
		height: 100px;
	}
	
	.lb-champion-avatar {
		width: 120px;
		height: 120px;
	}
}

	@keyframes medalPulse {
		0%, 100% { transform: scale(1) rotate(0deg); }
		50% { transform: scale(1.1) rotate(5deg); }
	}

	@keyframes crownGlow {
		0%, 100% { 
			transform: translateX(-50%) translateY(0px);
			filter: drop-shadow(0 8px 16px rgba(0,0,0,0.5)) drop-shadow(0 0 20px rgba(255,215,0,0.3));
		}
		50% { 
			transform: translateX(-50%) translateY(-5px);
			filter: drop-shadow(0 12px 20px rgba(0,0,0,0.6)) drop-shadow(0 0 30px rgba(255,215,0,0.6));
		}
	}

	.lb-score-display {
		background: rgba(255,255,255,0.15);
		backdrop-filter: blur(15px);
		border-radius: 15px;
		padding: 1rem 2rem;
		margin-top: 1.5rem;
		border: 1px solid rgba(255,255,255,0.2);
	}

	.lb-progress-bar {
		background: rgba(255,255,255,0.2);
		height: 8px;
		border-radius: 4px;
		overflow: hidden;
		position: relative;
	}

	.lb-progress-fill {
		height: 100%;
		background: linear-gradient(90deg, #FFFFFF, rgba(255,255,255,0.8));
		border-radius: 4px;
		transition: width 2s cubic-bezier(0.4, 0, 0.2, 1);
		position: relative;
	}

	.lb-progress-fill::after {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
		animation: progressShine 2s ease-in-out infinite;
	}

	@keyframes progressShine {
		0% { left: -100%; }
		100% { left: 100%; }
	}

	.lb-table {
		background: white;
		border-radius: 20px;
		overflow: hidden;
		box-shadow: 0 25px 50px rgba(0,0,0,0.1);
		margin-top: 2rem;
		border: 1px solid rgba(0,0,0,0.05);
	}

	.lb-table-header {
		background: linear-gradient(135deg, #1F2937 0%, #374151 50%, #4B5563 100%);
		color: white;
		font-weight: 700;
		padding: 1.5rem;
		position: relative;
	}

	.lb-table-header::after {
		content: '';
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		height: 2px;
		background: linear-gradient(90deg, #DC2626, #EF4444, #DC2626);
	}

	.lb-table-row {
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
		border-bottom: 1px solid #F3F4F6;
		animation: slideInUp 0.8s ease forwards;
		opacity: 0;
		transform: translateY(30px);
		position: relative;
	}

	.lb-table-row:hover {
		transform: translateY(-3px) scale(1.02);
		box-shadow: 0 15px 35px rgba(0,0,0,0.1);
		z-index: 10;
		background: linear-gradient(135deg, #FFFFFF 0%, #FAFAFA 100%);
	}

	.lb-table-row:hover::before {
		content: '';
		position: absolute;
		left: 0;
		top: 0;
		bottom: 0;
		width: 4px;
		background: linear-gradient(to bottom, #DC2626, #EF4444);
		border-radius: 0 4px 4px 0;
	}

	.lb-table-row:last-child {
		border-bottom: none;
	}

	@keyframes slideInUp {
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.lb-rank-badge {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		background: linear-gradient(135deg, #DC2626, #B91C1C);
		color: white;
		display: flex;
		align-items: center;
		justify-content: center;
		font-weight: 800;
		box-shadow: 0 6px 15px rgba(220,38,38,0.4);
		position: relative;
		overflow: hidden;
	}

	.lb-rank-badge::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
		animation: badgeShine 3s ease-in-out infinite;
	}

	@keyframes badgeShine {
		0% { left: -100%; }
		50% { left: 100%; }
		100% { left: 100%; }
	}

	.lb-member-avatar {
		width: 56px;
		height: 56px;
		border-radius: 50%;
		border: 3px solid #FEE2E2;
		transition: all 0.3s ease;
		position: relative;
		overflow: hidden;
	}

	.lb-member-avatar:hover {
		border-color: #DC2626;
		transform: scale(1.15) rotate(5deg);
	}

	.lb-stat-value {
		font-weight: 700;
		color: #374151;
		font-size: 1rem;
	}

	.lb-no-data {
		background: linear-gradient(135deg, #F9FAFB, #F3F4F6);
		border: 3px dashed #D1D5DB;
		animation: pulse 2s ease-in-out infinite;
	}

	@keyframes pulse {
		0%, 100% { opacity: 1; transform: scale(1); }
		50% { opacity: 0.9; transform: scale(1.02); }
	}

	.lb-podium-number {
		position: absolute;
		/* place badge overlapping the lower edge of the avatar */
		bottom: -18px;
		left: 50%;
		transform: translateX(-50%);
		background: #111; /* black background */
		color: #fff;
		width: 36px;
		height: 36px;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		font-weight: 800;
		font-size: 1rem;
		box-shadow: 0 6px 14px rgba(0,0,0,0.2);
		z-index: 60; /* ensure the podium number sits above the avatar */
	}

/* Remove white border from avatar containers when using colored ring classes
   and ensure the ring (inset box-shadow) is visible */
.lb-podium-avatar.lb-ring-1,
.lb-podium-avatar.lb-ring-2,
.lb-podium-avatar.lb-ring-3,
.lb-champion-avatar.lb-ring-1,
.lb-podium-avatar.lb-ring-1 > img,
.lb-podium-avatar.lb-ring-2 > img,
.lb-podium-avatar.lb-ring-3 > img {
	border: none !important;
}

/* ensure the ring classes provide the colored stroke using inset shadow */
.lb-ring-1{ box-shadow: 0 0 0 6px var(--lb-ring-1) inset; }
.lb-ring-2{ box-shadow: 0 0 0 6px var(--lb-ring-2) inset; }
.lb-ring-3{ box-shadow: 0 0 0 6px var(--lb-ring-3) inset; }

	/* larger champion badge */
	.lb-podium-number.lb-champion-number {
		width: 52px;
		height: 52px;
		font-size: 1.25rem;
		bottom: -22px;
		background: #111; /* same black background */
		color: #fff;
	}

/* hide old medal images (gold/silver/bronze) - using badges instead */
.lb-medal, .lb-medal-side { display: none !important; }

	/* Enhanced glassmorphism */
	.lb-glass {
		background: rgba(255, 255, 255, 0.1);
		backdrop-filter: blur(20px);
		border: 1px solid rgba(255, 255, 255, 0.2);
	}

	/* Table alignment improvements */
	.lb-table-grid {
		display: grid;
		grid-template-columns: 1fr 3fr 1.5fr 1fr 1fr 1fr;
		gap: 1rem;
		align-items: center;
	}

	.lb-table-header .lb-table-grid > div {
		text-align: center;
	}

	.lb-table-header .lb-table-grid > div:first-child,
		.lb-table-header .lb-table-grid > div:nth-child(2) {
		text-align: left;
	}

	.lb-table-row .lb-table-grid > div {
		text-align: center;
	}

	.lb-table-row .lb-table-grid > div:first-child,
		.lb-table-row .lb-table-grid > div:nth-child(2) {
		text-align: left;
	}

	/* Staggered animation delays */
	.lb-table-row:nth-child(1) { animation-delay: 0.1s; }
	.lb-table-row:nth-child(2) { animation-delay: 0.2s; }
	.lb-table-row:nth-child(3) { animation-delay: 0.3s; }
	.lb-table-row:nth-child(4) { animation-delay: 0.4s; }
	.lb-table-row:nth-child(5) { animation-delay: 0.5s; }
	.lb-table-row:nth-child(6) { animation-delay: 0.6s; }

	/* Responsive adjustments for dashboard layout */
	@media (max-width: 1024px) {
		.lb-title {
			font-size: 1.5rem;
			padding: 0.8rem 2rem;
		}
		
		.lb-avatar-champion {
			width: 120px;
			height: 120px;
		}
		
		.lb-avatar-side {
			width: 80px;
			height: 80px;
		}
		
		.lb-table-grid {
			grid-template-columns: 0.8fr 2.5fr 1.2fr 1fr 1fr 1fr;
			gap: 0.5rem;
		}
	}

	@media (max-width: 768px) {
		.lb-table-grid {
			grid-template-columns: 1fr 2fr 1fr;
			gap: 0.5rem;
		}
		
		.lb-table-grid > div:nth-child(4),
		.lb-table-grid > div:nth-child(5),
		.lb-table-grid > div:nth-child(6) {
			display: none;
		}
	}
</style>
<style>
/* Medal image sizes (responsive) - nudged down slightly to sit lower over avatars */
.lb-medal-gold { position: relative; top: 8px; width: 80px; height: 80px; object-fit: contain; filter: drop-shadow(0 10px 18px rgba(0,0,0,0.35)); }
.lb-medal-silver { position: relative; top: 6px; width: 74px; height: 74px; object-fit: contain; filter: drop-shadow(0 8px 14px rgba(0,0,0,0.28)); }
.lb-medal-bronze { position: relative; top: 6px; width: 66px; height: 66px; object-fit: contain; filter: drop-shadow(0 8px 14px rgba(0,0,0,0.28)); }

@media (max-width: 1024px) {
	.lb-medal-gold { top: 6px; width: 68px; height: 68px; }
	.lb-medal-silver { top: 5px; width: 64px; height: 64px; }
	.lb-medal-bronze { top: 5px; width: 58px; height: 58px; }
}

@media (max-width: 768px) {
	.lb-medal-gold { top: 4px; width: 56px; height: 56px; }
	.lb-medal-silver { top: 3px; width: 56px; height: 56px; }
	.lb-medal-bronze { top: 3px; width: 48px; height: 48px; }
}
</style>
<style>
	:root{
		--lb-avatar-size: 96px;
		--lb-ring-1: #FFD700; /* gold */
		--lb-ring-2: #C0C0C0; /* silver */
		--lb-ring-3: #CD7F32; /* bronze */
		--lb-accent: #e53935; /* red accent */
	}

  .lb-avatar{ width: var(--lb-avatar-size); height: var(--lb-avatar-size); border-radius: 9999px; display:inline-block; overflow:hidden; }
  .lb-avatar img{ width:100%; height:100%; object-fit:cover; }
.lb-ring-1{ box-shadow: 0 0 0 6px var(--lb-ring-1); }
.lb-ring-2{ box-shadow: 0 0 0 6px var(--lb-ring-2); }
.lb-ring-3{ box-shadow: 0 0 0 6px var(--lb-ring-3); }

  .lb-top-card { position: relative; padding-top: 4.5rem }
  .lb-top-avatar { width: var(--lb-avatar-size); height: var(--lb-avatar-size); border-radius:9999px }
  .lb-top-avatar.lb-ring-1{ box-shadow: 0 0 0 6px var(--lb-ring-1) }
  .lb-top-avatar.lb-ring-2{ box-shadow: 0 0 0 6px var(--lb-ring-2) }
  .lb-top-avatar.lb-ring-3{ box-shadow: 0 0 0 6px var(--lb-ring-3) }

  /* header over card */
  .lb-top-card > .absolute { pointer-events: none }

  /* list header */
  .lb-list-header{ background: linear-gradient(90deg, rgba(229,57,53,0.04), rgba(255,235,238,0.03)); border-radius: 8px 8px 0 0 }

  .leaderboard-list > div { /* removed dividing lines - card style */ }

  .lb-badge-num{ display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:50%; background:var(--lb-accent); color:white; font-weight:700; }

  .lb-top-avatar + svg { position: absolute; right: -6px; bottom: -6px }

  .lb-badge-overlay{ position:absolute; right:-6px; bottom:-6px; border-radius:50%; box-shadow:0 6px 14px rgba(0,0,0,0.12); pointer-events:none; z-index:30; display:flex; align-items:center; justify-content:center }
  .lb-badge-1{ width:48px; height:48px; right:-10px; bottom:-10px; }
  .lb-badge-2{ width:40px; height:40px; right:-8px; bottom:-8px; }
  .lb-badge-3{ width:36px; height:36px; right:-6px; bottom:-6px; }

  @keyframes lb-fade-up { from { opacity:0; transform: translateY(6px) } to { opacity:1; transform: translateY(0) } }

  /* remove the table-style header bottom border */
  .lb-list-header { border-bottom: none }

  /* medal svg sizing */
  .lb-medal{ width:24px; height:24px; display:inline-block; vertical-align:middle }

  .lb-badge-num{ display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:50%; background:var(--lb-accent); color:white; font-weight:700 }

  .leaderboard-list .col-span-2, .leaderboard-list .col-span-1 { text-align:center }

  /* table header accent */
	/* small UI tweaks requested by user:
		 - Reduce champion name & score sizes
		 - Remove 'boxed' glass appearance from the three top-stat tiles
		 - Align 2nd & 3rd podium badges to match 1st's placement
	*/

	/* Champion typography tweaks (smaller, tighter) */
	.lb-champion-container h2,
	.lb-champion-container .text-4xl {
		font-size: 1.6rem; /* smaller than text-4xl */
		line-height: 1.1;
		margin-bottom: 0.35rem;
	}

	.lb-score-display .text-3xl {
		font-size: 1.5rem; /* slightly smaller champion score */
		font-weight: 700;
	}

	/* Make the three stat tiles non-boxed: keep spacing but remove glass/background/border */
	.lb-champion-container .lb-glass {
		background: transparent !important;
		backdrop-filter: none !important;
		border: none !important;
		box-shadow: none !important;
	}

	/* Align 2nd & 3rd badges to sit like the 1st badge (same vertical offset) */
	.lb-podium-badge-2,
	.lb-podium-badge-3 {
		bottom: -22px; /* match the champion badge vertical placement */
		left: 50%;
		transform: translateX(-50%);
	}

	.lb-header-accent{ background: rgba(229,57,53,0.06); color: var(--lb-accent); }

/* Final safety overrides: prevent any hover from changing the avatar ring/border
   and ensure the ring color/border remains visible on hover. */
.lb-podium-avatar:hover,
.lb-champion-avatar:hover,
.lb-avatar-side:hover,
.lb-member-avatar:hover {
	transform: none !important;
	transition: none !important;
	box-shadow: none !important;
}

.lb-podium-avatar.lb-ring-1:hover,
.lb-champion-avatar.lb-ring-1:hover { border: 6px solid var(--lb-ring-1) !important; }
.lb-podium-avatar.lb-ring-2:hover { border: 6px solid var(--lb-ring-2) !important; }
.lb-podium-avatar.lb-ring-3:hover { border: 6px solid var(--lb-ring-3) !important; }

/* Champion gets slightly thicker border even on hover */
.lb-champion-avatar.lb-ring-1:hover { border-width: 8px !important; }

</style>
