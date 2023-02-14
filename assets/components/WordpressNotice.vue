<template>
    <TransitionGroup name="notice">
        <div v-for="notice in notices" :key="notice.id" :class="`notice notice-${notice.type} is-dismissible`">
            <div v-html="notice.message"></div>
            <button type="button" @click="store.remove(notice.id)" class="notice-dismiss">
                <span class="screen-reader-text">{{ __('Dismiss this notice.', 'acss-purger') }}</span>
            </button>
        </div>
    </TransitionGroup>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useWordpressNotice } from '../stores/wordpressNotice';

const store = useWordpressNotice();
const { notices } = storeToRefs(store);

</script>

<style scoped>
.notice-enter-active,
.notice-leave-active {
    transition: transform 0.4s, opacity 0.6s;
}

.notice-enter-from,
.notice-leave-to {
    opacity: 0;
    transform: scale(0.88);
}
</style>

<style lang="scss">
.awn-toast-icon {
    img {
        width: theme('width.11');
        height: theme('height.11');

        &.icon-tip,
        &.icon-async {
            filter: invert(52%) sepia(1%) saturate(0%) hue-rotate(13deg) brightness(96%) contrast(88%);
        }

        &.icon-info {
            filter: invert(39%) sepia(49%) saturate(960%) hue-rotate(160deg) brightness(88%) contrast(87%);
        }

        &.icon-success {
            filter: invert(35%) sepia(86%) saturate(448%) hue-rotate(56deg) brightness(104%) contrast(87%);
        }

        &.icon-warning {
            filter: invert(33%) sepia(73%) saturate(3587%) hue-rotate(30deg) brightness(105%) contrast(102%);
        }

        &.icon-alert {
            filter: invert(16%) sepia(62%) saturate(3834%) hue-rotate(355deg) brightness(93%) contrast(87%);
        }

        &.icon-async {
            animation: spin 2s linear infinite;

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        }
    }
}
</style>