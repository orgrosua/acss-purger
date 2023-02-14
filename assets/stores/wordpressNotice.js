import { defineStore } from 'pinia';
import { nanoid } from 'nanoid';

export const useWordpressNotice = defineStore('wordpressNotice', {
    state: () => ({
        notices: []
    }),
    actions: {
        /**
         * @param {object} notice
         * @param {string} notice.message
         * @param {string} notice.type - 'success', 'info', 'warning', 'error'
         * @param {object} notice.options
         */
        add(notice) {
            let id = nanoid(10);
            this.notices.unshift({
                id,
                timestamp: Date.now(),
                ...notice,
            });

            return id;
        },
        remove(id) {
            this.notices = this.notices.filter((notice) => notice.id !== id);
        }
    },
});