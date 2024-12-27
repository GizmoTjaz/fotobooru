<!-- svelte-ignore a11y-click-events-have-key-events -->
<!-- svelte-ignore a11y-no-noninteractive-element-interactions -->
<dialog
	bind:this={dialog}
	on:close={() => enabled = false}
	on:click|self={() => dialog.close()}
>
	<!-- svelte-ignore a11y-no-static-element-interactions -->
	<div class="dialog-content">
		<h1 class="dialog-title">New Post</h1>
		<Error>{error}</Error>
		<form on:submit|preventDefault={submit}>
			<input type="hidden" name="MAX_FILE_SIZE" value={MAX_FILE_SIZE}>
			<input
				type="file"
				name="media"
				accept="image/*"
				bind:this={fileInput}
			>
		</form>
		<div class="button-row">
			<Button on:click={submit}>Create</Button>
			<Button on:click={() => dialog.close()}>Cancel</Button>
		</div>
	</div>
</dialog>
<!-- svelte-ignore a11y-click-events-have-key-events -->
<!-- svelte-ignore a11y-no-static-element-interactions -->
<div class="backdrop" class:enabled={enabled} on:click={() => dialog.close()}></div>

<script lang="ts">

	// Components
	import Error from "./Error.svelte";
	import Button from "./Button.svelte";

	// Utils
    import { createPost } from "$lib/utils/api";
    import { goto } from "$app/navigation";
    import { MAX_FILE_SIZE } from "$lib/utils/constants";

	// Props
	export let enabled = false;

	let
		fileInput: HTMLInputElement,
		dialog: HTMLDialogElement,
		error = "";

	$: if (dialog && enabled) { error = ""; dialog.showModal(); }

	async function submit () {

		error = "";

		const file = fileInput.files?.[0];

		if (!file) {
			error = "No image selected.";
			return;
		}

		if (file.size > MAX_FILE_SIZE) {
			error = `Image must be smaller than ${Math.ceil(MAX_FILE_SIZE / Math.pow(10, 6))} MB.`;
			return;
		}

		const post = await createPost(file);

		if (typeof post !== "string") {
			goto(`/posts/${post.id}`);
		} else {
			error = post;
		}
	}

</script>

<style>

	.backdrop {
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		z-index: 4;
		pointer-events: none;
		backdrop-filter: blur(3px) opacity(0);
		transition: backdrop-filter .2s ease-in-out;
	}

	.backdrop.enabled {
		backdrop-filter: blur(3px) opacity(1);
	}

	@keyframes popup-open {
		0% {
			display: none;
			opacity: 0;
			transform: translateY(.5em) scale(99%);
		}
		100% {
			display: block;
			opacity: 1;
		}
	}

	@keyframes popup-close {
		0% {
			display: block;
			opacity: 1;
		}
		100% {
			display: none;
			opacity: 0;
			transform: translateY(.5em) scale(99%);
		}
	}

	dialog {
		color: var(--text-color);
		background-color: var(--background-color);
		border: 1px solid var(--primary-color);
		z-index: 5;
		animation: popup-close .3s ease-out;
		border-radius: var(--border-radius);
	}

	dialog[open] {
		animation: popup-open .45s cubic-bezier(0, 1.68, .43, 1.01);
	}

	/* dialog::backdrop {
		backdrop-filter: blur(5px);
	} */

	.dialog-content {
		display: flex;
		flex-direction: column;
		justify-content: flex-start;
		align-items: center;
	}

	.dialog-title {
		margin-top: 0;
	}

	.button-row {
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: center;
		margin-top: 1em;
		gap: 1em;
	}

</style>
