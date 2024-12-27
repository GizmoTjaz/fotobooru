<div id="container">
	<Header />
	<div id="content">
		<slot />
	</div>
</div>

<script lang="ts">

	// Modules
	import { onMount } from "svelte";

	// Components
	import Header from "$lib/components/Header.svelte";

	// Stores
	import { user } from "$lib/stores/user";

	// Utils
	import { getCurrentUser } from "$lib/utils/api";

	onMount(async () => {

		if (!document.cookie.includes("PHPSESSID"))
			return;

		const currentUser = await getCurrentUser();

		user.set(
			typeof currentUser !== "string"
				? currentUser
				: null
		);

	});

</script>

<style>

	:root {
		--text-color: #FFF;
		--secondary-text-color: rgba(153, 153, 153);
		--background-color: #111;
		--primary-color: rgb(204, 0, 255);
		--secondary-color: rgb(0, 162, 255);
		--primary-hover-color: rgb(122, 0, 153);
		--error-color: rgb(223, 72, 72);
		--disabled-color: rgb(43, 43, 43);
		--highlight-color: rgb(57, 0, 71);
		--border-radius: 3px;
	}

	:global(body) {
		background-color: var(--background-color);
		color: var(--text-color);
		margin: 0;
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: flex-start;
	}

	:global(body *) {
		/* font-family: Roboto, sans-serif; */
		font-family: sans-serif;
	}

	/* @font-face {
		src: url("/fonts/Roboto-Regular.ttf");
		font-family: "Roboto", sans-serif;
		font-weight: normal;
		font-style: normal;
	}

	@font-face {
		src: url("/fonts/Roboto-Italic.ttf");
		font-family: "Roboto", sans-serif;
		font-weight: 400;
		font-style: italic;
	}

	@font-face {
		src: url("/fonts/Roboto-Bold.ttf");
		font-family: "Roboto", sans-serif;
		font-weight: bold;
		font-style: normal;
	} */

	:global(form) {
		display: flex;
		flex-direction: column;
		justify-content: flex-start;
		align-items: center;
	}

	#container, #content {
		width: 100%;
		display: flex;
		flex-direction: column;
		justify-content: flex-start;
		align-items: center;
	}

	#container {
		box-sizing: border-box;
		padding: 0 .5em 0 .5em;
	}

	@media only screen and (min-width: 600px) {
		#container {
			padding: 0 1em 0 1em;
		}
	}

	@media only screen and (min-width: 1300px) {
		#container {
			max-width: 1300px;
		}
	}

</style>
