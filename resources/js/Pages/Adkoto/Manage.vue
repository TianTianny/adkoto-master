<template>
    <Head title="Adkoto" />

    <AuthenticatedLayout>
        <div
            class="max-w-7xl mx-auto h-full overflow-y-auto p-4 scrollbar-thin"
        >
            <div class="grid grid-cols-12 gap-6">
                <FeaturedAds :featuredAds="featuredAds" class="col-span-12" />
                <div class="col-span-8">
                    <div class="bg-blue-400 p-4">
                        <h1 class="text-lg text-white font-semibold">
                            You can manage your Advertisements
                        </h1>
                    </div>
                    <!-- <div v-if="advertisements.length > 0">
                        <Listings :advertisements="advertisements" />
                    </div>
                    <div v-else class="text-center text-gray-600 py-4">
                        <div class="bg-white p-4 shadow rounded-lg">
                            You don't have any advertisements posted.
                        </div>
                    </div> -->
                    <Listings
                        :advertisements="advertisements"
                        class="lg:col-span-8 col-span-12"
                    />
                </div>
                <div class="col-span-4">
                    <ManageCard class="my-2" />
                    <SponsoredAds :sponsoredAds="sponsoredAds" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <UpdateProfileReminder />
</template>

<script setup>
import { ref } from "vue";
import { usePage, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UpdateProfileReminder from "@/Components/UpdateProfileReminder.vue";
import Listings from "@/Components/Adkoto/Listings.vue";
import SponsoredAds from "@/Components/Adkoto/SponsoredAds.vue";
import ManageCard from "@/Components/Adkoto/ManageCard.vue";
import FeaturedAds from "@/Components/Adkoto/FeaturedAds.vue";

const { props } = usePage();
const advertisements = ref(props.advertisements);
const featuredAds = ref(props.featuredAds);
const sponsoredAds = ref(props.sponsoredAds);
</script>
