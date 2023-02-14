<template>
    <div class="">
        <main class="tw-mx-auto tw-max-w-7xl tw-px-6 lg:tw-px-8">
            <div class="tw-mx-auto tw-max-w-xl tw-py-16 sm:tw-py-24">
                <div class="tw-text-center">
                    <p class="tw-text-base tw-font-semibold tw-text-sky-600">404</p>
                    <h1 class="tw-mt-2 tw-text-4xl tw-font-bold tw-tracking-tight tw-text-gray-900 sm:tw-text-5xl">{{ __('This page does not exist.', 'acss-purger') }}</h1>
                    <p class="tw-mt-2 tw-text-lg tw-text-gray-500">{{ __('The page you are looking for could not be found.', 'acss-purger') }}</p>
                </div>
                <div class="tw-mt-12">
                    <h2 class="tw-text-base tw-font-semibold tw-text-gray-500">{{ __('Popular pages', 'acss-purger') }}</h2>
                    <ul role="list" class="tw-mt-4 tw-border-solid tw-border-0 tw-divide-solid tw-divide-x-0 tw-divide-y tw-divide-gray-300 tw-border-t tw-border-b tw-border-gray-300">
                        <li class="tw-relative tw-flex tw-items-start tw-space-x-4 tw-py-6">
                            <div class="tw-shrink-0">
                                <span class="tw-flex tw-h-12 tw-w-12 tw-items-center tw-justify-center tw-rounded-lg tw-bg-gray-200">
                                    <svg class="tw-h-6 tw-w-6 tw-text-sky-700 tw-fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
                                        <path d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V384c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H384 96zm0 384H352v64H96c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16zm16 48H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="tw-min-w-0 tw-flex-1">
                                <h3 class="tw-my-0 tw-text-base tw-font-medium tw-text-gray-900">
                                    <span class="tw-rounded-sm focus-within:tw-ring-2 focus-within:tw-ring-sky-500 focus-within:tw-ring-offset-2">
                                        <a href="#" class="tw-no-underline focus:tw-shadow-none focus:tw-outline-none tw-text-inherit">
                                            <span class="tw-absolute tw-inset-0" aria-hidden="true"></span>
                                            {{ __('Documentation', 'acss-purger') }}
                                        </a>
                                    </span>
                                </h3>
                                <p class="tw-my-0 tw-text-base tw-text-gray-500">{{ __('A complete knowledge-base of the product','acss-purger') }}</p>
                            </div>
                            <div class="tw-shrink-0 tw-self-center">
                                <!-- Heroicon name: mini/chevron-right -->
                                <svg class="tw-h-5 tw-w-5 tw-text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </li>

                        <li class="tw-relative tw-flex tw-items-start tw-space-x-4 tw-py-6">
                            <div class="tw-shrink-0">
                                <span class="tw-flex tw-h-12 tw-w-12 tw-items-center tw-justify-center tw-rounded-lg tw-bg-gray-200">
                                    <svg class="tw-h-6 tw-w-6 tw-text-sky-700 tw-fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
                                        <path d="M256 48C141.1 48 48 141.1 48 256v40c0 13.3-10.7 24-24 24s-24-10.7-24-24V256C0 114.6 114.6 0 256 0S512 114.6 512 256V400.1c0 48.6-39.4 88-88.1 88L313.6 488c-8.3 14.3-23.8 24-41.6 24H240c-26.5 0-48-21.5-48-48s21.5-48 48-48h32c17.8 0 33.3 9.7 41.6 24l110.4 .1c22.1 0 40-17.9 40-40V256c0-114.9-93.1-208-208-208zM144 208h16c17.7 0 32 14.3 32 32V352c0 17.7-14.3 32-32 32H144c-35.3 0-64-28.7-64-64V272c0-35.3 28.7-64 64-64zm224 0c35.3 0 64 28.7 64 64v48c0 35.3-28.7 64-64 64H352c-17.7 0-32-14.3-32-32V240c0-17.7 14.3-32 32-32h16z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="tw-min-w-0 tw-flex-1">
                                <h3 class="tw-my-0 tw-text-base tw-font-medium tw-text-gray-900">
                                    <span class="tw-rounded-sm focus-within:tw-ring-2 focus-within:tw-ring-sky-500 focus-within:tw-ring-offset-2">
                                        <a href="https://l.suabahasa.dev/EyJym" class="tw-no-underline focus:tw-shadow-none focus:tw-outline-none tw-text-inherit">
                                            <span class="tw-absolute tw-inset-0" aria-hidden="true"></span>
                                            {{ __('Support', 'acss-purger') }}
                                        </a>
                                    </span>
                                </h3>
                                <p class="tw-my-0 tw-text-base tw-text-gray-500">{{ __('How can we help you?', 'acss-purger') }} </p>
                            </div>
                            <div class="tw-shrink-0 tw-self-center">
                                <!-- Heroicon name: mini/chevron-right -->
                                <svg class="tw-h-5 tw-w-5 tw-text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </li>

                        <li class="tw-relative tw-flex tw-items-start tw-space-x-4 tw-py-6">
                            <div class="tw-shrink-0">
                                <span class="tw-flex tw-h-12 tw-w-12 tw-items-center tw-justify-center tw-rounded-lg tw-bg-gray-200">
                                    <!-- rss -->
                                    <svg class="tw-h-6 tw-w-6 tw-text-sky-700 tw-fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
                                        <path d="M0 64C0 46.3 14.3 32 32 32c229.8 0 416 186.2 416 416c0 17.7-14.3 32-32 32s-32-14.3-32-32C384 253.6 226.4 96 32 96C14.3 96 0 81.7 0 64zM128 416c0 35.3-28.7 64-64 64s-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64zM32 160c159.1 0 288 128.9 288 288c0 17.7-14.3 32-32 32s-32-14.3-32-32c0-123.7-100.3-224-224-224c-17.7 0-32-14.3-32-32s14.3-32 32-32z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="tw-min-w-0 tw-flex-1">
                                <h3 class="tw-my-0 tw-text-base tw-font-medium tw-text-gray-900">
                                    <span class="tw-rounded-sm focus-within:tw-ring-2 focus-within:tw-ring-sky-500 focus-within:tw-ring-offset-2">
                                        <a href="https://rosua.org/blog/" class="tw-no-underline focus:tw-shadow-none focus:tw-outline-none tw-text-inherit">
                                            <span class="tw-absolute tw-inset-0" aria-hidden="true"></span>
                                            {{ __('Blog', 'acss-purger') }}
                                        </a>
                                    </span>
                                </h3>
                                <p class="tw-my-0 tw-text-base tw-text-gray-500">{{ __('Read our latest news and articles', 'acss-purger') }}</p>
                            </div>
                            <div class="tw-shrink-0 tw-self-center">
                                <!-- Heroicon name: mini/chevron-right -->
                                <svg class="tw-h-5 tw-w-5 tw-text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </li>
                    </ul>
                    <div class="tw-mt-8">
                        <router-link :to="{ name: 'home' }" class="tw-no-underline tw-text-base tw-font-medium tw-text-sky-600 hover:tw-text-sky-500">
                            {{ __('Or go back home', 'acss-purger') }}
                            <span aria-hidden="true"> &rarr;</span>
                        </router-link>
                    </div>
                </div>
            </div>
        </main>
    </div>


</template>

<script setup></script>
