<template>
	<Head title="View Advertisements" />

	<AuthenticatedLayout>
		<div class="max-w-7xl mx-auto h-full overflow-y-auto p-4 scrollbar-thin">
			<div class="grid grid-cols-12 gap-6">
				<FeaturedAds
					:featuredAds="featuredAds"
					class="col-span-12" />
				<AdvertisementCard
					:advertisements="advertisements"
					class="lg:col-span-8 col-span-12" />
				<div class="lg:col-span-4 hidden lg:block">
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
import SponsoredAds from "@/Components/Adkoto/SponsoredAds.vue";
import AdvertisementCard from "@/Components/Adkoto/AdvertisementCard.vue";
import ManageCard from "@/Components/Adkoto/ManageCard.vue";
import FeaturedAds from "@/Components/Adkoto/FeaturedAds.vue";

const { props } = usePage();
const advertisements = ref(props.advertisements);
const categories = ref(props.categories);

const featuredAds = ref(props.featuredAds);
const sponsoredAds = ref(props.sponsoredAds);
// const sponsoredAds = ref(props.sponsoredAds);

console.log(categories.value);
</script>
