<div class="controls">
	<Button on:click={() => crop = !crop}>{ crop ? "Uncrop" : "Crop" }</Button>
</div>
<div class="collage">
	{#if posts.length > 0}
		{#each posts as post}
			<PostPreview {post} {crop} />
		{/each}
	{:else}
		<h1>No posts</h1>
	{/if}
</div>

<script lang="ts">
	
	// Components
	import PostPreview from "./PostPreview.svelte";
	import Button from "./Button.svelte";

	// Stores
	import { cropSetting } from "$lib/stores/settings";

	// Types
	import type { Post } from "$lib/types/Post";

	// Props
	export let posts: Post[];

	let crop = $cropSetting;

	$: $cropSetting = crop;

</script>

<style>

	.controls {
		width: 100%;
		display: flex;
		flex-direction: row;
		justify-content: flex-start;
		align-items: center;
		margin-bottom: .5em;
	}

	.collage {
		width: 100%;
		display: flex;
		flex-wrap: wrap;
	}

	.collage h1 {
		width: 100%;
		text-align: center;
	}

</style>
