<svelte:head>
    <title>Signup</title> 
</svelte:head>
<Heading>Signup</Heading>
<Error>{error}</Error>
<form on:submit|preventDefault={submitForm}>
	<input type="submit" hidden />
	<Input
		autofocus
		name="name"
		label="Name"
		bind:value={name}
	/>
	<Input
		type="password"
		name="password"
		label="Password"
		bind:value={password}
	/>
	<div class="controls">
		<Button on:click={submitForm}>Sign up</Button>
	</div>
</form>

<script lang="ts">

	// Modules
	import { goto } from "$app/navigation";

	// Stores
	import { user as userStore } from "$lib/stores/user";

	// Components
	import Heading from "$lib/components/Heading.svelte";
	import Input from "$lib/components/Input.svelte";
	import Error from "$lib/components/Error.svelte";
	import Button from "$lib/components/Button.svelte";

	// Utils
	import { signup } from "$lib/utils/api";

	let
		name = "",
		password = "",
		error = "";

	async function submitForm () {

		error = "";

		if (name.trim().length == 0) {
			error = "Name is required.";
			return;
		}

		if (name.trim().length > 20) {
			error = "Names can only be up to 20 characters long.";
			return;
		}

		if (password.trim().length == 0) {
			error = "Password is required.";
			return;
		}

		const user = await signup(name.trim(), password);

		if (typeof user !== "string") {
			userStore.set(user);
			goto("/");
		} else {
			error = user;
		}
	}

</script>

<style>

	form {
		width: 100%;
	}

	.controls {
		margin-top: 1em;
	}

	@media only screen and (min-width: 400px) {
		form {
			width: auto;
		}
	}

</style>
