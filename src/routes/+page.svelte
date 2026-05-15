<script lang="ts">
	import { onMount } from 'svelte';
	import { encode, decode } from '@msgpack/msgpack';

	import { dev } from '$app/environment';

	interface Todo {
		id: number;
		text: string;
		done: boolean;
	}

	let todos = $state<Todo[]>([]);
	let newTodoText = $state('');
	let loading = $state(true);

	const API_URL = dev ? 'http://localhost:8080/api/todos.php' : '/api/todos.php';

	async function fetchTodos() {
		try {
			const res = await fetch(API_URL);
			if (res.ok) {
				const buffer = await res.arrayBuffer();
				// Fallback to JSON if not msgpack (for now)
				try {
					todos = decode(new Uint8Array(buffer)) as Todo[];
				} catch (e) {
					const text = new TextDecoder().decode(buffer);
					todos = JSON.parse(text);
				}
			}
		} catch (e) {
			console.error('Failed to fetch', e);
		} finally {
			loading = false;
		}
	}

	async function saveTodos() {
		try {
			const packed = encode(todos);
			await fetch(API_URL, {
				method: 'POST',
				body: packed,
				headers: { 'Content-Type': 'application/x-msgpack' }
			});
		} catch (e) {
			console.error('Failed to save', e);
		}
	}

	onMount(() => {
		fetchTodos();
	});

	async function addTodo() {
		if (newTodoText.trim()) {
			todos.push({
				id: Date.now(),
				text: newTodoText.trim(),
				done: false
			});
			newTodoText = '';
			await saveTodos();
		}
	}

	async function deleteTodo(id: number) {
		todos = todos.filter((t) => t.id !== id);
		await saveTodos();
	}

	async function toggleTodo(id: number) {
		const todo = todos.find((t) => t.id === id);
		if (todo) {
			todo.done = !todo.done;
			await saveTodos();
		}
	}

	function handleKeydown(e: KeyboardEvent) {
		if (e.key === 'Enter') {
			addTodo();
		}
	}
</script>

<svelte:head>
	<title>Todo — Apple Style</title>
</svelte:head>

<div class="flex flex-col min-h-screen">
	<!-- Header -->
	<header class="pt-20 pb-10 px-lg text-center">
		<h1 class="font-hero mb-xs">
			Todo
		</h1>
		<p class="text-tagline text-ink-muted-48">
			Stay organized. Stay productive.
		</p>
	</header>

	<!-- Input Area -->
	<div class="px-lg mb-xl">
		<div class="relative flex items-center">
			<input
				type="text"
				bind:value={newTodoText}
				onkeydown={handleKeydown}
				placeholder="What's on your mind?"
				class="w-full bg-canvas-parchment border-none rounded-lg px-5 py-4 text-body focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-ink-muted-48"
			/>
			<button
				onclick={addTodo}
				class="absolute right-xs px-4 py-2 bg-primary text-on-primary rounded-pill text-button-utility font-normal hover:bg-primary-focus transition-all active:scale-95"
			>
				Add
			</button>
		</div>
	</div>

	<!-- List -->
	<div class="flex-1 px-lg pb-xxl">
		{#if loading}
			<div class="flex justify-center py-section">
				<div class="w-6 h-6 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
			</div>
		{:else}
			<ul class="space-y-sm">
				{#each todos as todo (todo.id)}
					<li class="group flex items-center justify-between p-sm bg-canvas-parchment rounded-lg transition-all border border-transparent hover:border-hairline">
						<div class="flex items-center gap-sm flex-1">
							<button
								onclick={() => toggleTodo(todo.id)}
								class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all {todo.done ? 'bg-primary border-primary' : 'border-surface-chip-translucent bg-canvas'}"
							>
								{#if todo.done}
									<svg class="w-3.5 h-3.5 text-on-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
									</svg>
								{/if}
							</button>
							<span class="text-body transition-all {todo.done ? 'text-ink-muted-48 line-through' : 'text-ink'}">
								{todo.text}
							</span>
						</div>
						<button
							onclick={() => deleteTodo(todo.id)}
							aria-label="Delete task"
							class="opacity-0 group-hover:opacity-100 p-2 text-[#ff3b30] hover:bg-red-50 rounded-full transition-all active:scale-90"
						>
							<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
						</button>
					</li>
				{:else}
					<div class="text-center py-section">
						<p class="text-body text-ink-muted-48">All caught up.</p>
					</div>
				{/each}
			</ul>
		{/if}
	</div>

	<!-- Footer -->
	<footer class="p-lg text-center border-t border-canvas-parchment">
		<p class="text-fine-print text-ink-muted-48">Designed for your focus.</p>
	</footer>
</div>

<style>
	:global(body) {
		background-color: var(--color-canvas-parchment);
	}
</style>
