import axios from 'axios';

axios.interceptors.request.use((config) => {
    const user = JSON.parse(localStorage.getItem('user'));
    const conf = config;

    if (user) {
        conf.headers.Authorization = `Bearer ${user.token}`;
    }

    return conf;
}, err => Promise.reject(err));

let instance = {};
/* global test */
if (process.env.NODE_ENV !== 'production') {
    const API_ENDPOINT = process.env.API === 'test' ? test.API_ENDPOINT : '';

    instance = axios.create({
        baseURL: API_ENDPOINT,
    });
} else {
    instance = axios.create({
        baseURL: '',
    });
}

export const buildData = (params) => {
    const bodyFormData = new FormData();

    Object.keys(params).forEach((key) => {
        if (params[key] !== null) {
            bodyFormData.append(key, params[key]);
        }
    });

    return bodyFormData;
};

export const parseQueryParams = (params) => {
    let queryParams = '';
    Object.keys(params).forEach((key) => {
        if (params[key] !== null) {
            queryParams += (queryParams === '') ? '?' : '&';
            queryParams += `${key}=${params[key]}`;
        }
    });
    return queryParams;
};

export const getRequest = (path, queryParams = '') => instance.get(`${path}${queryParams}`);

export const postRequest = (path, data) => instance.post(path, data);

export const putRequest = (path, bodyFormData, id) => instance.post(`${path}/${id}`, bodyFormData);

export const deleteRequest = (path, id) => instance.delete(`${path}/${id}`);

export const HTTP = config => axios.create(config);
