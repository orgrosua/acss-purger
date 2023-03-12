<template>
    <div class="wrapper-free tw-mt-5">
        <div class="tw-pr-5">

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
                                <div class="tw-grid tw-grid-cols-8 tw-gap-8 tw-mt-2 tw-mb-4">
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
                                    <highlightjs language="php" :code="filterHookExample" style="white-space:pre-wrap;" />
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
        </div>
        <div>
            <div class="tw-flex tw-flex-col yabe-webfont-banner">
                <div class="inner-wrap">
                    <a class="webfont-banner" target="_blank" rel="noopener noreferrer" href="https://webfont.yabe.land/?utm_medium=acss-purger-plugin" style="background-image: url(&quot;data:image/svg+xml;charset=utf-8,%3Csvg%20id%3D%22Layer_1%22%20data-name%3D%22Layer%201%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20459%20459%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3Cstyle%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-1%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20fill%3A%20%23fef08a%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-2%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20fill%3A%20%23fef08a%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-3%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20fill%3A%20%23713f12%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%3C%2Fstyle%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Ctitle%3Eyabe-webfont-banner%3C%2Ftitle%3E%0A%20%20%20%20%3Crect%20class%3D%22cls-1%22%20width%3D%22459%22%20height%3D%22459%22%20%2F%3E%0A%20%20%20%20%3Cpath%20class%3D%22cls-2%22%20d%3D%22M144.9%2C379.68a6.21%2C6.21%2C0%2C0%2C1-.57-.49%2C19.06%2C19.06%2C0%2C0%2C0-2.8-2%2C11.73%2C11.73%2C0%2C0%2C1-1.79-1%2C60.76%2C60.76%2C0%2C0%2C0-58.9-39.53%2C86.13%2C86.13%2C0%2C0%2C0-11.22%2C1.53%2C54.56%2C54.56%2C0%2C0%2C0-14%2C5.1l-3.82%2C2.3a30.65%2C30.65%2C0%2C0%2C0-2-5.1%2C52.49%2C52.49%2C0%2C0%2C0-18.36-21.68%2C77.52%2C77.52%2C0%2C0%2C0%2C38-41.05c0-.26.26-.77.26-1s.25-.51.25-.77a66.1%2C66.1%2C0%2C0%2C0%2C3.06-10.2%2C74.47%2C74.47%2C0%2C0%2C0%2C1.28-7.9%2C74.61%2C74.61%2C0%2C0%2C0-1.53-24.74%2C102.43%2C102.43%2C0%2C0%2C0-4.08-12.49%2C77.64%2C77.64%2C0%2C0%2C0-16.07-24A59.74%2C59.74%2C0%2C0%2C0%2C46.67%2C191a68.93%2C68.93%2C0%2C0%2C0-10-6.89%2C79%2C79%2C0%2C0%2C0-25.24-9.18c-3.32-.51-6.89-.76-10.2-1%2C2.8-1%2C5.61-1.78%2C8.41-2.55A63.89%2C63.89%2C0%2C0%2C1%2C63.24%2C183.6a22%2C22%2C0%2C0%2C1%2C7.14%2C6.63c1.53%2C1.79%2C3.06%2C3.57%2C4.34%2C5.36a46.11%2C46.11%2C0%2C0%2C1%2C26%2C82.62%2C71.67%2C71.67%2C0%2C0%2C1%2C24.22%2C11%2C84%2C84%2C0%2C0%2C1%2C36.47-19.38%2C79.36%2C79.36%2C0%2C0%2C1%2C49.47%2C4.34%2C44.8%2C44.8%2C0%2C0%2C1%2C23.2-6.63c23.46.25%2C42.58%2C19.38%2C42.33%2C42.84v2.29a24.75%2C24.75%2C0%2C0%2C1%2C22.7%2C24.74%2C19.91%2C19.91%2C0%2C0%2C1-.77%2C5.61%2C76.42%2C76.42%2C0%2C0%2C1%2C83.38%2C58.9c.26%2C2%2C.77%2C4.08%2C1%2C6.12a69.75%2C69.75%2C0%2C0%2C1%2C39%2C47.94c.23%2C1%2C.44%2C2%2C.62%2C3.06H416.1a47.52%2C47.52%2C0%2C0%2C0-4.53-7.91%2C53%2C53%2C0%2C0%2C0-45.9-22.69%2C35.11%2C35.11%2C0%2C0%2C0-5.1.51A22.1%2C22.1%2C0%2C0%2C0%2C341.19%2C419a24.7%2C24.7%2C0%2C0%2C0-4.34.51%2C2.92%2C2.92%2C0%2C0%2C1-.65.2l-.47.1a3%2C3%2C0%2C0%2C0-.66.2%2C9.35%2C9.35%2C0%2C0%2C0-2.81%2C1c-.51-1.53-1-2.8-1.53-4.08-1-2-2-4.08-3.06-5.86l-2.29-3.83c-7.14-10.2-18.36-18.36-31.62-23.71a92.31%2C92.31%2C0%2C0%2C0-33.15-6.12h-4.34a92.83%2C92.83%2C0%2C0%2C0-15%2C1.78%2C90.42%2C90.42%2C0%2C0%2C0-22.44%2C8.16%2C63.09%2C63.09%2C0%2C0%2C0-13.52%2C9.18c-3.57-.51-7.14-1-11-1.27h-3.83a75.63%2C75.63%2C0%2C0%2C0-10.71.76l-7.65%2C1.53a21.62%2C21.62%2C0%2C0%2C0-3.82%2C1%2C60%2C60%2C0%2C0%2C0-11.73%2C4.85c0-.51-.26-1.28-.26-1.79a17%2C17%2C0%2C0%2C0-.51-3.57%2C30.21%2C30.21%2C0%2C0%2C0-1.27-4.59%2C19.6%2C19.6%2C0%2C0%2C0-1.79-4.08%2C33.5%2C33.5%2C0%2C0%2C0-6.88-8.92%2C5%2C5%2C0%2C0%2C0-.57-.49l-.39-.29ZM206%22%20%2F%3E%0A%20%20%20%20%3Cpath%20class%3D%22cls-3%22%20d%3D%22M0%2C173.91H1.27a65.76%2C65.76%2C0%2C0%2C1%2C10.2%2C1%2C73.24%2C73.24%2C0%2C0%2C1%2C25.25%2C9.18A68.29%2C68.29%2C0%2C0%2C1%2C46.66%2C191a61%2C61%2C0%2C0%2C1%2C5.87%2C5.61%2C73.31%2C73.31%2C0%2C0%2C1%2C16.06%2C24%2C71.24%2C71.24%2C0%2C0%2C1%2C4.08%2C12.49%2C73.55%2C73.55%2C0%2C0%2C1%2C1.53%2C24.74c-.25%2C2.55-.76%2C5.35-1.27%2C7.9a78.54%2C78.54%2C0%2C0%2C1-3.06%2C10.2c0%2C.26-.26.51-.26.77s-.25.76-.25%2C1c-7.14%2C17.59-20.4%2C32.64-38%2C41.05a52.42%2C52.42%2C0%2C0%2C1%2C18.36%2C21.68%2C29.81%2C29.81%2C0%2C0%2C1%2C2%2C5.1l3.83-2.3a61.4%2C61.4%2C0%2C0%2C1%2C14-5.1%2C57%2C57%2C0%2C0%2C1%2C11.22-1.53%2C60.79%2C60.79%2C0%2C0%2C1%2C58.91%2C39.53%2C10.51%2C10.51%2C0%2C0%2C1%2C1.78%2C1%2C11.32%2C11.32%2C0%2C0%2C1%2C2.81%2C2%2C6.6%2C6.6%2C0%2C0%2C1%2C1.53%2C1.27%2C33.39%2C33.39%2C0%2C0%2C1%2C6.88%2C8.93%2C27.37%2C27.37%2C0%2C0%2C1%2C1.79%2C4.08%2C30.21%2C30.21%2C0%2C0%2C1%2C1.27%2C4.59c.26%2C1.27.26%2C2.29.51%2C3.57a4.24%2C4.24%2C0%2C0%2C1%2C.26%2C1.78%2C59.38%2C59.38%2C0%2C0%2C1%2C11.73-4.84c1.27-.26%2C2.55-.77%2C3.82-1a39.88%2C39.88%2C0%2C0%2C1%2C7.65-1.53%2C70.61%2C70.61%2C0%2C0%2C1%2C10.71-.77h3.83a51.2%2C51.2%2C0%2C0%2C1%2C11%2C1.28%2C83.42%2C83.42%2C0%2C0%2C1%2C13.52-9.18%2C86.54%2C86.54%2C0%2C0%2C1%2C22.44-8.16%2C97.5%2C97.5%2C0%2C0%2C1%2C15-1.79h4.34a92.48%2C92.48%2C0%2C0%2C1%2C33.15%2C6.12c13.51%2C5.36%2C24.48%2C13.52%2C31.62%2C23.72l2.29%2C3.82a31.74%2C31.74%2C0%2C0%2C1%2C3.06%2C5.87c.51%2C1.27%2C1%2C2.8%2C1.54%2C4.08a12%2C12%2C0%2C0%2C1%2C2.8-1%2C2.55%2C2.55%2C0%2C0%2C1%2C.66-.21l.47-.1a3.18%2C3.18%2C0%2C0%2C0%2C.66-.2%2C27.79%2C27.79%2C0%2C0%2C1%2C4.33-.51%2C22.72%2C22.72%2C0%2C0%2C1%2C19.38%2C9.94%2C35.39%2C35.39%2C0%2C0%2C1%2C5.1-.51%2C52.47%2C52.47%2C0%2C0%2C1%2C45.9%2C22.7%2C49.63%2C49.63%2C0%2C0%2C1%2C4.62%2C7.9H0V173.91Z%22%20%2F%3E%0A%3C%2Fsvg%3E&quot;);"></a>
                    <div class="webfont-upgrade-details">
                        <h1 class="tw-font-bold">Yabe Webfont</h1>
                        <h3>Custom fonts management and self-host Google Fonts with seamless WordPress page builders integration</h3>
                        <ul class="pro-upgrades">
                            <li>One-click Google Fonts self-host</li>
                            <li>Bulk upload files</li>
                            <li>No FTP setup required</li>
                            <li>Without having to code</li>
                            <li>Visual builders integration</li>
                            <li>Variable Fonts</li>
                            <li>Superfast UI</li>
                            <li>Performance in mind</li>
                        </ul>
                    </div>
                    <div class="webfont-discount block tw-pb-0">
                        <h2>Get up to 50% off lifetime-update deal with coupon code: <code>ACSS-PURGER-WEBFONT</code>!</h2>
                        <h3><a href="https://webfont.yabe.land/?utm_medium=acss-purger-plugin">Get the discount</a></h3>
                        <p class="discount-applied">* Discount available for limited time.</p>
                    </div>
                    <div class="block testimonial">
                        <div class="header"><a class="author" target="_blank" rel="noopener noreferrer" href="https://webfont.yabe.land/#testimonials">{{ testimonial.name }}</a>
                            <p class="stars"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></p>
                        </div>
                        <p class="quote">"{{ testimonial.text }}"</p>
                    </div>
                    <a href="https://rosua.org" target="_blank" rel="noopener noreferrer">
                        <div class="dbi">Developed and maintained by <span class="name">Rosua.org</span></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onBeforeMount, onMounted, computed } from 'vue';
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

