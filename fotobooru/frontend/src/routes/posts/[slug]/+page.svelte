<svelte:head>
    <title>{`Post #${$page.params.slug}`}</title> 
</svelte:head>
{#await getPost(parseInt($page.params.slug))}
	<h1>...</h1>	
{:then post} 
	{#if typeof post !== "string"}
		<Post post={post} />
	{:else}
		<h1>{post}</h1>
	{/if}
{:catch error}
	<h1>{error}</h1>
{/await}

<script lang="ts">

	// Components
	import Post from "$lib/components/Post.svelte";

	// Stores
	import { page } from "$app/stores";

	// Utils
	import { getPost } from "$lib/utils/api";

</script>

<style>

	

</style>
