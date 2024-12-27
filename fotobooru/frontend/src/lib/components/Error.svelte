<span><slot /></span>

<style>

	span {
		max-width: 100%;
		color: var(--error-color);
		font-style: italic;
		font-size: 1.2em;
		min-height: 1.2em; /* Error labels shouldn't cause document shifts when hidden */
		padding: .5em 0 .5em 0;
		word-wrap: break-word;
	}

</style>
