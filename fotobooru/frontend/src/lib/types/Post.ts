// Types
import type { User } from "./User";
import type { Reply } from "./Reply";

export interface Post {
	id: number;
	uploader: User;
	media_url: string;
	replies: Reply[];
	created_at: number;
}
