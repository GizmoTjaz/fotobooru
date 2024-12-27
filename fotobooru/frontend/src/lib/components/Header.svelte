<header>
	<nav>
		<Button href="/">Gallery</Button>
		<div id="account-container">
			{#if $user}
				<button class="name" on:click={visitProfile}>{$user.name}</button>
				<Button on:click={logOut}>Log out</Button>
			{:else}
				<Button href="/signup">Sign up</Button>
				<Button href="/login">Log in</Button>
			{/if}
		</div>
	</nav>
</header>

<script>

	// Modules
    import { goto } from "$app/navigation";

	// Stores
	import { user } from "$lib/stores/user";
	
	// Components
	import Button from "$lib/components/Button.svelte";
    import { logout } from "$lib/utils/api";

	function logOut () {

		logout();

		user.set(null);
		document.cookie = "";
	}

	function visitProfile () {
		if ($user !== null)
			goto(`/users/${$user.id}`);
	}

</script>

<style>

	header {
		width: 100%;
	}

	header nav {
		width: 100%;
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		align-items: center;
		padding: 1em 0 1em 0;
	}

	.name {
		color: var(--text-color);
		font-size: 1.25em;
		background-color: var(--highlight-color);
		border: 1px solid var(--highlight-color);
		border-radius: 5px;
		padding: .2em .4em .2em .4em;
		margin-right: .3em;
		cursor: pointer;
	}

	.name:hover {
		background-color: var(--primary-color);
	}

</style>
