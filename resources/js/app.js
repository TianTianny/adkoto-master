import "./bootstrap";
import "flowbite";

import {
    Livewire,
    Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";
// import Clipboard from "@ryangjchandler/alpine-clipboard";

// Alpine.plugin(Clipboard);

Livewire.start();

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import CKEditor from "@ckeditor/ckeditor5-vue";
import "../css/app.css";

import VueSweetalert2 from "vue-sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

const html = window.document.documentElement;
const darkMode = parseInt(localStorage.getItem("darkMode") || 1);
if (darkMode) {
    html.classList.add("dark");
} else {
    html.classList.remove("dark");
}

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    // setup({ el, App, props, plugin }) {
    //     return createApp({ render: () => h(App, props) })
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(CKEditor)
            .use(ZiggyVue)
            .use(VueSweetalert2)
            .use(Toast)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