const testimonials = [
    {
        'name': 'Luke Allen',
        'text': 'I have always hosted Google Fonts locally and to do so was always a manual task. It would be a case of adding a snippet, downloading the fonts I want to use, then upload to a folder on the server. This plugin changes that and makes all of it seamless. It is the hidden gem that I have been looking for!',
    },
    {
        'name': 'Renato Corluka',
        'text': 'Hosting fonts locally is a must-have for sites that operate within the EU. And this is the most painless way to host them locally. You can configure everything you need from the plugin interface withe ease.',
    },
    {
        'name': 'Mohammad Arshad',
        'text': 'I\'m working as a freelancer WordPress Expert, has been working with an German Web Agency. Because of GPDR rules I\'m local hosting google fonts and custm fonts from years manually, but sometimes it is a mess to do so when you have multiple fonts. This Plugin solves the major problem of my work, with much less steps and it is done automatically. I love this easy process.',
    },
    {
        'name': 'Luciana Hanan',
        'text': 'I make WordPress sites since the beginning, and adding custom fonts or even google fonts to the site was always a struggle. But no more! Yabe Fonts is the dream fonts manager that I always wanted, a beautiful interface is simple and accurate! It\'s easy to have local google fonts and install my custom fonts, and Yabe Fonts deals with FOUT flawlessly! I strongly recommend it to everyone!',
    },
    {
        'name': 'Jornes',
        'text': 'I\'m a cabinet designer and also create websites for my businesses. Yabe Font is a great tool. It\'s a life saver. Just a few clicks away, I can have Google font host locally without hassle. No more downloading from GoogleFont and self-uploading it by myself, and no more worrying about GDPR compliant, and host the font locally improves the page speed. The support is awesome! I truly appreciate it. I will use it for every site I build. Highly recommended.',
    },
    {
        'name': 'Akram ahmed',
        'text': 'I\'m a wordpress web dev and also create websites for my small business . Yabe Font is an awesome tool and life saver. Just right on your wordPress dashboard one clicks away, and easily like that u can have Google font host locally without wasting your time . No more downloading from GoogleFont and self-uploading it by yourself. The support is always there to help! I truly appreciate it. I will use it for every site I build. Highly recommended.',
    },
];

