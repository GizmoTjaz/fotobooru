<a href={postUrl} class:cropped={crop}>
	<img
		src={post.media_url}
		alt="Post preview"
	>
</a>

<script lang="ts">

	// Types
	import type { Post } from "$lib/types/Post";

	// Props
	export let post: Post;
	export let crop = false;

	$: postUrl = `/posts/${post.id}`;

</script>

<style>

	a {
		position: relative;
		display: inline-block;
		border: 3px solid transparent;
		line-height: 0; /* Remove anchor bottom padding */
		border-radius: var(--border-radius);
	}

	a:hover {
		border-color: var(--primary-color);
	}

	a:not(.cropped) img {
		max-width: 100%;
		width: auto;
		height: calc(100vh / 4);
	}
	
	a.cropped {
		width: calc(calc(100% - 5 * 6px) / 5);
		aspect-ratio: 1;
		overflow: hidden;
	}

	a.cropped img {
		position: absolute;
		max-width: 180%;
		max-height: 180%;
		width: auto;
		height: auto;
		min-width: 100%;
		min-height: 100%;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}

</style>
