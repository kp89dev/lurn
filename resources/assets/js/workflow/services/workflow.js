import interceptors from './interceptors';

export default {
    save(request) {
        return Vue.http.post('/admin/workflows', request)
            .then((response) => Promise.resolve(response.data))
            .catch((error) => Promise.reject(error));
    }
}
