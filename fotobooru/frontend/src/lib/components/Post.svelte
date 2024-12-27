<div class="post-container">
	<div class="image-container">
		<img src={post.media_url} alt="Post">
	</div>
	<div class="post-meta">
		<span class="uploader">Posted by <Link href={`/users/${post.uploader.id}`}>{post.uploader.name}</Link></span>
		<span class="timestamp">{timestamp}</span>
		<span class="tools">
			{#if $user !== null && $user.id === post.uploader.id}
				<Button on:click={handleDeletePost}>🗑️</Button>
			{/if}
		</span>
		<div class="composer">
			{#if $user}
				<Error>{error}</Error>
				<Composer bind:content={composerContent} on:submit={submitReply} />
			{/if}
		</div>
	</div>
	<div class="reply-container">
		{#each post.replies as reply}
			<Reply
				{reply}
				highlighted={reply.id === highlightedReplyId}
				on:hover={highlightReply}
				on:quote={quoteReply}
				on:delete={handleDeleteReply}
			/>
		{/each}
	</div>
</div>

<script lang="ts">

	// Modules
    import { goto } from "$app/navigation";
	
	// Stores
	import { user } from "$lib/stores/user";
	import { cache as postCache } from "$lib/stores/post";

	// Components
	import Link from "$lib/components/Link.svelte";
	import Reply from "$lib/components/Reply.svelte";
	import Composer from "./Composer.svelte";
	import Error from "./Error.svelte";
	import Button from "./Button.svelte";

	// Utils
	import { formatTimestamp } from "$lib/utils/date";
	import { createReply, deleteReply, deletePost } from "$lib/utils/api";

	// Types
	import type { Post } from "$lib/types/Post";
	import type { Reply as ReplyType } from "$lib/types/Reply";

	// Props
	export let post: Post;

	let
		highlightedReplyId = -1,
		error = "",
		composerContent = "";

	$: timestamp = formatTimestamp(post.created_at);

	function highlightReply (e: CustomEvent<number>) {
		highlightedReplyId = e.detail;
	}

	function quoteReply (replyId: CustomEvent<number>) {
		
		if (composerContent.length > 0)
			composerContent += "\n";

		composerContent += `>>${replyId.detail}`;
	}

	async function submitReply (e: CustomEvent<string>) {

		const content = e.detail;

		error = "";

		if (content.trim().length === 0) {
			error = "Reply cannot be empty.";
			return;
		}

		const reply = await createReply(post, content);

		if (typeof reply !== "string") {
			
			const newPost = $postCache.get(post.id);
			
			if (newPost) {

				post = newPost;
				highlightedReplyId = reply.id;

				setTimeout(() => {
					if (highlightedReplyId === reply.id) {
						highlightedReplyId = -1;
					}
				}, 2000);
			}

			composerContent = "";

			// DOM isn't immediately updated on reply creation
			setTimeout(() => {
				window.scrollTo({
					top: document.body.scrollHeight,
					behavior: "instant"
				})
			}, 100);

		} else {
			error = reply;
		}
	}

	async function handleDeleteReply (e: CustomEvent<ReplyType>) {

		const reply = e.detail;

		if ($user === null || $user.id !== reply.poster.id)
			return;

		const res = await deleteReply(post, reply);

		if (res) {

			const cachedPost = $postCache.get(post.id);

			if (cachedPost)
				post = cachedPost;
		}
	}

	async function handleDeletePost () {
		
		if ($user === null || $user.id !== post.uploader.id)
			return;

		const res = await deletePost(post);

		if (res)
			goto("/");
	}

</script>

<style>

	.post-container, .post-meta {
		width: 100%;
		display: flex;
		flex-direction: column;
		justify-content: flex-start;
		align-items: flex-start;
	}

	.image-container {
		width: 100%;
		display: flex;
		flex-direction: row;
		justify-content: center;
	}

	img {
		max-width: 800px;
		min-width: 20%;
		width: auto;
		height: auto;
	}

	.uploader {
		font-size: 1.5em;
		font-weight: bold;
		margin: 1em 0 .15em 0;
	}

	.timestamp {
		color: var(--secondary-text-color);
		font-style: italic;
	}

	.tools {
		margin-top: .5em;
	}

	.composer {
		width: 100%;
		display: flex;
		flex-direction: column;
		justify-content: flex-start;
		align-items: center;
	}

	.reply-container {
		width: 100%;
		margin: 1em 0 1em 0;
	}

</style>
