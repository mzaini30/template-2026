<script lang="ts">
	import { onMount } from 'svelte';
	import { encode, decode } from '@msgpack/msgpack';

	interface Todo {
		id: number;
		text: string;
		done: boolean;
	}

	let todos = $state<Todo[]>([]);
	let newTodoText = $state('');
	let loading = $state(true);

	const API_URL = '/api/todos.php';

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

<div class="min-h-screen bg-[#f5f5f7] font-sans antialiased text-[#1d1d1f]">
	<main class="mx-auto max-w-[600px] min-h-screen bg-white shadow-sm flex flex-col">
		<!-- Header -->
		<header class="pt-20 pb-10 px-8 text-center">
			<h1 class="text-[40px] font-semibold tracking-[-0.01em] leading-tight mb-2">
				Todo
			</h1>
			<p class="text-[21px] text-[#86868b] font-normal">
				Stay organized. Stay productive.
			</p>
		</header>

		<!-- Input -->
		<div class="px-8 mb-10">
			<div class="relative flex items-center">
				<input
					type="text"
					bind:value={newTodoText}
					onkeydown={handleKeydown}
					placeholder="What's on your mind?"
					class="w-full bg-[#f5f5f7] border-none rounded-[12px] px-5 py-4 text-[17px] focus:ring-2 focus:ring-[#0066cc] outline-none transition-all placeholder:text-[#86868b]"
				/>
				<button
					onclick={addTodo}
					class="absolute right-2 px-4 py-2 bg-[#0066cc] text-white rounded-full text-[14px] font-medium hover:bg-[#0071e3] transition-colors active:scale-95"
				>
					Add
				</button>
			</div>
		</div>

		<!-- List -->
		<div class="flex-1 px-8 pb-20">
			{#if loading}
				<div class="flex justify-center py-20">
					<div class="w-6 h-6 border-2 border-[#0066cc] border-t-transparent rounded-full animate-spin"></div>
				</div>
			{:else}
				<ul class="space-y-4">
					{#each todos as todo (todo.id)}
						<li class="group flex items-center justify-between p-4 bg-[#f5f5f7] rounded-[18px] transition-all">
							<div class="flex items-center gap-4 flex-1">
								<button
									onclick={() => toggleTodo(todo.id)}
									class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all {todo.done ? 'bg-[#0066cc] border-[#0066cc]' : 'border-[#d2d2d7] bg-white'}"
								>
									{#if todo.done}
										<svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
										</svg>
									{/if}
								</button>
								<span class="text-[17px] transition-all {todo.done ? 'text-[#86868b] line-through' : 'text-[#1d1d1f]'}">
									{todo.text}
								</span>
							</div>
							<button
								onclick={() => deleteTodo(todo.id)}
								aria-label="Delete task"
								class="opacity-0 group-hover:opacity-100 p-2 text-[#ff3b30] hover:bg-red-50 rounded-full transition-all"
							>
								<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
								</svg>
							</button>
						</li>
					{:else}
						<div class="text-center py-20">
							<p class="text-[17px] text-[#86868b]">All caught up.</p>
						</div>
					{/each}
				</ul>
			{/if}
		</div>

		<!-- Footer -->
		<footer class="p-8 text-center border-t border-[#f5f5f7]">
			<p class="text-[12px] text-[#86868b]">Designed for your focus.</p>
		</footer>
	</main>
</div>

<style>
	:global(body) {
		background-color: #f5f5f7;
	}
</style>
