<template>
    <main class="bg-white dark:bg-slate-950 p-4 shadow rounded-lg">
        <section>
            <div class="flex space-x-4 mb-4">
                <h1 class="text-lg font-semibold dark:text-white">
                    <!-- Check if category and subCategory exist -->
                    {{
                        subCategory
                            ? `${subCategory.name}`
                            : category
                            ? category.name
                            : "Advertisements"
                    }}
                </h1>
            </div>

            <!-- Render Each Advertisement -->
            <div
                v-for="advertisement in advertisements.data"
                :key="advertisement.id"
            >
                <ListingItem :advertisement="advertisement" />
            </div>

            <!-- Pagination Links -->
            <div class="mt-4">
                <Pagination :links="advertisements.links" />
            </div>
        </section>
    </main>
</template>

<script setup>
import ListingItem from "./ListingItem.vue";
import Pagination from "./Pagination.vue";

const props = defineProps({
    advertisements: Object,
    category: Object,
    subCategory: Object,
});
</script>
