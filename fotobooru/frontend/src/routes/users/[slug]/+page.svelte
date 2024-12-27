<svelte:head>
	<title>{title}</title> 
</svelte:head>
{#await _getUser(parseInt($page.params.slug))}
	<h1>...</h1>	
{:then user} 
	{#if typeof user !== "string"}
		<h1>{user.name}'s Gallery</h1>
		{#await getAllPostsByUploader(user.id)}
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
	{:else}
		<h1>{user}</h1>
	{/if}
{:catch error}
	<h1>{error}</h1>
{/await}

<script lang="ts">

	// Components
	import Collage from "$lib/components/Collage.svelte";

	// Stores
	import { page } from "$app/stores";

	// Utils
	import { getAllPostsByUploader, getUser } from "$lib/utils/api";
    
	// Types
	import type { User } from "$lib/types/User";

	let title = "Gallery";

	async function _getUser (id: number): Promise<User|string> {
		
		const user = await getUser(id);
		
		if (typeof user !== "string")
			title = `${user.name}'s Gallery`;

		return user;
	}

</script>

<style>

	

</style>
