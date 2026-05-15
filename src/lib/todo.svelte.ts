import { encode, decode } from '@msgpack/msgpack';
import { dev } from '$app/environment';

export interface Todo {
	id: number;
	text: string;
	done: boolean;
}

const API_URL = dev ? 'http://localhost:8080/api/todos.php' : '/api/todos.php';

class TodoState {
	todos = $state<Todo[]>([]);
	loading = $state(true);
	lastSynced = $state<Date | null>(null);

	async fetch() {
		this.loading = true;
		try {
			const res = await fetch(API_URL);
			if (res.ok) {
				const buffer = await res.arrayBuffer();
				try {
					this.todos = decode(new Uint8Array(buffer)) as Todo[];
				} catch (e) {
					const text = new TextDecoder().decode(buffer);
					this.todos = JSON.parse(text);
				}
				this.lastSynced = new Date();
			}
		} catch (e) {
			console.error('Fetch failed', e);
		} finally {
			this.loading = false;
		}
	}

	async save() {
		try {
			const packed = encode($state.snapshot(this.todos));
			await fetch(API_URL, {
				method: 'POST',
				body: packed,
				headers: { 'Content-Type': 'application/x-msgpack' }
			});
			this.lastSynced = new Date();
		} catch (e) {
			console.error('Save failed', e);
		}
	}

	async add(text: string) {
		if (text.trim()) {
			this.todos.push({
				id: Date.now(),
				text: text.trim(),
				done: false
			});
			await this.save();
			await this.fetch(); // Refresh IDs from DB
		}
	}

	async toggle(id: number) {
		const todo = this.todos.find((t) => t.id === id);
		if (todo) {
			todo.done = !todo.done;
			await this.save();
		}
	}

	async remove(id: number) {
		this.todos = this.todos.filter((t) => t.id !== id);
		await this.save();
	}
}

export const todoState = new TodoState();
