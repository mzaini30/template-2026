<script lang="ts">
	import { onMount } from 'svelte';

	interface Todo {
		id: number;
		text: string;
		done: boolean;
	}

	let todos = $state<Todo[]>([]);
	let newTodoText = $state('');

	onMount(() => {
		const saved = localStorage.getItem('todos');
		if (saved) {
			todos = JSON.parse(saved);
		}
	});

	$effect(() => {
		localStorage.setItem('todos', JSON.stringify(todos));
	});

	function addTodo() {
		if (newTodoText.trim()) {
			todos.push({
				id: Date.now(),
				text: newTodoText.trim(),
				done: false
			});
			newTodoText = '';
		}
	}

	function deleteTodo(id: number) {
		todos = todos.filter((t) => t.id !== id);
	}

	function toggleTodo(id: number) {
		const todo = todos.find((t) => t.id === id);
		if (todo) {
			todo.done = !todo.done;
		}
	}

	function handleKeydown(e: KeyboardEvent) {
		if (e.key === 'Enter') {
			addTodo();
		}
	}
</script>

<div class="min-h-screen bg-gray-100 py-8 px-4">
	<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
		<h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Todo List</h1>

		<div class="flex gap-2 mb-6">
			<input
				type="text"
				bind:value={newTodoText}
				onkeydown={handleKeydown}
				placeholder="Add a new task..."
				class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
			/>
			<button
				onclick={addTodo}
				class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
			>
				Add
			</button>
		</div>

		<ul class="space-y-3">
			{#each todos as todo (todo.id)}
				<li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg group">
					<input
						type="checkbox"
						checked={todo.done}
						onchange={() => toggleTodo(todo.id)}
						class="w-5 h-5 text-blue-500 rounded focus:ring-blue-500"
					/>
					<span class="flex-1 {todo.done ? 'line-through text-gray-400' : 'text-gray-700'}">
						{todo.text}
					</span>
					<button
						onclick={() => deleteTodo(todo.id)}
						class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity"
					>
						Delete
					</button>
				</li>
			{:else}
				<p class="text-center text-gray-500 italic">No tasks yet. Add one above!</p>
			{/each}
		</ul>
	</div>
</div>
