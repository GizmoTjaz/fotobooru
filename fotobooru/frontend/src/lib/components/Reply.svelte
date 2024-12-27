<div class="reply" class:highlighted={highlighted}>
	<div class="separator"></div>
	<div class="reply-meta">
		<span class="poster">
			{#if $user}
				<!-- svelte-ignore a11y-click-events-have-key-events -->
				<span
					class="id clickable"
					role="button"
					tabindex="0"
					on:click={() => quoteReply(reply.id)}
				>({reply.id})</span>
			{:else}
				<span class="id">({reply.id})</span>
			{/if}
			<Link href={`/users/${reply.poster.id}`}>{reply.poster.name}</Link>
		</span>
		<div class="tools">
			<span class="buttons">
				{#if $user !== null && $user.id === reply.poster.id}
					<Button on:click={handleDeleteReply}>🗑️</Button>
				{/if}
			</span>
			<span class="date">{timestamp}</span>
		</div>
	</div>
	<span class="content">
		<FormattedContent
			content={reply.content}
			on:hover
		/>
	</span>
</div>

<script lang="ts">

	// Modules
	import { createEventDispatcher } from "svelte";

	// Stores
	import { user } from "$lib/stores/user";

	// Components
	import Link from "$lib/components/Link.svelte";
	import FormattedContent from "./FormattedContent.svelte";
	import Button from "$lib/components/Button.svelte";

	// Utils
	import { formatTimestamp } from "$lib/utils/date";

	// Types
	import type { Reply } from "$lib/types/Reply";

	// Props
	export let reply: Reply;
	export let highlighted = false;

	const dispatch = createEventDispatcher();

	$: timestamp = formatTimestamp(reply.created_at);

	function quoteReply (replyId: number) {
		dispatch("quote", replyId);
	}

	function handleDeleteReply () {
		dispatch("delete", reply);
	}

</script>

<style>

	.reply {
		position: relative;
		width: 100%;
		padding: 0 0 .8em 0;
		box-sizing: border-box;
		transition: background-color .3s ease-in-out;
	}

	.separator {
		height: 1px;
		margin-bottom: .2em;
	}

	.separator::after {
		position: absolute;
		width: 100%;
		height: 1px;
		top: 0;
		left: 0;
		content: "";
		/* background-color: var(--disabled-color); */
		border-top: 1px dashed var(--disabled-color);
	}

	.reply.highlighted {
		background-color: var(--highlight-color);
	}

	.reply-meta {
		font-size: 1.3em;
		margin-bottom: .25em;
	}
	
	.reply-meta, .poster {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		align-items: center;
	}

	.poster {
		font-size: .8em;
	}

	.id {
		font-size: .6em;
		color: var(--secondary-text-color);
		margin-right: .5em;
		margin-top: 2px;
	}

	.id.clickable {
		cursor: pointer;
	}

	.id.clickable:hover {
		text-decoration: underline;
	}

	.date {
		font-size: .6em;
		font-style: italic;
		color: var(--secondary-text-color);
	}

	.buttons {
		font-size: .5em;
	}

	.content {
		width: 100%;
		font-size: .8em;
		line-height: 0;
	}

	@media only screen and (min-width: 600px) {

		.poster {
			font-size: 1em;
		}

		.date {
			font-size: .8em;
		}

		.content {
			font-size: 1em;
		}

		.buttons {
			font-size: .6em;
		}

	}

</style>
