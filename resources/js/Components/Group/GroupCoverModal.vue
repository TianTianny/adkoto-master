<template>
	<Dialog
		:open="isOpen"
		@close="closeModal"
		class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-10">
		<DialogPanel class="relative w-full h-full mx-auto p-1 bg-white rounded-lg">
			<!-- Full Cover Image -->
			<img
				:src="imageSrc"
				class="w-full h-full object-cover"
				alt="Cover Image" />

			<!-- Ellipsis Button for Actions -->
			<div class="absolute top-4 right-4">
				<Link
					:href="
						route('group.coverPage', {
							group: groupId,
						})
					">
					<button class="text-white p-2 bg-gray-800 rounded-full hover:bg-gray-700">
						Update Group's Cover Photo
					</button>
				</Link>
			</div>

			<!-- Close Button -->
			<button
				@click="closeModal"
				class="absolute top-4 left-4 text-white">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					class="h-6 w-6"
					fill="none"
					viewBox="0 0 24 24"
					stroke="currentColor">
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
		</DialogPanel>
	</Dialog>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from "@headlessui/vue";

const authUser = usePage().props.auth.user;

const props = defineProps({
	imageSrc: {
		type: String,
		required: true,
	},
	isOpen: {
		type: Boolean,
		required: true,
	},
	groupId: {
		type: [Number, String],
		required: false,
	},
});

const emit = defineEmits(["close"]);

const closeModal = () => {
	emit("close");
};
</script>
