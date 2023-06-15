<template>
    <h1 class="wp-heading-inline">{{ __('ACSS Purger', 'acss-purger') }}</h1>
    <div class="acss-purger-top-menu tw-p-2">
        <a href="https://rosua.org" target="_blank">Rosua.org</a> |
        <a href="https://rosua.org/blog/" target="_blank">{{ __('News', 'acss-purger') }}</a> |
        <a href="https://l.suabahasa.dev/EyJym" target="_blank">{{ __('Support', 'acss-purger') }}</a> |
        <a aria-label="more plug-ins" href="https://rosua.org/" target="_blank">More plugins</a> -
        <span tabindex="0">Version: {{ acssPurger._version }}</span>
    </div>
    <hr class="wp-header-end">
    <WordpressNotice />
    <h2 class="nav-tab-wrapper tw-flex tw-items-center">
        <router-link :to="{ name: 'settings' }" class="nav-tab" activeClass="nav-tab-active">Settings</router-link>
        <!-- <router-link :to="{ name: 'ko-fi' }" class="nav-tab" activeClass="nav-tab-active">Ko-fi</router-link> -->
        <a class="nav-tab" href="https://acss-purger.yabe.land" target="_blank">
            Docs
            <svg class="tw-w-[15px] tw-h-[15px]" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 100 100">
                <path fill="currentColor" d="M18.8,85.1h56l0,0c2.2,0,4-1.8,4-4v-32h-8v28h-48v-48h28v-8h-32l0,0c-2.2,0-4,1.8-4,4v56C14.8,83.3,16.6,85.1,18.8,85.1z"></path>
                <polygon fill="currentColor" points="45.7,48.7 51.3,54.3 77.2,28.5 77.2,37.2 85.2,37.2 85.2,14.9 62.8,14.9 62.8,22.9 71.5,22.9"></polygon>
            </svg>
        </a>
        <a class="tw-flex-1"></a>

        <div class="flex">
            <canvas ref="pupRiveCanvas" class="pup-rive-canvas tw-absolute"></canvas>
            <a @mouseenter="$event => koFiHovered = true" @mouseleave="$event => koFiHovered = false" href="https://ko-fi.com/Q5Q75XSF7" target="_blank" class="tw-ml-2">
                <img class="tw-h-7" src="https://storage.ko-fi.com/cdn/brandasset/kofi_button_red.png" alt="">
            </a>
        </div>
    </h2>
    <div class="acss-purger-content">
        <router-view></router-view>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import WordpressNotice from './components/WordpressNotice.vue';
import { Rive } from '@rive-app/canvas';

import { assetPath } from './library/assetsHelper.js';

import pupRiveFile from './rive/pup.riv';

const pupRiveCanvas = ref(null);

/** @type {Rive} */
let pupRive = null;
const koFiHovered = ref(false);

onMounted(() => {
    pupRive = new Rive({
        src: assetPath(pupRiveFile),
        canvas: pupRiveCanvas.value,
        autoplay: true,
        stateMachines: 'YeyMachine',
        onLoad: () => {
            pupRive.resizeDrawingSurfaceToCanvas();
        },
    });
});

watch(koFiHovered, (value) => {
    pupRive.stateMachineInputs('YeyMachine')[0].value = value;
});

onBeforeUnmount(() => {
    pupRive.cleanup();
});
</script>

<style>
#wpcontent {
    padding: 20px;
}

.pup-rive-canvas {
    height: 2.5rem;
    top: 4.19rem;
    right: 0.25rem;
    z-index: 3;
}
</style>