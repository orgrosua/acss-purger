<template>
    <h1 class="wp-heading-inline">{{ __('ACSS Purger', 'acss-purger') }}</h1>
    <div class="acss-purger-top-menu tw-p-2">
        <a href="https://rosua.org" target="_blank">Rosua.org</a> |
        <a href="https://rosua.org/blog/" target="_blank">{{ __('News', 'acss-purger') }}</a> |
        <a href="https://l.suabahasa.dev/EyJym" target="_blank">{{ __('Support', 'acss-purger') }}</a> |
        <a href="https://l.suabahasa.dev/s7XEu" target="_blank">Lead developer's homepage</a> |
        <a aria-label="more plug-ins" href="https://rosua.org/" target="_blank">More plugins</a> -
        <span tabindex="0">Version: {{ acssPurger._version }}</span>
    </div>
    <hr class="wp-header-end">
    <WordpressNotice />
    <h2 class="nav-tab-wrapper">
        <router-link :to="{ name: 'settings' }" class="nav-tab" activeClass="nav-tab-active">Settings</router-link>
        <a class="nav-tab" href="https://acss-purger.yabe.land/docs" target="_blank">
            Docs
            <svg class="tw-w-[15px] tw-h-[15px]" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 100 100">
                <path fill="currentColor" d="M18.8,85.1h56l0,0c2.2,0,4-1.8,4-4v-32h-8v28h-48v-48h28v-8h-32l0,0c-2.2,0-4,1.8-4,4v56C14.8,83.3,16.6,85.1,18.8,85.1z"></path>
                <polygon fill="currentColor" points="45.7,48.7 51.3,54.3 77.2,28.5 77.2,37.2 85.2,37.2 85.2,14.9 62.8,14.9 62.8,22.9 71.5,22.9"></polygon>
            </svg>
        </a>
    </h2>
    <div class="acss-purger-content">
        <router-view></router-view>
    </div>
</template>

<script setup>
import WordpressNotice from './components/WordpressNotice.vue';
</script>

<style>
#wpcontent {
    padding: 20px;
}
</style>