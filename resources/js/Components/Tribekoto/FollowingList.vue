<script setup>
import FollowingListItems from "@/Components/Tribekoto/FollowingListItems.vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import GroupListItems from "@/Components/Tribekoto/GroupListItems.vue";

defineProps({
    users: Array,
    suggestedUsers: Array,
});
</script>

<template>
    <!-- <div
		class="px-3 bg-white dark:bg-slate-950 dark:border-slate-900 dark:text-gray-100 rounded border h-full py-3 overflow-hidden"> -->
    <div
        class="px-3 md:bg-transparent bg-white dark:bg-slate-950 border-r dark:border-slate-900 dark:text-gray-100 rounded h-full py-3 overflow-hidden"
    >
        <div class="block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">My Followings</h2>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 transition-all"
                            :class="open ? 'rotate-90 transform' : ''"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 4.5l7.5 7.5-7.5 7.5"
                            />
                        </svg>
                    </div>
                </DisclosureButton>
                <DisclosurePanel>
                    <FollowingListItems :users="users" />
                    <h2 class="text-xl font-bold rounded text-center p-2">
                        People you may know
                    </h2>
                    <FollowingListItems :users="suggestedUsers" />
                </DisclosurePanel>
            </Disclosure>
        </div>
        <div class="h-full overflow-hidden flex-col hidden lg:flex">
            <div class="h-full items-center">
                <h2 class="text-xl font-bold">My Followings</h2>
                <FollowingListItems :users="users" />
            </div>
            <div class="h-full items-center md:border-t-2 md:pt-8">
                <h2 class="text-xl font-bold">People you may know</h2>
                <FollowingListItems :users="suggestedUsers" />
            </div>
        </div>
    </div>
</template>

<style scoped></style>
