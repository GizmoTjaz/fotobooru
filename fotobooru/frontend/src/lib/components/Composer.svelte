<form on:submit|preventDefault={submit}>
	<textarea
		maxlength="1024"
		bind:value={content}
		placeholder="Compose your reply..."
	></textarea>
	<Button on:click={submit}>Post</Button>
</form>

<script src="ts">

	// Modules
	import { createEventDispatcher } from "svelte";

	// Components
	import Button from "./Button.svelte";

	// Props
	export let content = "";

	const dispatch = createEventDispatcher();

	function submit () {
		dispatch("submit", content);
	}

</script>

<style>

	form {
		width: 100%;
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: flex-start;
	}

	textarea {
		flex: 1;
		color: var(--text-color);
		background-color: var(--background-color);
		border: 1px dashed var(--primary-color);
		resize: vertical;
		margin-right: .5em;
		padding: .25em;
	}

</style>
