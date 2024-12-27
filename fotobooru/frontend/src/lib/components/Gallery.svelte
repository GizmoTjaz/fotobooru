<Heading>Gallery</Heading>
{#if $user}
	<div class="gallery-controls">
		<Button on:click={() => enabled = true}>Create post</Button>
		<Creator bind:enabled />
	</div>
{/if}
<div class="gallery-container">
	{#await getAllPosts()}
		<h1>...</h1>
	{:then posts}
		{#if typeof posts !== "string"}
			<Collage posts={posts} />
		{:else}
			<h1>{posts}</h1>
		{/if}
	{:catch error}
		<h1>{error}</h1>
	{/await}
</div>

<script lang="ts">

	// Stores
	import { user } from "$lib/stores/user";

	// Components
	import Heading from "./Heading.svelte"
	import Collage from "./Collage.svelte";
	import Button from "./Button.svelte";
	import Creator from "$lib/components/Creator.svelte";

	// Utils
	import { getAllPosts } from "$lib/utils/api";

	let enabled = false;

</script>

<style>

	.gallery-controls,
	.gallery-container {
		width: 100%;
	}
	
	.gallery-controls {
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: center;
		border-bottom: 1px dashed var(--disabled-color);
		padding-bottom: 1em;
	}

	.gallery-container {
		margin-top: 1em;
	}

	.gallery-container h1 {
		width: 100%;
		text-align: center;
	}

</style>
