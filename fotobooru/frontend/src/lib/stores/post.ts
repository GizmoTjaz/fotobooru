// Modules
import { writable } from "svelte/store";

// Types
import type { Post } from "$lib/types/Post";

export const cache = writable<Map<number, Post>>(new Map());