let rotateTestimonialsIndex = ref(0);

setInterval(() => {
    rotateTestimonialsIndex.value = Math.floor(Math.random() * testimonials.length);
}, 7000);

const testimonial = computed(() => {
    return testimonials[rotateTestimonialsIndex.value];
});


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

.wrapper-free {
    display: grid;
    grid-template-columns: 8fr 315px;
}

.yabe-webfont-banner .inner-wrap {
    background: #fff;
    border-radius: 7px;
    border: 1px solid #d9e1eb;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 10%);
}

.yabe-webfont-banner .webfont-banner {
    display: block;
    background-color: #fef08a;
    background-position: 0 100%;
    background-repeat: no-repeat;
    padding-bottom: 180px;
    box-shadow: none;
    border-radius: 7px 7px 0 0;
    color: #1d2327;
}

.yabe-webfont-banner .webfont-upgrade-details {
    background-color: #1d2327;
    padding: 10px 20px 20px;
    color: #eee;
    font-size: 13px;
    margin: 0;
    display: block;
    text-decoration: none;
}

.yabe-webfont-banner .webfont-upgrade-details h1 {
    font-size: 28px;
    color: #fef08a;
    margin: 0 0 15px;
    padding: 0;
    text-decoration: none;
    font-weight: 200;
    line-height: 1;
}

