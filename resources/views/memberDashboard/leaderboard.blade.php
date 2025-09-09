@extends('memberDashboard.layout')

@section('content')
@include('memberDashboard._leaderboard_styles')

{{-- Main Content Container --}}
<div class="p-6">
	{{-- Period Toggle: Weekly | 30d | Calendar Month --}}
	@php
		$activePeriod = strtolower(request('period', 'weekly'));
		$semantics = strtolower(request('semantics', ''));
	$isWeekly = $activePeriod === 'weekly';
	$isLastWeek = $activePeriod === 'last-week';
		$isMonthly30 = $activePeriod === 'monthly' && $semantics !== 'calendar';
		$isMonthlyCal = $activePeriod === 'monthly' && $semantics === 'calendar';
		// Safe initials helper available to the whole view
		$initials = function($u) {
			$fn = $u->first_name ?? '';
			$ln = $u->last_name ?? '';
			$parts = array_filter([$fn, $ln]);
			$chars = '';
			foreach ($parts as $p) { $chars .= mb_substr($p,0,1); }
			return strtoupper(substr($chars,0,2));
		};
	@endphp
	<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
	<div class="inline-flex bg-gray-100 dark:bg-gray-800 rounded-full p-1 shadow border border-gray-200 dark:border-gray-700">
			<a href="{{ route('member.leaderboard', ['period' => 'weekly']) }}"
			   class="px-4 py-2 rounded-full text-sm font-medium transition {{ $isWeekly ? 'bg-red-600 dark:bg-red-500 text-white shadow' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900' }}">
				Weekly
			</a>
			<a href="{{ route('member.leaderboard', ['period' => 'last-week']) }}"
			   class="px-4 py-2 rounded-full text-sm font-medium transition {{ $isLastWeek ? 'bg-red-600 dark:bg-red-500 text-white shadow' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900' }}">
				Last Week
			</a>
			<a href="{{ route('member.leaderboard', ['period' => 'monthly']) }}"
			   class="px-4 py-2 rounded-full text-sm font-medium transition {{ $isMonthly30 ? 'bg-red-600 dark:bg-red-500 text-white shadow' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900' }}">
				Monthly
			</a>
		</div>
	</div>
	@if(empty($entries))
		<div class="no-data rounded-2xl p-12 text-center bg-white dark:bg-gray-900 shadow">
			<div class="text-8xl mb-6">🏆</div>
			<h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">No Champions Yet!</h2>
			<p class="text-lg text-gray-600 dark:text-gray-300">Kick things off by logging workouts, attendance, and meals to appear here.</p>
		</div>
	@else
		<div class="max-w-full">
			@php
				$top = $entries[0] ?? null;
				$second = $entries[1] ?? null;
				$third = $entries[2] ?? null;
			@endphp

