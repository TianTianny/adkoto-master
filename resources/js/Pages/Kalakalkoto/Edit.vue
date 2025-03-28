<template>
	<Head title="Kalakalkoto Edit" />

	<AuthenticatedLayout>
		<div
			class="max-w-4xl mx-auto my-2 h-full overflow-y-auto p-6 bg-white shadow-md rounded-lg scrollbar-thin">
			<h1 class="text-3xl font-bold mb-6">Edit Listing</h1>
			<form
				@submit.prevent="submitForm"
				class="space-y-6">
				<!-- Title -->
				<div>
					<label
						for="name"
						class="block text-sm font-medium text-gray-700"
						>Name of the Product</label
					>
					<input
						type="text"
						v-model="form.name"
						required
						class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
				</div>

				<!-- Category -->
				<div>
					<label
						for="category_id"
						class="block text-sm font-medium text-gray-700"
						>Category</label
					>
					<select
						v-model="form.category_id"
						class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
						<option
							value=""
							disabled>
							Select a category
						</option>
						<option
							v-for="category in categories"
							:key="category.id"
							:value="category.id">
							{{ category.name }}
						</option>
					</select>
				</div>

				<!-- Description -->
				<div>
					<label
						for="description"
						class="block text-sm font-medium text-gray-700"
						>Description</label
					>
					<textarea
						v-model="form.description"
						required
						rows="4"
						class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
				</div>

				<!-- Price -->
				<div>
					<label
						for="price"
						class="block text-sm font-medium text-gray-700"
						>Price</label
					>
					<input
						type="number"
						v-model="form.price"
						required
						class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
				</div>

				<!-- Location -->
				<div>
					<label
						for="location"
						class="block text-sm font-medium text-gray-700"
						>Location</label
					>
					<input
						type="text"
						v-model="form.location"
						required
						class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
				</div>

				<!-- Images -->
				<div>
					<label
						for="images"
						class="block text-sm font-medium text-gray-700"
						>Images</label
					>
					<input
						type="file"
						@change="handleFileUpload"
						multiple
						class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
				</div>

				<!-- Image Previews -->
				<div
					v-if="imagePreviews.length"
					class="mt-4">
					<h3 class="text-lg font-medium text-gray-700">Image Previews</h3>
					<div class="mt-2 flex flex-wrap space-x-2">
						<div
							v-for="(image, index) in imagePreviews"
							:key="index"
							class="relative">
							<img
								:src="image.url"
								alt="Preview"
								class="w-24 h-24 object-cover rounded-md shadow-sm" />
							<button
								@click="removeImage(index)"
								class="absolute top-0 right-0 bg-red-600 text-white rounded-full p-1 shadow-md">
								&times;
							</button>
						</div>
					</div>
				</div>

				<!-- Submit Button -->
				<button
					type="submit"
					:disabled="isLoading"
					:class="{
						'w-full py-2 px-4 bg-blue-300 text-white font-semibold rounded-md shadow-md hover:bg-blue-300 transition duration-300 ease-in-out cursor-not-allowed':
							isLoading,
						'w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 transition duration-300 ease-in-out':
							!isLoading,
					}">
					Submit
				</button>
			</form>
		</div>
	</AuthenticatedLayout>
	<UpdateProfileReminder />
</template>

<script setup>
import { ref } from "vue";
import { usePage, Head, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UpdateProfileReminder from "@/Components/UpdateProfileReminder.vue";
import { useToast } from "vue-toastification";
import axios from "axios";

const props = defineProps({
	categories: Array,
	item: Object,
});

const toast = useToast();
const imagePreviews = ref([]);
const form = useForm({
	id: props.item.id,
	category_id: props.item.category_id,
	name: props.item.name,
	description: props.item.description,
	price: props.item.price,
	location: props.item.location,
	images: [],
});

const handleFileUpload = (event) => {
	const files = Array.from(event.target.files);
	form.value.images.push(...files);

	imagePreviews.value.push(
		...files.map((file) => ({
			file,
			url: URL.createObjectURL(file),
		}))
	);
};

const removeImage = (index) => {
	form.value.images.splice(index, 1);
	URL.revokeObjectURL(imagePreviews.value[index].url);
	imagePreviews.value.splice(index, 1);
};

const isLoading = ref(false);

const submitForm = () => {
	isLoading.value = true;
	form.put(`/kalakalkoto/${form.id}`, {
		onSuccess: () => {
			toast.success("Advertisement updated successfully!");
		},
		onError: () => {
			toast.error("There was an error updating the advertisement.");
		},
	});
};

// const resetForm = () => {
//     form.value = {
//         category_id: props.item.category_id,
//         name: props.item.name,
//         description: props.item.description,
//         price: props.item.price,
//         location: props.item.location,
//         images: [],
//     };
//     imagePreviews.value = [];
// };
</script>
