<a {href} on:click={onClick}><slot /></a>

<script lang="ts">

	// Modules
	import { createEventDispatcher } from "svelte";

	// Props
	export let href: string | undefined = undefined;

	const dispatch = createEventDispatcher();

	function onClick (e: MouseEvent) {
		dispatch("click", e);
	}

</script>

<style>

	a {
		color: var(--primary-color);
		text-decoration: none;
	}

	a:hover {
		color: var(--primary-hover-color);
		text-decoration: underline;
	}

</style>
