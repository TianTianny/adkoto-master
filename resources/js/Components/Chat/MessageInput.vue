<template>
    <div class="border-t-2 border-gray-200 px-4">
        <div
            class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 relative"
        >
            <!-- <button
                type="button"
                class="inline-flex justify-center p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
            >
                <MicIcon class="dark:text-gray-200" />
                <span class="sr-only">Record audio</span>
            </button> -->
            <!-- Mobile: Toggle More Options -->
            <button
                type="button"
                class="p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 block sm:hidden"
                @click="showMobileActions = !showMobileActions"
            >
                <DotsVerticalIcon class="dark:text-gray-200" />
                <span class="sr-only">More options</span>
            </button>

            <!-- <button
                type="button"
                class="p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                @click="triggerFileInput"
            >
                <PaperClipIcon class="dark:text-gray-200" />
                <span class="sr-only">Attach file</span>
            </button>
            <input
                type="file"
                ref="fileInput"
                @change="handleFileChange"
                class="hidden"
                multiple
                accept="image/*, video/*"
            />
            <button
            type="button"
            class="p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
            @click="toggleEmojiPicker"
            >
            <EmojiIcon class="dark:text-gray-200" />
            <span class="sr-only">Add emoji</span>
        </button> -->
            <!-- File and Emoji Buttons (visible on sm+ or toggled on mobile) -->
            <div
                :class="[
                    'flex items-center gap-1',
                    showMobileActions ? 'block' : 'hidden',
                    'sm:flex',
                ]"
            >
                <!-- File Upload Button -->
                <button
                    type="button"
                    class="p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                    @click="triggerFileInput"
                >
                    <PaperClipIcon class="dark:text-gray-200" />
                    <span class="sr-only">Attach file</span>
                </button>
                <input
                    type="file"
                    ref="fileInput"
                    @change="handleFileChange"
                    class="hidden"
                    multiple
                    accept="image/*, video/*"
                />

                <!-- Emoji Picker Button -->
                <button
                    type="button"
                    class="p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
                    @click="toggleEmojiPicker"
                >
                    <EmojiIcon class="dark:text-gray-200" />
                    <span class="sr-only">Add emoji</span>
                </button>
            </div>
            <!-- <button
            type="button"
            class="p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
            @click="triggerFileInput"
        >
            <CameraIcon class="dark:text-gray-200" />
            <span class="sr-only">Upload image</span>
        </button> -->
            <!-- Emoji Picker Trigger -->

            <!-- Emoji Picker Component -->
            <emoji-picker
                v-if="showEmojiPicker"
                class="absolute bottom-16 left-0 z-10"
                @emoji-click="addEmoji"
            ></emoji-picker>

            <!-- Textarea for message input -->
            <!-- <textarea
				v-model="newMessage"
				@keyup.enter="sendMessage"
				id="chat"
				rows="1"
				class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
				placeholder="Message..."></textarea> -->
            <textarea
                v-model="newMessage"
                @keydown.enter.exact.prevent="sendMessage"
                @keydown.enter.shift.exact="addNewLine"
                id="chat"
                rows="1"
                class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Message..."
            ></textarea>

            <!-- Send Button -->
            <button
                @click="sendMessage"
                type="button"
                class="inline-flex justify-center p-2 text-blue-600 rounded-full cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600"
            >
                <SendIcon />
                <span class="sr-only">Send message</span>
            </button>
        </div>
        <!-- File Previews -->
        <div
            v-if="selectedFiles.length"
            class="mt-2 flex flex-wrap justify-center gap-2"
        >
            <div
                v-for="(file, index) in selectedFiles"
                :key="index"
                class="relative"
            >
                <img
                    v-if="isImage(file)"
                    :src="filePreview(file)"
                    class="h-32 w-32 object-cover rounded-lg"
                />
                <video
                    v-if="isVideo(file)"
                    :src="filePreview(file)"
                    class="h-32 w-32 object-cover rounded-lg"
                    controls
                />
                <button
                    @click="removeFile(index)"
                    class="absolute top-1 right-1 bg-gray-600 text-white p-1 rounded-full"
                >
                    &times;
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits } from "vue";
import { usePage } from "@inertiajs/vue3";
import axiosClient from "@/axiosClient.js";
import MicIcon from "@/Components/Icons/MicIcon.vue";
import PaperClipIcon from "@/Components/Icons/PaperClipIcon.vue";
import CameraIcon from "@/Components/Icons/CameraIcon.vue";
import EmojiIcon from "@/Components/Icons/EmojiIcon.vue";
import SendIcon from "@/Components/Icons/SendIcon.vue";
import "emoji-picker-element";
import DotsVerticalIcon from "../Icons/DotsVerticalIcon.vue";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    conversation: {
        type: Object,
        required: true,
    },
});

const showMobileActions = ref(false);

const authUser = usePage().props.auth.user;

const emit = defineEmits(["message-sent"]);
const newMessage = ref("");
const showEmojiPicker = ref(false);
const user = props.user;
const conversation = props.conversation;
const selectedFiles = ref([]);
const fileInput = ref(null);

const addNewLine = (event) => {
    event.preventDefault();
    newMessage.value += "\n";
};

function triggerFileInput() {
    fileInput.value.click();
}

function handleFileChange(event) {
    const files = Array.from(event.target.files);
    selectedFiles.value.push(...files);
}

function removeFile(index) {
    selectedFiles.value.splice(index, 1);
}

function isImage(file) {
    return file && file.type.startsWith("image/");
}

function isVideo(file) {
    return file && file.type.startsWith("video/");
}

function filePreview(file) {
    return URL.createObjectURL(file);
}

function toggleEmojiPicker() {
    showEmojiPicker.value = !showEmojiPicker.value;
}

function addEmoji(event) {
    const emoji = event.detail.unicode || event.detail.emoji;
    newMessage.value += emoji;
    toggleEmojiPicker();
}

// Function to send a new message
async function sendMessage() {
    if (!user.id || !conversation.id) {
        console.error("User or Conversation information is missing.");
        return;
    }

    if (newMessage.value.trim() === "" && !selectedFiles.value.length) return;

    const formData = new FormData();
    formData.append("message", newMessage.value);
    formData.append("receiver_id", user.id);
    formData.append("conversation_id", conversation.id);

    selectedFiles.value.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });

    // const tempMessage = {
    // 	id: Date.now(),
    // 	message: newMessage.value,
    // 	sender_id: user.id,
    // 	conversation_id: conversation.id,
    // 	files: selectedFiles.value.map((file) => URL.createObjectURL(file)),
    // };

    const tempMessage = {
        id: Date.now(),
        sender_id: authUser.id,
        sender: { id: authUser.id, name: "You" },
        message: newMessage.value,
        created_at: new Date().toISOString(),
        attachments: [],
        temp: true,
    };

    emit("message-sent", tempMessage);
    newMessage.value = "";
    selectedFiles.value = [];

    try {
        await axiosClient.post(
            `/chat/conversations/${conversation.id}/messages`,
            formData
        );
    } catch (error) {
        console.error("Error sending message:", error);
    }
}
</script>
