<template>
    <div class="relative">
        <!-- Notification Bell Icon/Button -->
        <!-- <button
			@click="showDropdown = !showDropdown"
			class="relative block">
			<img
				:src="notificationIcon"
				class="md:h-auto md:w-12 xl:w-[58px]"
				alt="Notifications" /> -->
        <button
            @click="toggleDropdown"
            class="relative block bg-white p-0.5 rounded-full hover:bg-red-500 hover:text-white hover:border-red-700 hover:shadow-md hover:scale-105 transition duration-200 ease-in-out"
        >
            <img
                :src="notificationIcon"
                class="md:h-auto md:w-12 xl:w-10"
                alt="Notifications"
            />
            <!-- Unread count indicator -->
            <div
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 rounded-full"
            ></div>
        </button>

        <!-- Dropdown Panel -->
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-if="showDropdown"
                ref="dropdownRef"
                class="origin-top-right absolute right-0 mt-12 w-[400px] rounded-md shadow-lg bg-white dark:bg-slate-950 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
            >
                <div class="py-1">
                    <div
                        class="sticky flex justify-between top-0 bg-white dark:bg-slate-950 dark:text-white shadow-sm z-10 px-4 py-2"
                    >
                        <h2 class="text-xl font-bold">Notifications</h2>
                    </div>
                    <div v-if="loading" class="p-4 text-center text-gray-500">
                        Loading...
                    </div>
                    <template v-else>
                        <template v-if="notifications.length === 0">
                            <div class="p-4 text-center text-gray-500">
                                No notifications
                            </div>
                        </template>
                        <ul v-else class="divide-y divide-gray-200">
                            <li
                                v-for="notification in notifications.slice(
                                    0,
                                    5
                                )"
                                :key="notification.id"
                                class="py-2"
                            >
                                <div
                                    class="hover:bg-gray-100 dark:hover:bg-slate-800 dark:hover:text-white transition-colors"
                                >
                                    <!-- <a
                                        :href="notification.url"
                                        class="flex items-center hover:cursor-pointer gap-3 justify-start px-4 py-2 text-sm text-gray-700"
                                        @click="markAsRead(notification)"
                                    > -->
                                    <Link
                                        :href="notification.data.route"
                                        class="flex items-center hover:cursor-pointer gap-3 justify-start px-4 py-2 text-sm text-gray-700"
                                        @click="markAsRead(notification)"
                                    >
                                        <!-- Display user profile -->
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <img
                                                v-if="
                                                    notification.data.user_id &&
                                                    userProfile[
                                                        notification.data
                                                            .user_id
                                                    ]
                                                "
                                                :src="
                                                    userProfile[
                                                        notification.data
                                                            .user_id
                                                    ].avatar_url
                                                "
                                                class="h-10 w-10 rounded-full"
                                                alt="User Avatar"
                                            />
                                            <span
                                                v-else
                                                class="text-sm text-gray-500 dark:text-white"
                                            >
                                                Loading profile...
                                            </span>
                                            <div class="absolute">
                                                <template
                                                    v-if="
                                                        notification.data
                                                            .reaction_image
                                                    "
                                                >
                                                    <img
                                                        :src="
                                                            notification.data
                                                                .reaction_image
                                                        "
                                                        :alt="
                                                            notification.data
                                                                .reaction
                                                        "
                                                        class="w-6 h-6 object-contain relative top-3 left-4"
                                                    />
                                                </template>

                                                <template
                                                    v-else-if="
                                                        notification.data.icon
                                                    "
                                                >
                                                    <span
                                                        class="w-6 h-6 flex items-center justify-center text-md relative top-3 left-4"
                                                    >
                                                        {{
                                                            notification.data
                                                                .icon
                                                        }}
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            <!-- Notification Icon based on type -->
                                            <template
                                                v-if="
                                                    notification.type ===
                                                    'App\Notifications\FollowUser'
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6 text-blue-500"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                                    ></path>
                                                </svg>
                                            </template>
                                            <p
                                                :class="{
                                                    'text-base': true,
                                                    'font-semibold':
                                                        notification.read_at ===
                                                        null,
                                                    'font-light':
                                                        notification.read_at !==
                                                        null,
                                                    'text-gray-900 dark:text-white': true,
                                                }"
                                            >
                                                {{ notification.data.message }}
                                            </p>
                                            <p
                                                class="text-gray-400 hover:cursor-pointer text-xs flex items-center justify-between pl-1"
                                            >
                                                {{
                                                    dayjs(
                                                        notification.created_at
                                                    ).fromNow()
                                                }}
                                            </p>
                                        </div>
                                    </Link>
                                </div>
                            </li>
                        </ul>
                    </template>
                    <div class="py-2 px-4 text-center">
                        <Link
                            :href="route('notifications.fetchAllNotifications')"
                            class="text-sm font-medium w-full text-blue-600 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md px-4 py-2 hover:bg-gray-200 dark:hover:bg-slate-700 dark:hover:text-white transition-colors block text-center"
                        >
                            View all Notifications
                        </Link>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Link } from "@inertiajs/vue3";
import axiosClient from "@/axiosClient.js";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";

dayjs.extend(relativeTime);

const showDropdown = ref(false);
const dropdownRef = ref(null);
const notifications = ref([]);
const loading = ref(true);
const userProfile = ref({});

import notificationIcon from "/public/img/icons/notification-bk.png";

const unreadCount = computed(() => {
    return notifications.value.filter((notification) => !notification.read_at)
        .length;
});

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        showDropdown.value = false;
    }
};

const toggleDropdown = () => {
    // If dropdown is open, don't toggle it again
    if (!showDropdown.value) {
        showDropdown.value = true;
    }
};

const fetchNotifications = async () => {
    try {
        const response = await axiosClient.get("/notifications");
        notifications.value = response.data;

        // Fetch user profiles for notifications
        await Promise.all(
            response.data.map((notification) => {
                if (
                    notification.data.user_id &&
                    !userProfile.value[notification.data.user_id]
                ) {
                    return fetchUserProfile(notification.data.user_id);
                }
            })
        );
    } catch (error) {
        console.error("Failed to fetch notifications:", error);
        notifications.value = [];
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    await fetchNotifications();
    document.addEventListener("mousedown", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("mousedown", handleClickOutside);
});

// const markAsRead = (notification) => {
//     console.log("Marking notification as read:", notification);
// };

const fetchUserProfile = async (userId) => {
    try {
        const response = await axiosClient.get(`/users/${userId}`);
        userProfile.value[userId] = response.data;
    } catch (error) {
        console.error(
            `Failed to fetch user profile for user ${userId}:`,
            error
        );
        userProfile.value[userId] = null;
    }
};

const markAsRead = async (notification) => {
    if (!notification.read_at) {
        try {
            await axiosClient.post(
                `/notifications/${notification.id}/mark-as-read`
            );
            notification.read_at = new Date().toISOString();
            window.location.href = notification.data.route;
        } catch (error) {
            console.error("Failed to mark notification as read", error);
        }
    } else {
        window.location.href = notification.data.route;
    }
};
</script>