{{-- Enhanced Champion Section --}}
<div class="lb-champion-wrapper" style="position: relative;">
	{{-- Leaderboard Title Overlay with stacked date inside the black pill --}}
	<div class="lb-title" style="background:#000; color:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; padding:12px 20px; min-height:72px;">
		<span class="text-base sm:text-lg font-semibold tracking-widest">LEADERBOARD</span>
		<span class="text-xs sm:text-sm" style="opacity:0.92;">
			{{ isset($start) ? $start->format('M d, Y') : '' }} — {{ isset($end) ? $end->format('M d, Y') : '' }}
		</span>
	</div>
	<div class="lb-champion-container p-8 text-white shadow-2xl mb-8 mt-12">
		<div class="flex items-center justify-center gap-8 pt-8">
			{{-- Second Place --}}
			<div class="text-center">
				@if($second)
					<div class="relative inline-block">
						<div class="lb-podium-avatar lb-ring-2 rounded-full bg-white relative mx-auto">
							@if(!empty($second['user']->profile_image))
								<img src="{{ asset($second['user']->profile_image) }}?v={{ time() }}" class="w-full h-full object-cover" alt="{{ $second['user']->first_name }}">
							@else
								<div class="w-full h-full flex items-center justify-center text-3xl font-bold bg-gradient-to-br from-gray-100 to-gray-200 text-red-600">{{ $initials($second['user']) }}</div>
							@endif
						</div>
						<div class="lb-podium-badge lb-podium-badge-2" style="background:transparent; box-shadow:none; width:auto; height:auto;">
							<img src="{{ asset('images/silver.png') }}" alt="Silver" class="lb-medal-silver">
						</div>
					</div>
					<div class="mt-4">
						<div class="text-2xl font-bold mt-6 mb-2">{{ $second['user']->first_name ?? 'Member' }}</div>
						{{-- plain pts line removed; show boxed score below instead --}}
						{{-- compact stats under 2nd place --}}
						<div class="mt-3 lb-small-stats text-center">
							<div class="lb-score-display lb-score-display-sm inline-block mb-2">
								<div class="text-xl font-bold">{{ number_format($second['score_display'] ?? $second['score'],3) }}</div>
								<div class="text-xs opacity-80">Score</div>
							</div>
							<div class="text-xs opacity-80 mb-1">Attendances <span class="font-bold">{{ round($second['attendance_pct'] ?? 0, 1) }}%</span></div>
							<div class="lb-progress-bar lb-progress-small mb-2">
								<div class="lb-progress-fill" style="width: {{ min(100, max(0, round($second['attendance_pct'] ?? 0,1))) }}%"></div>
							</div>
							<div class="text-xs opacity-80 mb-1">Workout <span class="font-bold">{{ isset($second['workout_pct']) ? number_format($second['workout_pct'],1) . '%' : (isset($second['workout_days']) ? number_format($second['workout_days'],1) . '%' : '0%') }}</span></div>
							<div class="lb-progress-bar lb-progress-small mb-2">
								<div class="lb-progress-fill" style="width: {{ min(100, max(0, round($second['workout_pct'] ?? ($second['workout_days'] ?? 0),1))) }}%"></div>
							</div>
							<div class="text-xs opacity-80 mb-1">Diet <span class="font-bold">{{ isset($second['compliance_pct']) ? number_format($second['compliance_pct'],1) . '%' : '0%' }}</span></div>
							<div class="lb-progress-bar lb-progress-small">
								<div class="lb-progress-fill" style="width: {{ min(100, max(0, round($second['diet_pct'] ?? ($second['compliance_pct'] ?? 0),1))) }}%"></div>
							</div>
						</div>
					</div>
				@endif
			</div>

			{{-- Champion Center --}}
			<div class="text-center px-4 relative">
				@if($top)
					<div class="relative inline-block text-center">
						<img src="{{ asset('images/crown.png') }}" class="lb-crown-inline" alt="Crown">
						<div class="lb-wheat-wreath"></div>
						<div class="lb-podium-avatar lb-champion-avatar lb-ring-1 rounded-full bg-white relative mx-auto">
							@if(!empty($top['user']->profile_image))
								<img src="{{ asset($top['user']->profile_image) }}?v={{ time() }}" class="w-full h-full object-cover" alt="{{ $top['user']->first_name }}">
							@else
								<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-5xl font-extrabold text-red-600">{{ $initials($top['user']) }}</div>
							@endif
						</div>
						<div class="lb-podium-badge lb-podium-badge-1" style="background:transparent; box-shadow:none; width:auto; height:auto;">
							<img src="{{ asset('images/gold.png') }}" alt="Gold" class="lb-medal-gold">
						</div>
					</div>

					<div class="mt-8">
						<h2 class="text-4xl font-extrabold mb-2">{{ $top['user']->first_name ?? 'Member' }} {{ $top['user']->last_name ?? '' }}</h2>
						<div class="lb-score-display inline-block">
							<div class="text-3xl font-bold">{{ number_format($top['score_display'] ?? $top['score'], 3) }}</div>
							<div class="text-sm opacity-80">Champion Score</div>
						</div>
					</div>

					{{-- Champion compact vertical stats: show only the progress bars (remove boxed score for champion) --}}
					<div class="mt-6 lb-champion-compact-stats mx-auto text-center">
						<div class="lb-small-stats lb-champion-small">
							<div class="text-sm opacity-80 mb-1">Attendances <span class="font-bold">{{ round($top['attendance_pct'] ?? 0, 1) }}%</span></div>
							<div class="lb-progress-bar lb-progress-large mb-3">
								<div class="lb-progress-fill" style="width: {{ min(100, max(0, round($top['attendance_pct'] ?? 0,1))) }}%"></div>
							</div>

							<div class="text-sm opacity-80 mb-1">Workout <span class="font-bold">{{ isset($top['workout_pct']) ? number_format($top['workout_pct'],1) . '%' : (isset($top['workout_days']) ? number_format($top['workout_days'],1) . '%' : '0%') }}</span></div>
							<div class="lb-progress-bar lb-progress-large mb-3">
								<div class="lb-progress-fill" style="width: {{ min(100, max(0, round($top['workout_pct'] ?? ($top['workout_days'] ?? 0),1))) }}%"></div>
							</div>

							<div class="text-sm opacity-80 mb-1">Diet <span class="font-bold">{{ isset($top['compliance_pct']) ? number_format($top['compliance_pct'],1) . '%' : '0%' }}</span></div>
							<div class="lb-progress-bar lb-progress-large">
								<div class="lb-progress-fill" style="width: {{ min(100, max(0, round($top['diet_pct'] ?? ($top['compliance_pct'] ?? 0),1))) }}%"></div>
							</div>
						</div>
					</div>
				@else
					<div class="py-12">
						<div class="text-8xl mb-6">👑</div>
						<div class="text-3xl font-bold">No Champion Yet</div>
						<div class="text-lg opacity-75 mt-2">The throne awaits its ruler!</div>
					</div>
				@endif
			</div>

			{{-- Third Place --}}
			<div class="text-center">
				@if($third)
					<div class="relative inline-block">
						<div class="lb-podium-avatar lb-ring-3 rounded-full bg-white relative mx-auto">
							@if(!empty($third['user']->profile_image))
								<img src="{{ asset($third['user']->profile_image) }}?v={{ time() }}" class="w-full h-full object-cover" alt="{{ $third['user']->first_name }}">
							@else
								<div class="w-full h-full flex items-center justify-center text-3xl font-bold bg-gradient-to-br from-gray-100 to-gray-200 text-red-600">{{ $initials($third['user']) }}</div>
							@endif
						</div>
						<div class="lb-podium-badge lb-podium-badge-3" style="background:transparent; box-shadow:none; width:auto; height:auto;">
							<img src="{{ asset('images/bronze.png') }}" alt="Bronze" class="lb-medal-bronze">
						</div>
					</div>
					<div class="mt-4">
						<div class="text-2xl font-bold mt-6 mb-2">{{ $third['user']->first_name ?? 'Member' }}</div>
						{{-- plain pts line removed; show boxed score below instead --}}
						{{-- compact stats under 3rd place --}}
						<div class="mt-3 lb-small-stats text-center">
							<div class="lb-score-display lb-score-display-sm inline-block mb-2">
								<div class="text-xl font-bold">{{ number_format($third['score_display'] ?? $third['score'],3) }}</div>
								<div class="text-xs opacity-80">Score</div>
							</div>
							<div class="text-xs opacity-80 mb-1">Attendances <span class="font-bold">{{ round($third['attendance_pct'] ?? 0, 1) }}%</span></div>
							<div class="lb-progress-bar lb-progress-small mb-2">
								<div class="lb-progress-fill" style="width: {{ min(100, max(0, round($third['attendance_pct'] ?? 0,1))) }}%"></div>
							</div>
							<div class="text-xs opacity-80 mb-1">Workout <span class="font-bold">{{ isset($third['workout_pct']) ? number_format($third['workout_pct'],1) . '%' : (isset($third['workout_days']) ? number_format($third['workout_days'],1) . '%' : '0%') }}</span></div>
							<div class="lb-progress-bar lb-progress-small mb-2">
								<div class="lb-progress-fill" style="width: {{ min(100, max(0, round($third['workout_pct'] ?? ($third['workout_days'] ?? 0),1))) }}%"></div>
							</div>
							<div class="text-xs opacity-80 mb-1">Diet <span class="font-bold">{{ isset($third['compliance_pct']) ? number_format($third['compliance_pct'],1) . '%' : '0%' }}</span></div>
							<div class="lb-progress-bar lb-progress-small">
								<div class="lb-progress-fill" style="width: {{ min(100, max(0, round($third['diet_pct'] ?? ($third['compliance_pct'] ?? 0),1))) }}%"></div>
							</div>
						</div>
					</div>
				@endif
			</div>

		</div> <!-- end .flex items-center -->

				{{-- Enhanced Leaderboard Table --}}
				@if(count($entries) > 3)
					<div class="lb-table mt-8">
						{{-- Table Header --}}
						<div class="lb-table-header">
							<div class="lb-table-grid">
								<div class="font-bold">Rank</div>
								<div class="font-bold">Member</div>
								<div class="font-bold">Score</div>
								<div class="font-bold hidden md:block">Attendance</div>
								<div class="font-bold hidden md:block">Workout</div>
								<div class="font-bold hidden md:block">Diet</div>
							</div>
						</div>

						{{-- Table Body --}}
						<div>
							@foreach(array_slice($entries, 3) as $i => $entry)
								@php $rank = $i + 4; @endphp
								<div class="lb-table-row p-6">
									<div class="lb-table-grid">
										<div>
											<div class="lb-rank-badge">{{ $rank }}</div>
										</div>
										<div>
											<div class="flex items-center space-x-4">
												<div class="lb-member-avatar bg-gradient-to-br from-gray-100 to-gray-200">
													@if(!empty($entry['user']->profile_image))
														<img src="{{ asset($entry['user']->profile_image) }}?v={{ time() }}" class="w-full h-full object-cover" alt="{{ $entry['user']->first_name }}">
													@else
														<div class="w-full h-full flex items-center justify-center text-lg font-bold text-red-600">{{ $initials($entry['user']) }}</div>
													@endif
												</div>
												<div>
													<div class="font-bold text-gray-900 text-lg">{{ $entry['user']->first_name ?? 'Member' }} {{ $entry['user']->last_name ?? '' }}</div>
													<div class="text-sm text-gray-500">Competitor</div>
												</div>
											</div>
										</div>
										<div>
											<div class="lb-stat-value text-xl">{{ number_format($entry['score_display'] ?? $entry['score'],3) }}</div>
										</div>
										<div class="hidden md:block">
											<div class="lb-stat-value">{{ round($entry['attendance_pct'] ?? 0, 1) }}%</div>
										</div>
										<div class="hidden md:block">
											<div class="lb-stat-value">{{ isset($entry['workout_pct']) ? number_format($entry['workout_pct'],1) . '%' : (isset($entry['workout_days']) ? number_format($entry['workout_days'],1) . '%' : '0%') }}</div>
										</div>
										<div class="hidden md:block">
											<div class="lb-stat-value">{{ isset($entry['compliance_pct']) ? number_format($entry['compliance_pct'],1) . '%' : '0%' }}</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				@endif
				</div>
			</div>
		</div>
	@endif
	</div>

