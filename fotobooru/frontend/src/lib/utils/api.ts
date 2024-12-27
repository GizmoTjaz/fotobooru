// Utils
import { API_ENDPOINT, MAX_FILE_SIZE } from "./constants";

// Modules
import { get } from "svelte/store";

// Stores
import { cache as postCache } from "$lib/stores/post";
import { cache as userCache } from "$lib/stores/user";

// Types
import type { User } from "$lib/types/User";
import type { Post } from "$lib/types/Post";
import type { Reply } from "$lib/types/Reply";

export async function getCurrentUser (): Promise<User | string> {
	try {
		
		const res = await fetch(`${API_ENDPOINT}/users/@me`);;

		if (res.status === 200) {
			return (await res.json()) as User;
		} else {
			return await res.text();
		}

	} catch (err) {
		console.error(err);
		return "Unknown error occurred.";
	}
}


export async function login (name: string, password: string): Promise<User | string> {
	try {
		
		const res = await fetch(`${API_ENDPOINT}/users/login`, {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
			},
			body: encodeURI(`name=${name}&password=${password}`)
		});

		if (res.status === 200) {
			return (await res.json()) as User;
		} else {
			return await res.text();
		}

	} catch (err) {
		console.error(err);
		return "Unknown error occurred.";
	}
}

export async function logout (): Promise<User | string> {
	try {
		
		const res = await fetch(`${API_ENDPOINT}/users/@me`, {
			method: "DELETE"
		});

		return await res.text();

	} catch (err) {
		console.error(err);
		return "Unknown error occurred.";
	}
}

export async function signup (name: string, password: string): Promise<User | string> {
	try {
		
		const res = await fetch(`${API_ENDPOINT}/users`, {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
			},
			body: encodeURI(`name=${name}&password=${password}`)
		});

		if (res.status === 200) {
			return (await res.json()) as User;
		} else {
			return await res.text();
		}

	} catch (err) {
		console.error(err);
		return "Unknown error occurred.";
	}
}

export async function getAllPosts (): Promise<Post[]> {

	const cache = get(postCache);

	try {

		const
			res = await fetch(`${API_ENDPOINT}/posts`),
			posts = await res.json() as Post[];

		posts.forEach(post => cache.set(post.id, post));

		return posts;
	} catch (err) {
		console.error(err);
		return [];
	}
}

export async function getAllPostsByUploader (uploaderId: number): Promise<Post[]> {

	const cache = get(postCache);

	try {

		const
			res = await fetch(`${API_ENDPOINT}/users/${uploaderId}/posts`),
			posts = await res.json() as Post[];

		posts.forEach(post => cache.set(post.id, post));

		return posts;
	} catch (err) {
		console.error(err);
		return [];
	}
}

export async function getPost (id: number): Promise<Post | string> {

	const
		users = get(userCache),
		posts = get(postCache);

	if (posts.has(id))
		return posts.get(id) as Post;

	try {

		const res = await fetch(`${API_ENDPOINT}/posts/${id}`);

		if (res.status === 200) {
			
			const post = (await res.json()) as Post;
			posts.set(id, post);
			users.set(post.uploader.id, post.uploader);

			post.replies.forEach(reply => {
				users.set(reply.poster.id, reply.poster);
			});

			return post;
		} else {
			return "Post not found.";
		}

	} catch (err) {
		console.error(err);
		return "Unknown error occurred.";
	}
}

export async function getUser (id: number): Promise<User | string> {

	const cache = get(userCache);

	if (cache.has(id))
		return cache.get(id) as User;

	try {

		const res = await fetch(`${API_ENDPOINT}/users/${id}`);

		if (res.status === 200) {
			
			const user = (await res.json()) as User;
			cache.set(id, user);

			return user;
		} else {
			return "User not found.";
		}

	} catch (err) {
		console.error(err);
		return "Unknown error occurred.";
	}
}

export async function createReply (post: Post, content: string): Promise<Reply | string> {
	
	const cache = get(postCache);
	
	try {

		const res = await fetch(`${API_ENDPOINT}/posts/${post.id}/replies`, {
			method: "POST",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify({ content })
		});

		if (res.status === 200) {

			const reply = await res.json() as Reply;

			if (cache.has(post.id)) {
				
				const cachedPost = cache.get(post.id);

				if (cachedPost) {
					cachedPost.replies.push(reply);
					cache.set(post.id, cachedPost);
				}
			}

			return reply;
		} else {
			return await res.text();
		}

	} catch (err) {
		console.error(err);
		return "Unknown error occurred.";
	}
}

export async function createPost (file: File): Promise<Post | string> {

	const cache = get(postCache);
	
	try {

		const formData = new FormData();
		formData.append("MAX_FILE_SIZE", `${MAX_FILE_SIZE}`);
		formData.append("media", file);

		const res = await fetch(`${API_ENDPOINT}/posts`, {
			method: "POST",
			body: formData
		});

		if (res.status === 200) {

			const post = (await res.json()) as Post;

			cache.set(post.id, post);

			return post;
		} else {
			return await res.text();
		}

	} catch (err) {
		console.error(err);
		return "Unknown error occurred.";
	}
}

export async function deleteReply (post: Post, reply: Reply): Promise<boolean> {

	const cache = get(postCache);

	try {

		const res = await fetch(`${API_ENDPOINT}/posts/${post.id}/replies/${reply.id}`, {
			method: "DELETE"
		});

		if (res.status === 200) {

			const cachedPost = cache.get(post.id);

			if (cachedPost) {
				cachedPost.replies = cachedPost.replies.filter(r => r.id !== reply.id);
				cache.set(cachedPost.id, cachedPost);
			}
			
			return true;
		}

	} catch (err) {
		console.error(err);
	}

	return false;
}

export async function deletePost (post: Post): Promise<boolean> {

	const cache = get(postCache);

	try {

		const res = await fetch(`${API_ENDPOINT}/posts/${post.id}`, {
			method: "DELETE"
		});

		if (res.status === 200) {
			cache.delete(post.id);
			return true;
		}

	} catch (err) {
		console.error(err);
	}

	return false;
}
