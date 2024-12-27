<fieldset>
	<legend>{label}</legend>
	<!-- svelte-ignore a11y-autofocus -->
	<input
		{placeholder}
		{ ...{ type: type } }
		bind:value={value}
		{ ...$$restProps }
	/>
</fieldset>

<script lang="ts">

	// Props
	export let label: string;
	export let placeholder = "";
	export let type = "text";
	// export let autofocus = false;

	export let value: string;

</script>

<style>

	fieldset, legend, input {
		width: 100%;
	}

	fieldset {
		border: 1px dashed var(--disabled-color);
		box-sizing: border-box;
	}

	legend {
		font-weight: bold;
	}

	input {
		font-size: 1.5em;
		color: var(--text-color);
		background-color: var(--disabled-color);
		border: 1px solid var(--primary-color);
		border-radius: var(--border-radius);
	}

</style>