@if(auth()->check())
	@php
		$currentUserId = auth()->id();
		$topThreeIds = array_map(fn($e) => $e['user_id'], array_slice($entries, 0, 3));
		$isTopThree = in_array($currentUserId, $topThreeIds);
		$userRank = null;
		foreach($entries as $index => $entry) {
			if($entry['user_id'] == $currentUserId) {
				$userRank = $index + 1;
				break;
			}
		}
	@endphp

	{{-- Confetti ONLY for users who are in top 3 ranks --}}
	@if($isTopThree && isset($userRank))
		<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
		<script>
			(function() {
				function randomInRange(min, max) {
					return Math.random() * (max - min) + min;
				}

				const userRank = @json($userRank);

				@if($userRank == 1)
					// CHAMPION CONFETTI - Lots of confetti, 4 waves with increased intensity
					function championConfetti() {
						// First big wave - increased particles
						confetti({
							particleCount: 250,
							spread: 160,
							origin: { y: 0.3 },
							colors: ['#FFD700', '#FFA500', '#FF8C00', '#FFFF00'],
							shapes: ['star', 'circle'],
							scalar: 1.6,
							startVelocity: 60
						});

						// Second wave after delay - increased
						setTimeout(() => {
							confetti({
								particleCount: 200,
								spread: 140,
								origin: { y: 0.4 },
								colors: ['#FFD700', '#FFFF00', '#FFA500'],
								shapes: ['star'],
								scalar: 1.5,
								startVelocity: 55
							});
						}, 600);

						// Side cannons - increased
						setTimeout(() => {
							confetti({
								particleCount: 120,
								angle: 45,
								spread: 100,
								origin: { x: 0, y: 0.5 },
								colors: ['#FFD700', '#FFA500'],
								scalar: 1.4,
								startVelocity: 50
							});
							confetti({
								particleCount: 120,
								angle: 135,
								spread: 100,
								origin: { x: 1, y: 0.5 },
								colors: ['#FFD700', '#FFA500'],
								scalar: 1.4,
								startVelocity: 50
							});
						}, 300);

						// Third celebratory burst - increased
						setTimeout(() => {
							confetti({
								particleCount: 180,
								spread: 130,
								origin: { y: 0.35 },
								colors: ['#FFD700', '#FFFF00'],
								shapes: ['star'],
								scalar: 1.7,
								startVelocity: 50
							});
						}, 1200);

						// Fourth final burst - new wave
						setTimeout(() => {
							confetti({
								particleCount: 150,
								spread: 150,
								origin: { y: 0.3 },
								colors: ['#FFD700', '#FFA500', '#FFFF00'],
								shapes: ['star', 'circle'],
								scalar: 1.5,
								startVelocity: 45
							});
						}, 1800);
					}

					// Trigger champion confetti immediately
					setTimeout(championConfetti, 200);

				@elseif($userRank == 2)
					// SECOND PLACE CONFETTI - Enhanced intensity with 3 waves
					function secondPlaceConfetti() {
						confetti({
							particleCount: 150,
							spread: 120,
							origin: { y: 0.4 },
							colors: ['#C0C0C0', '#E5E7EB', '#D1D5DB', '#F3F4F6'],
							shapes: ['circle'],
							scalar: 1.4,
							startVelocity: 50
						});

						setTimeout(() => {
							confetti({
								particleCount: 100,
								spread: 100,
								origin: { y: 0.45 },
								colors: ['#C0C0C0', '#F3F4F6'],
								shapes: ['circle'],
								scalar: 1.3,
								startVelocity: 45
							});
						}, 500);

						// Third wave for enhanced effect
						setTimeout(() => {
							confetti({
								particleCount: 80,
								spread: 90,
								origin: { y: 0.5 },
								colors: ['#C0C0C0', '#E5E7EB'],
								shapes: ['circle'],
								scalar: 1.2,
								startVelocity: 40
							});
						}, 1000);
					}

					// Trigger second place confetti
					setTimeout(secondPlaceConfetti, 300);

				@elseif($userRank == 3)
					// THIRD PLACE CONFETTI - Enhanced intensity with 3 waves
					function thirdPlaceConfetti() {
						confetti({
							particleCount: 120,
							spread: 100,
							origin: { y: 0.45 },
							colors: ['#CD7F32', '#D2691E', '#B8860B'],
							shapes: ['circle'],
							scalar: 1.3,
							startVelocity: 45
						});

						setTimeout(() => {
							confetti({
								particleCount: 80,
								spread: 80,
								origin: { y: 0.5 },
								colors: ['#CD7F32', '#B8860B'],
								shapes: ['circle'],
								scalar: 1.2,
								startVelocity: 40
							});
						}, 400);

						// Third wave for enhanced effect
						setTimeout(() => {
							confetti({
								particleCount: 60,
								spread: 70,
								origin: { y: 0.55 },
								colors: ['#CD7F32', '#D2691E'],
								shapes: ['circle'],
								scalar: 1.1,
								startVelocity: 35
							});
						}, 800);
					}

					// Trigger third place confetti
					setTimeout(thirdPlaceConfetti, 400);
				@endif

			})();
		</script>
	@endif
@endif

@endsection
