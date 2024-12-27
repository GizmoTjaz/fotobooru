// Types
import type { User } from "./User";

export interface Reply {
	id: number;
	poster: User;
	content: string;
	created_at: number;
}
