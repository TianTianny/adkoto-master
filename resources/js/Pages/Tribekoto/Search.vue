<script setup>
import UserListItem from "@/Components/Tribekoto/UserListItem.vue";
import GroupItem from "@/Components/Tribekoto/GroupItem.vue";
import PostList from "@/Components/Tribekoto/PostList.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UpdateProfileReminder from "@/Components/UpdateProfileReminder.vue";

const props = defineProps({
	search: String,
	users: Array,
	groups: Array,
	posts: Object,
});
</script>

<template>
	<AuthenticatedLayout>
		<div class="p-4 h-full overflow-y-auto scrollbar-thin">
			<div
				v-if="!search.startsWith('#')"
				class="grid grid-cols-1 sm:grid-cols-2 gap-3">
				<div
					class="shadow bg-white border dark:bg-slate-950 dark:border-slate-900 dark:text-gray-100 p-3 rounded mb-3">
					<h2 class="text-lg font-bold">Users</h2>
					<div class="grid-cols-2">
						<UserListItem
							v-if="users.length"
							v-for="user of users"
							:user="user" />
						<div
							v-else
							class="py-8 text-center text-gray-500">
							No users were found.
						</div>
					</div>
				</div>
				<!-- <div class="shadow bg-white p-3 rounded mb-3"> -->
				<div
					class="shadow bg-white border dark:bg-slate-950 dark:border-slate-900 dark:text-gray-100 p-3 rounded mb-3">
					<h2 class="text-lg font-bold">Groups</h2>
					<div class="grid-cols-2">
						<GroupItem
							v-if="groups.length"
							v-for="group of groups"
							:group="group" />
						<div
							v-else
							class="py-8 text-center text-gray-500">
							No groups were found.
						</div>
					</div>
				</div>
			</div>

			<div>
				<h2 class="text-lg font-bold">Posts</h2>
				<PostList
					v-if="posts.data.length"
					:posts="posts.data"
					class="flex-1" />
				<div
					v-else
					class="py-8 text-center text-gray-500">
					No posts were found.
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
	<UpdateProfileReminder />
</template>

<style scoped></style>
