<script lang="ts">
	import './layout.css';
	import favicon from '$lib/assets/favicon.svg';
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	let { children } = $props();

	onMount(async () => {
		if (browser) {
			const VConsole = (await import('vconsole')).default;
			new VConsole();
		}
	});
</script>

<svelte:head><link rel="icon" href={favicon} /></svelte:head>

<div class="w-full max-w-[600px] min-h-screen bg-canvas shadow-sm flex flex-col">
	<!-- Global Nav -->
	<nav class="h-[44px] bg-surface-black text-on-dark px-lg flex items-center justify-between z-50">
		<div class="flex items-center gap-lg">
			<svg class="w-4 h-4" viewBox="0 0 17 21" fill="currentColor">
				<path d="M14.2 11.4c0-2.3 1.9-3.5 1.9-3.5-1.1-1.6-2.8-1.8-3.4-1.8-1.4-.1-2.8.8-3.6.8-.7 0-1.9-.8-3.1-.8-1.6 0-3.1.9-3.9 2.4-1.7 2.9-.4 7.2 1.2 9.6.8 1.2 1.7 2.5 3 2.5 1.2 0 1.7-.8 3.2-.8s1.9.8 3.2.8c1.3 0 2.1-1.2 2.9-2.4.9-1.3 1.3-2.6 1.3-2.7-.1 0-2.7-1-2.7-4zm-2.7-7.3c.6-.8 1-1.9.9-3-.9 0-2 1.1-2.6 1.8-.6.7-1 1.8-1 2.8.9.1 1.9-.6 2.7-1.6z"/>
			</svg>
		</div>
		<div class="flex items-center gap-lg text-nav-link">
			<span class="cursor-default opacity-60">Store</span>
			<span class="cursor-default opacity-60">Mac</span>
			<span class="cursor-default opacity-60">iPad</span>
			<span class="cursor-default opacity-60">iPhone</span>
		</div>
		<div class="flex items-center gap-sm">
			<svg class="w-4 h-4 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
			<svg class="w-4 h-4 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
		</div>
	</nav>

	<!-- Sub Nav Frosted -->
	<nav class="sticky top-0 h-[52px] bg-canvas-parchment/80 backdrop-blur-xl border-b border-hairline px-lg flex items-center justify-between z-40">
		<span class="text-tagline font-semibold">Todo</span>
		<div class="flex items-center gap-sm">
			<span class="text-button-utility cursor-default hover:text-primary transition-colors">Overview</span>
			<span class="text-button-utility cursor-default hover:text-primary transition-colors">History</span>
			<button class="bg-primary text-on-primary rounded-pill px-3 py-1 text-[12px] font-medium hover:bg-primary-focus transition-all active:scale-95">
				Sync
			</button>
		</div>
	</nav>

	<div class="flex-1 overflow-y-auto">
		{@render children()}
	</div>
</div>