.yabe-webfont-banner .webfont-upgrade-details h3 {
    color: #fef08a;
    font-weight: 500;
    text-decoration: none;
    font-size: 15px;
    letter-spacing: -.2px;
    box-shadow: none;
    line-height: 1.5;
}

.yabe-webfont-banner .webfont-upgrade-details ul li {
    list-style: none;
    margin: 8px 0;
    padding-left: 28px;
    line-height: 19px;
    background: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16'%3E%3Cpath fill='%23fef08a' d='M15.878 6.61A8 8 0 11.122 9.39a8 8 0 0115.756-2.78zm-8.71 5.618l5.936-5.936a.516.516 0 000-.73l-.73-.73a.516.516 0 00-.73 0l-4.84 4.841-2.26-2.26a.516.516 0 00-.73 0l-.73.73a.516.516 0 000 .73l3.354 3.355a.516.516 0 00.73 0z'/%3E%3C/svg%3E") 0 2px no-repeat;
}

.yabe-webfont-banner .webfont-discount {
    border-top: none;
    padding-bottom: 5px;
}

.yabe-webfont-banner .webfont-discount h2 {
    padding: 0;
    margin: 0 0 .5em;
    color: #666;
    font-size: 17px;
    line-height: 1.4em;
    float: none;
    font-weight: 500;
}

.yabe-webfont-banner .webfont-discount .discount-applied {
    color: #999;
    font-size: 12px;
    line-height: 1.4em;
    text-align: center;
    margin: 1.2rem auto !important;
    width: 100%;
}

.yabe-webfont-banner .webfont-discount h3 a {
    color: #fff;
    display: flex;
    background: #236de7;
    box-shadow: 0 2px 0 0 #0d51c1;
    border-radius: 4px;
    margin: 0 auto;
    padding: .9rem 0 .8rem;
    text-align: center;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 1px;
    justify-content: center;
    box-shadow: 0 2px 2px 0 #0d53bf;
}

.yabe-webfont-banner .webfont-discount h3 a:hover {
    background: #145dd6
}

.yabe-webfont-banner .block {
    padding: 20px;
}

.yabe-webfont-banner .testimonial {
    border: 1px solid #d9e1eb;
    border-radius: 6px;
    padding: 0 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 1rem;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 10%);
}

.yabe-webfont-banner .testimonial .header {
    display: flex;
    justify-content: space-between;
    width: 100%;
    padding: 1rem 0 .4rem;
    align-items: center;
    padding-bottom: 0;
}

.yabe-webfont-banner .testimonial .author {
    white-space: nowrap;
    text-align: right;
    font-size: 16px;
    font-weight: 600;
    color: #666;
    margin-top: -7px;
}

.yabe-webfont-banner .testimonial .quote {
    font-size: 15px;
    color: #666;
    letter-spacing: -.25px;
    line-height: 20px;
    margin-bottom: 1rem;
    margin-top: 0;
}

.yabe-webfont-banner .dashicons.dashicons-star-filled {
    color: #236de7;
}

.yabe-webfont-banner a {
    text-decoration: none;
}

.yabe-webfont-banner .dbi {
    background: #2e2e31;
    margin-top: 1rem;
    padding: 1rem;
    color: #fff;
    font-size: 12px;
    font-weight: 300;
    text-align: center;
    border-radius: 0 0 7px 7px;
    text-decoration: none;
}
</style>