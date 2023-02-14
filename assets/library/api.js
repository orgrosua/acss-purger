import axios from 'axios';

export function useApi(config = {}) {
    return axios.create(Object.assign({
        baseURL: acssPurger.rest_api.url,
        headers: {
            'content-type': 'application/json',
            'accept': 'application/json',
            'X-WP-Nonce': acssPurger.rest_api.nonce,
        },
    }, config));
}