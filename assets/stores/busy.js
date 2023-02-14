import { defineStore } from 'pinia';

export const useBusy = defineStore('busy', {
    state: () => ({
        tasks: []
    }),
    actions: {
        /**
         * @param {string} task 
         */
        add(task = null) {
            this.tasks.unshift({
                timestamp: Date.now(),
                task: task,
            });
        },
        /**
         * @param {string} task 
         */
        remove(task = null) {
            let found = false;
            this.tasks = this.tasks.filter((t) => {
                if (found) {
                    return true;
                }
                if (t.task === task) {
                    found = true;
                    return false;
                }
                return true;
            });
        },
        reset() {
            this.tasks = [];
        }
    },
    getters: {
        /**
         * @returns {boolean}
         */
        isBusy: (state) => state.tasks.length > 0,
        /**
         * @param {string} task
         * @returns {Function}
         */
        hasTask: (state) => {
            return (task) => state.tasks.some((t) => t.task === task);
        },
    },
});