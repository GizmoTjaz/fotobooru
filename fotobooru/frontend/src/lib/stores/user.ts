// Modules
import { writable } from "svelte/store";

// Types
import type { User } from "$lib/types/User";

export const user = writable<User | null>(null);

export const cache = writable<Map<number, User>>(new Map());
