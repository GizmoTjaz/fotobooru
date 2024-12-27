export function formatTimestamp (timestamp: number): string {
	// return new Date(timestamp * 1000 - (new Date().getTimezoneOffset() * 60 * 1000))
	return new Date(timestamp * 1000)
		.toLocaleDateString(undefined, {
			year: "numeric",
			month: "2-digit",
			day: "2-digit",
			hour: "2-digit",
			minute: "2-digit",
			timeZoneName: "short"
		})
		.split(" ")
		.slice(0, -1)
		.join(" ");
}
