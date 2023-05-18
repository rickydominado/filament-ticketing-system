import "./bootstrap";
import Alpine from "alpinejs";
import FormsAlpinePlugin from "../../vendor/filament/forms/dist/module.esm";
import AlpineFloatingUI from "@awcodes/alpine-floating-ui";
import NotificationsAlpinePlugin from "../../vendor/filament/notifications/dist/module.esm";
import focus from "@alpinejs/focus";

Alpine.plugin(FormsAlpinePlugin);
Alpine.plugin(AlpineFloatingUI);
Alpine.plugin(NotificationsAlpinePlugin);
Alpine.plugin(focus);

window.Alpine = Alpine;

Alpine.start();
