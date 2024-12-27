<span class="button">
	<span class="divider">[</span>
	<a {href} on:click={onClick}><slot /></a>
	<span class="divider">]</span>
</span>

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

	.button {
		display: inline-flex;
		align-items: center;
		cursor: pointer;
	}

	.divider {
		vertical-align: center;
		font-size: 1.3em;
		transform: translateY(-1px) scaleY(105%);
	}

	a {
		color: var(--primary-color);
		text-decoration: none;
		font-size: 1.2em;
		margin: 0 .1em 0 .1em;
		user-select: none;
	}

	a:hover {
		color: var(--primary-hover-color);
		text-decoration: underline;
	}

</style>
