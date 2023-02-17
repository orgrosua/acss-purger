<template>
    <div class="tw-mb-6">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label>License Key</label></th>
                    <td>
                        <input name="license_key" type="password" v-model.lazy="license.key" class="tw-min-w-[25rem]">
                        <div v-if="license.is_activated" class="tw-flex tw-my-2.5 tw-items-center tw-font-medium">
                            Status: <span class="tw-font-normal tw-text-white tw-bg-green-700 tw-px-1.5 tw-py-1 tw-rounded tw-ml-2.5">active</span>
                        </div>
                        <p v-else class="description">Enter your <a href="https://acss-purger.yabe.land/#pricing-plans" target="_blank">license key</a> receive the update of the latest version.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="tw-mb-6">
        <h2 class="title">Performance</h2>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"> Purged CSS </th>
                    <td>
                        <div class="tw-flex tw-items-center tw-mb-2">
                            <button type="button" @click="doGenerateCache" :disabled="busy.isBusy" class="button button-secondary"> Purge </button>
                            <span :class="{ 'tw-hidden': !busy.isBusy || !busy.hasTask('settings:generate-cache') }" class="spinner tw-visible"></span>

                            <template v-if="css_cache.pending_task">
                                <svg class="tw-ml-3 tw-h-5 tw-w-5 tw-text-gray-400 tw-fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
                                    <path d="M32 0C14.3 0 0 14.3 0 32S14.3 64 32 64V75c0 42.4 16.9 83.1 46.9 113.1L146.7 256 78.9 323.9C48.9 353.9 32 394.6 32 437v11c-17.7 0-32 14.3-32 32s14.3 32 32 32H64 320h10.8C285.6 480.1 256 427.5 256 368c0-27.2 6.2-53 17.2-76l-36-36 67.9-67.9c30-30 46.9-70.7 46.9-113.1V64c17.7 0 32-14.3 32-32s-14.3-32-32-32H320 64 32zM96 75V64H288V75c0 19-5.6 37.4-16 53H112c-10.3-15.6-16-34-16-53zM576 368c0-79.5-64.5-144-144-144s-144 64.5-144 144s64.5 144 144 144s144-64.5 144-144zM432 288c8.8 0 16 7.2 16 16v48h32c8.8 0 16 7.2 16 16s-7.2 16-16 16H432c-8.8 0-16-7.2-16-16V304c0-8.8 7.2-16 16-16z" />
                                </svg>
                                <span class="tw-text-gray-400 tw-pl-1" :title="new dayjs(css_cache.pending_task * 1000).format('YYYY-MM-DD HH:mm:ss')">
                                    There is a scheduled task to purge the CSS.
                                </span>
                            </template>

                        </div>
                        <p class="description tw-my-2">
                            Remove unused classes on CSS file by scanning your design.
                        </p>
                        <p>
                            <span class="tw-font-medium"> Files: </span>
                        </p>
                        <div class="tw-grid tw-grid-cols-12 tw-gap-8 tw-mt-2 tw-mb-4">
                            <div v-for="f in css_files" class="tw-col-span-4 tw-flex tw-relative tw-border tw-border-solid tw-border-gray-200 tw-rounded-md tw-shadow-sm">
                                <div class="tw-flex-shrink-0 tw-flex tw-items-center tw-justify-center tw-w-20 tw-bg-gray-50 tw-text-green-500 tw-text-sm tw-font-medium tw-rounded-l-md">
                                    <svg class="tw-h-5 tw-w-5 tw-flex-shrink-0 tw-self-center tw-text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                    </svg>
                                    {{ f.reduction.toFixed(1) }}%
                                </div>
                                <div class="tw-flex tw-flex-1 tw-items-center tw-justify-between tw-truncate tw-rounded-r-md tw-border-t tw-border-r tw-border-b tw-border-gray-200 tw-bg-white">
                                    <div class="tw-flex-1 tw-truncate tw-px-4 tw-py-2 tw-text-sm">
                                        <span :title="new dayjs((f.purged ? f.purged.last_modified : f.original.last_modified) * 1000).format('YYYY-MM-DD HH:mm:ss')" class="tw-font-medium tw-text-gray-900 hover:tw-text-gray-600">
                                            {{ f.name }}
                                        </span>
                                        <p class="tw-text-gray-500 tw-font-medium">
                                            <span class="tw-font-normal">Original:</span> {{ prettyBytes(f.original.file_size) }}
                                            <a :href="`${f.original.file_url}?ver=${f.original.last_modified}`" target="_blank">
                                                <svg class="tw-w-[15px] tw-h-[15px]" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 100 100">
                                                    <path fill="currentColor" d="M18.8,85.1h56l0,0c2.2,0,4-1.8,4-4v-32h-8v28h-48v-48h28v-8h-32l0,0c-2.2,0-4,1.8-4,4v56C14.8,83.3,16.6,85.1,18.8,85.1z"></path>
                                                    <polygon fill="currentColor" points="45.7,48.7 51.3,54.3 77.2,28.5 77.2,37.2 85.2,37.2 85.2,14.9 62.8,14.9 62.8,22.9 71.5,22.9"></polygon>
                                                </svg>
                                            </a>
                                        </p>
                                        <p v-if="f.purged" class="tw-text-gray-500 tw-font-medium">
                                            <span class="tw-font-normal">Purged:</span> {{ prettyBytes(f.purged.file_size) }}
                                            <a :href="`${f.purged.file_url}?ver=${f.purged.last_modified}`" target="_blank">
                                                <svg class="tw-w-[15px] tw-h-[15px]" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 100 100">
                                                    <path fill="currentColor" d="M18.8,85.1h56l0,0c2.2,0,4-1.8,4-4v-32h-8v28h-48v-48h28v-8h-32l0,0c-2.2,0-4,1.8-4,4v56C14.8,83.3,16.6,85.1,18.8,85.1z"></path>
                                                    <polygon fill="currentColor" points="45.7,48.7 51.3,54.3 77.2,28.5 77.2,37.2 85.2,37.2 85.2,14.9 62.8,14.9 62.8,22.9 71.5,22.9"></polygon>
                                                </svg>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="tw-absolute -tw-bottom-5 tw-left-2 tw-text-gray-500 tw-font-medium">
                                    Last purged:
                                    <span v-if="f.purged" class="tw-font-normal">
                                        {{ new dayjs(f.purged.last_modified * 1000).format('YYYY-MM-DD HH:mm:ss') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Remove fallback <span class="tw-text-yellow-600">(experimental)</span></label></th>
                    <td>
                        <input id="remove-fallback" name="remove_fallback" type="checkbox" :checked="get(options, 'cache.remove_fallback', false)" :value="get(options, 'cache.remove_fallback', false)" @input="set(options, 'cache.remove_fallback', !options?.cache?.remove_fallback)">
                        <label for="remove-fallback"> Only use <code>clamp</code>, or <code>calc</code> function </label>
                        <p class="description">⚠️ <span class="tw-font-medium">Warning:</span> remove fallback like <code>calc</code> function or primitive value</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Safelist</label></th>
                    <td>
                        <VueTagsInput v-model="tag" :tags="safelist" :autocomplete-items="filteredTags" @tags-changed="newTags => safelist = newTags" :placeholder="'Add a classes'" />
                        <p class="description"> Add classes name that should be excluded.</p>
                        <p class="description tw-font-medium">
                            Filter hooks: <span class="tw-font-normal">(programmatically for developers)</span>
                        </p>
                        <div>
                            <highlightjs language="php" :code="filterHookExample" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="tw-flex tw-items-center">
        <button type="button" @click="doStore" :disabled="busy.isBusy" v-ripple class="button button-primary">Save Changes</button>
        <span :class="{ 'tw-hidden': !busy.isBusy || !busy.hasTask('settings:store-options') }" class="spinner tw-visible"></span>
</div>
</template>

<script setup>
import { ref, watch, onBeforeMount, computed } from 'vue';
import dayjs from 'dayjs';
import cloneDeep from 'lodash-es/cloneDeep';
import prettyBytes from 'pretty-bytes';
import set from 'lodash-es/set';
import get from 'lodash-es/get';

import { useApi } from '../../library/api';
import { useNotifier } from '../../library/notifier';
import { useBusy } from '../../stores/busy';

import VueTagsInput from '@sipec/vue3-tags-input';

const api = useApi();
const busy = useBusy();
const notifier = useNotifier();

const license = ref({
    key: null,
    is_activated: false,
    opt_in_pre_release: false,
});

const css_cache = ref({
    pending_task: false,
});

const css_files = ref([]);

const options = ref({});

const availableTags = ref([]);
const tag = ref('');
const safelist = ref([]);

const filteredTags = computed(() => {
    let filtered = availableTags.value.filter((item) => {
        return item.text.toLowerCase().indexOf(tag.value.toLowerCase()) !== -1;
    });
    return filtered;
});

onBeforeMount(() => {
    busy.add('settings:fetch-license');
    api
        .request({
            method: 'GET',
            url: '/setting/license/index'
        })
        .then(response => response.data)
        .then(data => {
            license.value = data.license;
        })
        .catch(function (error) {
            notifier.alert(error.message);
        })
        .finally(() => {
            busy.remove('settings:fetch-license');
        });

    busy.add('settings:fetch-cache');
    api
        .request({
            method: 'GET',
            url: '/setting/cache/index'
        })
        .then(response => response.data)
        .then(data => {
            css_cache.value = data.cache;
            data.files.original.forEach(element => {
                let f = {
                    name: element.name,
                    original: {
                        last_modified: element.last_modified,
                        file_url: element.file_url,
                        file_size: element.size,
                    },
                    purged: null,
                    reduction: 0.0,
                };

                let purged = data.files.purged.find(purged => purged.name === element.name);

                if (purged) {
                    f.purged = {
                        last_modified: purged.last_modified,
                        file_url: purged.file_url,
                        file_size: purged.size,
                    };

                    f.reduction = 100 - (purged.size / element.size * 100);
                }
                css_files.value.push(f);
            });
        })
        .catch(function (error) {
            notifier.alert(error.message);
        })
        .finally(() => {
            busy.remove('settings:fetch-cache');
        });

    busy.add('settings:fetch-options');
    api
        .request({
            method: 'GET',
            url: '/setting/option/index'
        })
        .then(response => response.data)
        .then(data => {
            options.value = cloneDeep(data.options);
            availableTags.value = data.acss_classes.map((item) => {
                return {
                    text: item,
                };
            });

            // set(options.value, 'cache.safelist', safe);

            safelist.value = get(options.value, 'cache.safelist', []).map((item) => {
                return {
                    text: item,
                    tiClasses: 'ti-valid'
                };
            });
        })
        .catch(function (error) {
            notifier.alert(error.message);
        })
        .finally(() => {
            busy.remove('settings:fetch-options');
        });
});

watch(
    () => license.value.key,
    async (newKey, oldKey) => {
        if (newKey === oldKey) {
            return;
        }

        busy.add('settings:store-license');
        api
            .request({
                method: 'POST',
                url: '/setting/license/store',
                data: {
                    license: newKey
                },
            })
            .then(response => response.data)
            .then(data => {
                license.value = cloneDeep(data.license);
                if (Object.keys(data.notice).length > 0) {
                    if (data.notice?.success) {
                        notifier.success(data.notice?.success);
                    } else if (data.notice?.error) {
                        notifier.alert(data.notice?.error);
                    }
                } else {
                    notifier.success('License setting saved.');
                }
            })
            .catch(function (error) {
                notifier.alert(error.message);
            })
            .finally(() => {
                busy.remove('settings:store-license');
            });
    }
);

watch(safelist, (newVal, oldVal) => {
    if (newVal === oldVal) {
        return;
    }

    let safe = newVal.map((item) => item.text);

    set(options.value, 'cache.safelist', safe);
});

function doStore() {
    busy.add('settings:store-options');
    api
        .request({
            method: 'POST',
            url: '/setting/option/store',
            data: {
                options: options.value,
            },
        })
        .then(response => response.data)
        .then(data => {
            options.value = cloneDeep(data.options);
            notifier.success('Settings saved.');
        })
        .catch(function (error) {
            notifier.alert(error.message);
        })
        .finally(() => {
            busy.remove('settings:store-options');
        });
}

function doGenerateCache() {
    busy.add('settings:generate-cache');
    api
        .request({
            method: 'POST',
            url: '/setting/cache/generate',
        })
        .then(response => response.data)
        .then(data => {
            css_cache.value = data.cache;
            let files = [];
            data.files.original.forEach(element => {
                let f = {
                    name: element.name,
                    original: {
                        last_modified: element.last_modified,
                        file_url: element.file_url,
                        file_size: element.size,
                    },
                    purged: null,
                    reduction: 0.0,
                };

                let purged = data.files.purged.find(purged => purged.name === element.name);

                if (purged) {
                    f.purged = {
                        last_modified: purged.last_modified,
                        file_url: purged.file_url,
                        file_size: purged.size,
                    };

                    f.reduction = 100 - (purged.size / element.size * 100);
                }
                files.push(f);
            });
            css_files.value = files;
            notifier.success('CSS file purged.');
        })
        .catch(function (error) {
            notifier.alert(error.message);
        })
        .finally(() => {
            busy.remove('settings:generate-cache');
        });
}

const filterHookExample = `<?php

add_filter('f!yabe/acsspurger/core/cache:selectors', fn (array $selectors): array => your_function_name_here($selectors), 10);

function your_function_name_here(array $selectors): array
{
    $selectors[] = 'your-class-name';
    return $selectors;
}
`;

</script>

<style lang="scss">
.vue-tags-input {
    max-width: none !important;
}

.ti-new-tag-input:focus {
    box-shadow: none !important;
}

.ti-new-tag-input {
    min-height: initial !important;
    font-size: 14px !important;
}

.ti-tag,
.ti-item.ti-selected-item {
    background-color: #007cba !important;
    color: #fff !important;
}

.ti-tags li {
    padding: 5px 8px !important;
    margin: 2px !important;
}

.ti-item {
    padding: 2px 3px !important;
}

.ti-tag {
    font-size: 14px !important;
}

.ti-tag span {
    line-height: 14px !important;
}
</style>