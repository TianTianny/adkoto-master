<script setup>
import { ref } from "vue";
import InputTextarea from "@/Components/InputTextarea.vue";
import TextInput from "@/Components/TextInput.vue";

defineProps({
    form: Object,
});

// const maxCharacters = 20;
// const form = ref({
//     description: "",
// });
// const remainingCharacters = ref(maxCharacters);

// const updateCharacterCount = () => {
//     remainingCharacters.value = maxCharacters - form.value.description.length;
// };
</script>

<template>
    <div class="space-y-6">
        <div class="mb-3 dark:text-gray-100">
            <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >Group Chat Name</label
            >
            <TextInput
                type="text"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                v-model="form.name"
                required
                autofocus
            />
        </div>

        <div class="mb-3 dark:text-gray-100">
            <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >Group Chat Desription</label
            >
            <InputTextarea
                v-model="form.description"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
            ></InputTextarea>
            <!-- <div class="mt-1 text-gray-600 text-sm">
                {{ remainingCharacters }} characters remaining
            </div> -->
        </div>
    </div>
</template>

<style scoped></style>
