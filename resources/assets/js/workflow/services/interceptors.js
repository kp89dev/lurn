Vue.http.interceptors.push((request,next) => {
    request.headers.set('X-CSRF-TOKEN', window._token);
    next();
});
