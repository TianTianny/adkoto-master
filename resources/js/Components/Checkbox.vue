<script setup>
import { computed } from "vue";

const emit = defineEmits(["update:checked"]);

const props = defineProps({
	checked: {
		type: [Array, Boolean],
		required: true,
	},
	value: {
		default: null,
	},
});

const proxyChecked = computed({
	get() {
		return props.checked;
	},

	set(val) {
		emit("update:checked", val);
	},
});
</script>

<template>
	<input
		type="checkbox"
		:value="value"
		v-model="proxyChecked"
		class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-blue-primary shadow-sm focus:ring-blue-primary dark:focus:ring-blue-primary dark:focus:ring-offset-gray-800" />
</template>
