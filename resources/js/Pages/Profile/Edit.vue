<!-- <script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Profile</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> -->

<script setup>
import DeactivateAccount from "./Partials/DeactivateAccount.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import Pages from "./Partials/Pages.vue";
import ToggleAccountPrivacy from "./Partials/ToggleAccountPrivacy.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { defineProps, watch } from "vue";

const props = defineProps({
	mustVerifyEmail: {
		type: Boolean,
	},
	status: {
		type: String,
	},
	isPrivate: {
		type: Boolean,
	},
});
</script>

<template>
	<div class="space-y-6">
		<div class="p-4 sm:p-8 bg-white dark:bg-slate-900 shadow sm:rounded-lg">
			<UpdateProfileInformationForm
				:must-verify-email="mustVerifyEmail"
				:status="status"
				class="max-w-xl" />
		</div>

		<div class="p-4 sm:p-8 bg-white dark:bg-slate-900 shadow sm:rounded-lg">
			<UpdatePasswordForm class="max-w-xl" />
		</div>

		<div class="p-4 sm:p-8 bg-white dark:bg-slate-900 shadow sm:rounded-lg">
			<ToggleAccountPrivacy
				:is-private="isPrivate"
				class="max-w-xl" />
		</div>

		<!-- <div class="p-4 sm:p-8 bg-white dark:bg-slate-900 shadow sm:rounded-lg">
            <DeleteUserForm class="max-w-xl" />
        </div> -->

		<div class="p-4 sm:p-8 bg-white dark:bg-slate-900 shadow sm:rounded-lg">
			<Pages class="max-w-xl" />
		</div>

		<div class="p-4 sm:p-8 bg-white dark:bg-slate-900 shadow sm:rounded-lg">
			<DeactivateAccount class="max-w-xl" />
		</div>
	</div>
</template>
