<span>
	{#each words as word}
		{#if word.match(/^&gt;&gt;\d+/)}
			<span
				class="reply-mention"
				role="tooltip"
				on:mouseover={onReplyHover}
				on:focus={onReplyHover}
				on:mouseleave={onHoverCancel}
			>{@html word}</span>
		{:else if word.match(/^@\w+/)}
			<span
				class="user-mention"
			>
				<Link href={parseUserLink(word)}>{@html word}</Link>
			</span>
		{:else if word.match(/^&gt;\w*/)}
			<span class="quote">{@html word}</span>
		{:else if word.match(/^https?:\/\/(www\.)?([\w-]+\.)*([\w-]+)(\/([\w-]+))*$/i)}
			<a class="link" href={word}>{word}</a>
		{:else}
			{@html word}
		{/if}
		{" "}
	{/each}
</span>

<script lang="ts">

	// Modules
    import { createEventDispatcher } from "svelte";
    import { get } from "svelte/store";

	// Components
	import Link from "$lib/components/Link.svelte";

	// Stores
	import { cache as userCache } from "$lib/stores/user";
    
	// Types	
	import type { User } from "$lib/types/User";

	// Props
	export let content: string;

	const dispatch = createEventDispatcher();
	
	$: words = content
		.replaceAll(/\n/g, "\n<br>\n") // Splits <br> as their own elements to be injected
		.split(/\n/)
		.map(line => line.match(/^&gt;\w+/)
			? line
			: line.split(" ")
		)
		.flat();

	function parseId (source: string, prefix: string): number | null {
		
		const id = parseInt(source.slice(prefix.length));
		
		if (!isNaN(id)) {
			return id;
		}

		return null;
	}

	function onReplyHover (e: MouseEvent | FocusEvent) {

		if (!e.currentTarget)
			return;

		const postId = parseId((e.currentTarget as HTMLSpanElement).innerText, ">>");

		if (postId)
			dispatch("hover", postId);
	}

	function parseUserLink (name: string): string {

		const user: User | undefined = Array.from(get(userCache).values()).find(u => u.name === name.slice(1));

		return user
			? `/users/${user.id}`
			: "#";
	}

	function onHoverCancel () {
		dispatch("hover", -1);
	}

</script>

<style>

	span {
		display: inline;
		word-wrap: break-word;
		white-space: pre-line;
	}

	.reply-mention,
	.user-mention,
	.quote,
	.link {
		color: var(--secondary-color);
	}

	.reply-mention:hover {		
		cursor: pointer;
		text-decoration: underline;
	}

	.link {
		font-style: italic;
	}

</style>
