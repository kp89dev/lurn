window.iziSuccess = (title, message, timeout = 4) => {
    iziToast.success({
        icon: 'check circle icon',
        title,
        message,
        timeout: timeout * 1000,
        position: 'topCenter',
        iconColor: '#fff',
        titleColor: '#fff',
        messageColor: '#fff',
        backgroundColor: '#16b15c',
    });
};

window.iziInfo = (title, message, timeout = 4) => {
    iziToast.success({
        icon: 'exclamation circle icon',
        title,
        message,
        timeout: timeout * 1000,
        position: 'topCenter',
        iconColor: '#fff',
        titleColor: '#fff',
        messageColor: '#fff',
        backgroundColor: '#1b9cce',
    });
};

window.iziWarning = (title, message, timeout = 4) => {
    iziToast.success({
        icon: 'warning sign icon',
        title,
        message,
        timeout: timeout * 1000,
        position: 'topCenter',
        iconColor: '#fff',
        titleColor: '#fff',
        messageColor: '#fff',
        backgroundColor: '#ef863c',
    });
};

window.iziError = (title, message, timeout = 4) => {
    iziToast.success({
        icon: 'close icon',
        title,
        message,
        timeout: timeout * 1000,
        position: 'topCenter',
        iconColor: '#fff',
        titleColor: '#fff',
        messageColor: '#fff',
        backgroundColor: '#dc5960',
    });
};