<template>
    <Head title="Live Auction" />

    <AuthenticatedLayout>
        <div
            class="max-w-7xl mx-auto h-full overflow-y-auto p-4 scrollbar-thin"
        >
            <PageSelector />
            <AuctionMenu />

            <div
                class="bg-white dark:bg-slate-950 flex flex-col gap-4 p-6 rounded-lg shadow-sm"
            >
                <!-- <YouTubeLiveStream :channelId="youtubeChannelId" /> -->
                <VideoSDKLiveStream />

                <ShowWindow
                    v-if="!noActiveBidding"
                    :item="item"
                    :highBid="highBid"
                    :bids="bids"
                    :user="user"
                    :walletBalance="walletBalance"
                />

                <div v-else class="text-center text-gray-500">
                    <p>No active bidding yet.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <UpdateProfileReminder />
</template>

<script setup>
import YouTubeLiveStream from "@/Components/Auction/YouTubeLiveStream.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { ref, defineProps, onMounted, onUnmounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UpdateProfileReminder from "@/Components/UpdateProfileReminder.vue";
import AuctionMenu from "@/Components/Auction/AuctionMenu.vue";
import PageSelector from "@/Components/Kalakalkoto/PageSelector.vue";
import ShowWindow from "@/Components/Auction/ShowWindow.vue";
import axios from "axios";
import VideoSDKLiveStream from "@/Components/Auction/VideoSDKLiveStream.vue";

const { props } = usePage();

const updatedItem = ref(props.item);
const updatedHighBid = ref(props.highBid);
const updatedBids = ref(props.bids);
const updatedUser = ref(props.user);
const updatedWalletBalance = ref(props.walletBalance);
const noActiveBidding = ref(props.noActiveBidding);
const youtubeChannelId = ref(props.youtubeChannelId);
let interval = null;

const fetchShowWindowData = async () => {
    try {
        const response = await axios.get("/auction/stream/bidlist");
        const data = response.data;

        updatedItem.value = data.item;
        updatedHighBid.value = data.highBid;
        updatedBids.value = data.bids;
        updatedUser.value = data.user;
        updatedWalletBalance.value = data.walletBalance;
        noActiveBidding.value = data.noActiveBidding;
    } catch (error) {
        console.error("Error fetching auction data:", error);
    }
};

onMounted(() => {
    // if (!noActiveBidding.value) {
    fetchShowWindowData();
    interval = setInterval(fetchShowWindowData, 20000);
    // }
});

onUnmounted(() => {
    clearInterval(interval);
});
</script>

<style scoped></style>
