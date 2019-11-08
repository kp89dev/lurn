/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 89);
/******/ })
/************************************************************************/
/******/ ({

/***/ 23:
/***/ (function(module, exports) {

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

(function (window, document) {
    'use strict';

    var Lurn = {},
        _on,
        _handler = [],
        _download_tracking = false,
        _download_pause,
        _outgoing_tracking = false,
        _outgoing_pause,
        _auto_decorate,
        _outgoing_ignore_subdomain = true;

    /**
     * Constants
     */
    var VERSION = 11;
    var ENDPOINT = window.location.hostname + '/track/';
    var XDM_PARAM_NAME = '__lurnid';

    /**
     * addEventListener polyfill 1.0 / Eirik Backer / MIT Licence
     * https://gist.github.com/eirikbacker/2864711
     * removeEventListener from https://gist.github.com/jonathantneal/3748027
     */
    /*eslint-disable*/
    (function (win, doc) {
        if (win.addEventListener) return; //No need to polyfill

        var listeners = [];

        function docHijack(p) {
            var old = doc[p];doc[p] = function (v) {
                return addListen(old(v));
            };
        }
        function addEvent(on, fn, self) {
            self = this;

            listeners.unshift([self, on, fn, function (e) {
                var e = e || win.event;
                e.preventDefault = e.preventDefault || function () {
                    e.returnValue = false;
                };
                e.stopPropagation = e.stopPropagation || function () {
                    e.cancelBubble = true;
                };
                e.currentTarget = self;
                e.target = e.srcElement || self;
                fn.call(self, e);
            }]);

            return this.attachEvent('on' + on, listeners[0][3]);
        }

        function removeEvent(on, fn) {
            for (var index = 0, register; register = listeners[index]; ++index) {
                if (register[0] == this && register[1] == on && register[2] == fn) {
                    return this.detachEvent("on" + on, listeners.splice(index, 1)[0][3]);
                }
            }
        }

        function addListen(obj, i) {
            if (obj && (i = obj.length)) {
                while (i--) {
                    obj[i].addEventListener = addEvent;
                    obj[i].removeEventListener = removeEvent;
                }
            } else if (obj) {
                obj.addEventListener = addEvent;
                obj.removeEventListener = removeEvent;
            }

            return obj;
        }

        addListen([doc, win]);
        if ('Element' in win) {
            // IE 8
            win.Element.prototype.addEventListener = addEvent;
            win.Element.prototype.removeEventListener = removeEvent;
        } else {
            // IE < 8
            //Make sure we also init at domReady
            doc.attachEvent('onreadystatechange', function () {
                addListen(doc.all);
            });
            docHijack('getElementsByTagName');
            docHijack('getElementById');
            docHijack('createElement');
            addListen(doc.all);
        }
    })(window, document);

    /**
     * Array.prototype.indexOf polyfill via
     * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/indexOf
     */
    if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function (searchElement, fromIndex) {
            if (this === undefined || this === null) {
                throw new TypeError('"this" is null or not defined');
            }

            var length = this.length >>> 0; // Hack to convert object.length to a UInt32

            fromIndex = +fromIndex || 0;

            if (Math.abs(fromIndex) === Infinity) {
                fromIndex = 0;
            }

            if (fromIndex < 0) {
                fromIndex += length;
                if (fromIndex < 0) {
                    fromIndex = 0;
                }
            }

            for (; fromIndex < length; fromIndex++) {
                if (this[fromIndex] === searchElement) {
                    return fromIndex;
                }
            }

            return -1;
        };
    }

    /**
     * Helper functions
     */
    Lurn.extend = function (o1, o2) {
        for (var key in o2) {
            o1[key] = o2[key];
        }
    };

    // https://code.google.com/p/form-serialize/
    // modified to return an object
    Lurn.serializeForm = function (form, options) {
        if (!form || form.nodeName !== "FORM") {
            return;
        }
        var _options = options || {};
        var _exclude = _options.exclude || [];
        var i,
            j,
            data = {};
        for (i = form.elements.length - 1; i >= 0; i = i - 1) {
            if (form.elements[i].name === "" || _exclude.indexOf(form.elements[i].name) > -1) {
                continue;
            }
            switch (form.elements[i].nodeName) {
                case 'INPUT':
                    switch (form.elements[i].type) {
                        case 'text':
                        case 'hidden':
                        case 'button':
                        case 'reset':
                        case 'submit':
                            data[form.elements[i].name] = form.elements[i].value;
                            break;
                        case 'checkbox':
                        case 'radio':
                            if (form.elements[i].checked) {
                                data[form.elements[i].name] = form.elements[i].value;
                            }
                            break;
                        case 'file':
                            break;
                    }
                    break;
                case 'TEXTAREA':
                    data[form.elements[i].name] = form.elements[i].value;
                    break;
                case 'SELECT':
                    switch (form.elements[i].type) {
                        case 'select-one':
                            data[form.elements[i].name] = form.elements[i].value;
                            break;
                        case 'select-multiple':
                            for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
                                if (form.elements[i].options[j].selected) {
                                    data[form.elements[i].name] = form.elements[i].options[j].value;
                                }
                            }
                            break;
                    }
                    break;
                case 'BUTTON':
                    switch (form.elements[i].type) {
                        case 'reset':
                        case 'submit':
                        case 'button':
                            data[form.elements[i].name] = form.elements[i].value;
                            break;
                    }
                    break;
            }
        }
        return data;
    };

    /*\
     |*|
     |*|  :: cookies.js ::
     |*|
     |*|  A complete cookies reader/writer framework with full unicode support.
     |*|
     |*|  Revision #1 - September 4, 2014
     |*|
     |*|  https://developer.mozilla.org/en-US/docs/Web/API/document.cookie
     |*|  https://developer.mozilla.org/User:fusionchess
     |*|
     |*|  This framework is released under the GNU Public License, version 3 or later.
     |*|  http://www.gnu.org/licenses/gpl-3.0-standalone.html
     |*|
     |*|  Syntaxes:
     |*|
     |*|  * docCookies.setItem(name, value[, end[, path[, domain[, secure]]]])
     |*|  * docCookies.getItem(name)
     |*|  * docCookies.removeItem(name[, path[, domain]])
     |*|  * docCookies.hasItem(name)
     |*|  * docCookies.keys()
     |*|
     \*/
    var docCookies = {
        getItem: function getItem(sKey) {
            if (!sKey) {
                return null;
            }
            return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
        },
        setItem: function setItem(sKey, sValue, vEnd, sPath, sDomain, bSecure) {
            if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) {
                return false;
            }
            var sExpires = "";
            if (vEnd) {
                switch (vEnd.constructor) {
                    case Number:
                        sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + vEnd;
                        break;
                    case String:
                        sExpires = "; expires=" + vEnd;
                        break;
                    case Date:
                        sExpires = "; expires=" + vEnd.toUTCString();
                        break;
                }
            }
            document.cookie = encodeURIComponent(sKey) + "=" + encodeURIComponent(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
            return true;
        },
        removeItem: function removeItem(sKey, sPath, sDomain) {
            if (!this.hasItem(sKey)) {
                return false;
            }
            document.cookie = encodeURIComponent(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "");
            return true;
        },
        hasItem: function hasItem(sKey) {
            if (!sKey) {
                return false;
            }
            return new RegExp("(?:^|;\\s*)" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=").test(document.cookie);
        },
        keys: function keys() {
            var aKeys = document.cookie.replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "").split(/\s*(?:\=[^;]*)?;\s*/);
            for (var nLen = aKeys.length, nIdx = 0; nIdx < nLen; nIdx++) {
                aKeys[nIdx] = decodeURIComponent(aKeys[nIdx]);
            }
            return aKeys;
        }
    };

    Lurn.docCookies = docCookies;
    /*eslint-enable*/

    /**
     * Wrapper for window.location
     */
    Lurn.location = function (property, value) {
        // make sure property is valid
        if (typeof window.location[property] !== 'undefined') {
            if (typeof value !== 'undefined') {
                window.location[property] = value;
            } else {
                return window.location[property];
            }
        }
    };

    /**
     * Parses current URL for parameters that start with either `utm_` or `woo_`
     * and have the keys `source`, `medium`, `content`, `campaign`, `term`
     *
     * @return {Object} Returns an object with campaign keys as keys
     */
    Lurn.getCampaignData = function () {
        var vars = Lurn.getUrlParams(),
            campaign = {},
            campaignKeys = ['source', 'medium', 'content', 'campaign', 'term'],
            key,
            value;

        for (var i = 0; i < campaignKeys.length; i++) {
            key = campaignKeys[i];
            value = vars['utm_' + key] || vars['woo_' + key];

            if (typeof value !== 'undefined') {
                campaign['campaign_' + (key === 'campaign' ? 'name' : key)] = value;
            }
        }

        return campaign;
    };

    Lurn.mapQueryParams = function (mapping) {
        var vars = Lurn.getUrlParams(),
            params = {};

        for (var key in mapping) {
            var value = vars[key];
            if (typeof value !== 'undefined') {
                params[mapping[key]] = value;
            }
        }

        return params;
    };

    /**
     * Parses the URL parameters for data beginning with a certain prefix
     *
     * @param {Function} method The callback method for each key found matching `prefix`
     * @param {string} prefix The prefix that the parameter should start with
     */
    Lurn.getCustomData = function (method, prefix) {
        var vars = Lurn.getUrlParams(),
            i,
            _prefix = prefix || 'wv_',
            key,
            value;

        for (i in vars) {
            if (vars.hasOwnProperty(i)) {
                value = vars[i];

                if (i.substring(0, _prefix.length) === _prefix) {
                    key = i.substring(_prefix.length);
                    method.call(this, key, value);
                }
            }
        }
    };

    /**
     * Parses Visitor Data in the URL.
     *
     * Query params that start with 'wv_'
     */
    Lurn.getVisitorUrlData = function (context) {
        Lurn.getCustomData.call(context, context.identify, 'wv_');
    };

    /**
     * Hides any campaign data (query params: wv_, woo_, utm_) from the URL
     * by using pushState (if available)
     */
    Lurn.hideCampaignData = function () {
        return Lurn.hideUrlParams(['wv_', 'woo_', 'utm_']);
    };
    Lurn.hideCrossDomainId = function () {
        return Lurn.hideUrlParams([XDM_PARAM_NAME]);
    };

    /**
     * Hides any URL parameters by calling window.history.replaceState
     *
     * @param {Array} params A list of parameter prefixes that will be hidden
     * @return {String} Returns the new URL that will be used
     */
    Lurn.hideUrlParams = function (params) {
        var regex = new RegExp('[?&]+((?:' + params.join('|') + ')[^=&]*)=([^&#]*)', 'gi');
        var href = Lurn.location('href').replace(regex, '');

        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, null, href);
        }

        return href;
    };

    /**
     * Retrieves the current URL parameters as an object
     *
     * @return {Object} An object for all of the URL parameters
     */
    Lurn.getUrlParams = function () {
        var vars = {};
        var href = Lurn.location('href');

        if (href) {
            href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
                vars[key] = decodeURIComponent(value.split('+').join(' '));
            });
        }
        return vars;
    };

    Lurn.buildUrlParams = function (params, prefix) {
        var _prefix = prefix || '',
            key,
            p = [];

        if (typeof params === 'undefined') {
            return params;
        }

        for (key in params) {
            if (params.hasOwnProperty(key)) {
                if (params[key] !== 'undefined' && params[key] !== 'null' && typeof params[key] !== 'undefined') {
                    p.push(_prefix + encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
                }
            }
        }
        return p.join('&');
    };

    /**
     * Generates a random 12 character string
     *
     * @return {String} Returns a random 12 character string
     */
    Lurn.randomString = function () {
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            i,
            rnum,
            s = '';

        for (i = 0; i < 12; i++) {
            rnum = Math.floor(Math.random() * chars.length);
            s += chars.substring(rnum, rnum + 1);
        }

        return s;
    };

    Lurn.loadScript = function (url, callback) {
        var ssc,
            _callback,
            script = document.createElement('script');

        script.type = 'text/javascript';
        script.async = true;

        if (callback && typeof callback === 'function') {
            _callback = callback;
        }

        if (typeof script.onreadystatechange !== 'undefined') {
            script.onreadystatechange = function () {
                if (this.readyState === 4 || this.readyState === 'complete' || this.readyState === 'loaded') {
                    if (_callback) {
                        _callback();
                    }
                    Lurn.removeScript(script);
                }
            };
        } else {
            script.onload = function () {
                if (_callback) {
                    _callback();
                }
                Lurn.removeScript(script);
            };
            script.onerror = function () {
                Lurn.removeScript(script);
            };
        }

        script.src = url;

        ssc = document.getElementsByTagName('script')[0];
        ssc.parentNode.insertBefore(script, ssc);
    };

    Lurn.removeScript = function (script) {
        if (script && script.parentNode) {
            script.parentNode.removeChild(script);
        }
    };

    /**
     * Helper to either query an element by id, or return element if passed
     * through options
     *
     * Supports searching by ids and classnames (or querySelector if browser supported)
     */
    Lurn.getElement = function (selector, options) {
        var _options = typeof selector === 'string' ? options || {} : selector || {};
        var _selector = selector;

        if (_options.el) {
            return _options.el;
        } else if (typeof selector === 'string') {
            if (document.querySelectorAll) {
                return document.querySelectorAll(_selector);
            } else if (selector[0] === '#') {
                _selector = selector.substr(1);
                return document.getElementById(_selector);
            } else if (selector[0] === '.') {
                _selector = selector.substr(1);
                return document.getElementsByClassName(_selector);
            }
        }
    };

    /**
     * Retrieves the current client domain name using the hostname
     * and returning the last two tokens with a `.` separator (domain + tld).
     *
     * This can be an issue if there is a second level domain
     */
    Lurn.getDomain = function (hostname) {
        var _hostname = hostname || Lurn.location('hostname');
        var secondLevelTlds = {
            'com.au': 1,
            'net.au': 1,
            'org.au': 1,
            'co.hu': 1,
            'com.ru': 1,
            'ac.za': 1,
            'net.za': 1,
            'com.za': 1,
            'co.za': 1,
            'co.uk': 1,
            'org.uk': 1,
            'me.uk': 1,
            'net.uk': 1
        };
        var domain = _hostname.substring(_hostname.lastIndexOf('.', _hostname.lastIndexOf('.') - 1) + 1);

        // check if domain is in list of second level domains, ignore if so
        if (secondLevelTlds[domain]) {
            domain = _hostname.substring(_hostname.lastIndexOf('.', _hostname.indexOf(domain) - 2) + 1);
        }

        return domain;
    };

    /**
     * Returns the current hostname with 'www' stripped out
     */
    Lurn.getHostnameNoWww = function () {
        var hostname = Lurn.location('hostname');

        if (hostname.indexOf('www.') === 0) {
            return hostname.replace('www.', '');
        }

        return hostname;
    };

    /**
     * Checks if string ends with suffix
     *
     * @param {string} str The haystack string
     * @param {string} suffix The needle
     * @return {boolean} True if needle was found in haystack
     */
    Lurn.endsWith = function (str, suffix) {
        return str.indexOf(suffix, str.length - suffix.length) !== -1;
    };

    /**
     * Checks if string starts with prefix
     *
     * @param {string} str The haystack string
     * @param {string} prefix The needle
     * @return {boolean} True if needle was found in haystack
     */
    Lurn.startsWith = function (str, prefix) {
        return str.indexOf(prefix) === 0;
    };

    _on = Lurn._on = function (parent, event, callback) {
        var id = parent.instanceName;

        if (!_handler[event]) {
            _handler[event] = {};
        }
        _handler[event][id] = parent;

        if (parent.__l) {
            if (!parent.__l[event]) {
                parent.__l[event] = [];
            }
            parent.__l[event].push(callback);
        }
    };

    Lurn._fire = function (event) {
        var handler;
        var _event = _handler[event];
        var _l;

        if (_event) {
            for (var id in _event) {
                if (_event.hasOwnProperty(id)) {
                    handler = _event[id];
                    _l = handler && handler.__l;
                    if (_l && _l[event]) {
                        for (var i = 0; i < _l[event].length; i++) {
                            _l[event][i].apply(this, Array.prototype.slice.call(arguments, 1));
                        }
                    }
                }
            }
        }
    };

    Lurn.attachEvent = function (element, type, callback) {
        if (element.addEventListener) {
            element.addEventListener(type, callback);
        } else if (element.attachEvent) {
            /*eslint-disable*/
            element.attachEvent('on' + type, function (e) {
                var e = e || win.event;
                e.preventDefault = e.preventDefault || function () {
                    e.returnValue = false;
                };
                e.stopPropagation = e.stopPropagation || function () {
                    e.cancelBubble = true;
                };
                callback.call(self, e);
            });
            /*eslint-enable*/
        }
    };

    Lurn.leftClick = function (evt) {
        evt = evt || window.event;
        var button = typeof evt.which !== 'undefined' && evt.which === 1 || typeof evt.button !== 'undefined' && evt.button === 0;
        return button && !evt.metaKey && !evt.altKey && !evt.ctrlKey && !evt.shiftKey;
    };

    Lurn.redirect = function (link) {
        Lurn.location('href', link);
    };

    /**
     * Determines if the current URL should be considered an outgoing URL
     */
    Lurn.isOutgoingLink = function (targetHostname) {
        var currentHostname = Lurn.location('hostname');
        var currentDomain = Lurn.getDomain(currentHostname);

        return targetHostname !== currentHostname && targetHostname.replace(/^www\./, '') !== currentHostname.replace(/^www\./, '') && (!_outgoing_ignore_subdomain || currentDomain !== Lurn.getDomain(targetHostname)) && !Lurn.startsWith(targetHostname, 'javascript') && targetHostname !== '' && targetHostname !== '#';
    };

    // attaches any events
    // needs to be handled here, instead of in a tracking instance because
    // these events should only be fired once on a page
    (function (on, fire) {
        on(document, 'mousedown', function (e) {
            var cElem;

            fire('mousemove', e, new Date());

            if (_auto_decorate) {
                cElem = e.srcElement || e.target;
                while (typeof cElem !== 'undefined' && cElem !== null) {
                    if (cElem.tagName && cElem.tagName.toLowerCase() === 'a') {
                        break;
                    }
                    cElem = cElem.parentNode;
                }
                if (typeof cElem !== 'undefined' && cElem !== null) {
                    fire('auto_decorate', cElem);
                }
            }
        });

        on(document, 'click', function (e) {
            var cElem,
                link,
                ignoreTarget = '_blank',
                _download;

            cElem = e.srcElement || e.target;

            if (Lurn.leftClick(e)) {
                fire('click', e, cElem);
            }

            if (_download_tracking || _outgoing_tracking) {

                // searches for an anchor element
                while (typeof cElem !== 'undefined' && cElem !== null) {
                    if (cElem.tagName && cElem.tagName.toLowerCase() === 'a') {
                        break;
                    }
                    cElem = cElem.parentNode;
                }

                if (typeof cElem !== 'undefined' && cElem !== null && !cElem.getAttribute('data-lurn-tracked')) {
                    link = cElem;
                    _download = link.pathname.match(/(?:doc|dmg|eps|svg|xls|ppt|pdf|xls|zip|txt|vsd|vxd|js|css|rar|exe|wma|mov|avi|wmv|mp3|mp4|m4v)($|\&)/);

                    if (_download_tracking && _download) {
                        fire('download', link.href);

                        if (link.target !== ignoreTarget && Lurn.leftClick(e)) {
                            e.preventDefault();
                            e.stopPropagation();

                            link.setAttribute('data-lurn-tracked', true);
                            window.setTimeout(function () {
                                link.click();
                            }, _download_pause);
                        }
                    }
                    // Make sure
                    // * outgoing tracking is enabled
                    // * this URL does not match a download URL (doesn't end
                    //   in a binary file extension)
                    // * not ignoring subdomains OR link hostname is not a partial
                    //   match of current hostname (to check for subdomains),
                    // * hostname is not empty
                    if (_outgoing_tracking && !_download && Lurn.isOutgoingLink(link.hostname)) {
                        fire('outgoing', link.href);

                        if (link.target !== ignoreTarget && Lurn.leftClick(e)) {
                            e.preventDefault();
                            e.stopPropagation();

                            link.setAttribute('data-lurn-tracked', true);

                            window.setTimeout(function () {
                                link.click();
                            }, _outgoing_pause);
                        }
                    }
                }
            }
        });

        on(document, 'mousemove', function (e) {
            fire('mousemove', e, new Date());
        });

        on(document, 'keydown', function () {
            fire('keydown');
        });
    })(Lurn.attachEvent, Lurn._fire);

    var Tracker = function Tracker(instanceName) {
        this.visitorData = {};
        this.sessionData = {};

        this.options = {
            app: 'js-client',
            use_cookies: true,
            ping: true,
            ping_interval: 12000,
            idle_timeout: 300000,
            idle_threshold: 10000,
            download_pause: _download_pause || 200,
            outgoing_pause: _outgoing_pause || 200,
            download_tracking: false,
            outgoing_tracking: false,
            outgoing_ignore_subdomain: true,
            hide_campaign: false,
            hide_xdm_data: false,
            campaign_once: false,
            third_party: false,
            save_url_hash: true,
            cross_domain: false,
            region: null,
            ignore_query_url: false,
            map_query_params: {},
            cookie_name: '__lurn_nation',
            cookie_domain: '.' + Lurn.getHostnameNoWww(),
            cookie_path: '/',
            cookie_expire: new Date(new Date().setDate(new Date().getDate() + 730))
        };

        this.instanceName = instanceName || 'lurn';
        this.idle = 0;
        this.cookie = '';
        this.last_activity = new Date();
        this.loaded = false;
        this.dirtyCookie = false;
        this.sentCampaign = false;
        this.version = VERSION;

        if (instanceName && instanceName !== '') {
            window[instanceName] = this;
        }
    };

    Tracker.prototype = {
        docCookies: docCookies,
        init: function init() {
            var callback,
                self = this;

            this.__l = {};
            this._processQueue('config');
            this._setupCookie();
            this._bindEvents();

            // Otherwise loading indicator gets stuck until the every response
            // in the queue has been received
            setTimeout(function () {
                self._processQueue();
            }, 1);

            this.loaded = true;

            callback = this.config('initialized');
            if (callback && typeof callback === 'function') {
                callback(this.instanceName);
            }

            // Safe to remove cross domain url parameter after setupCookie is called
            // Should only need to be called once on load
            if (this.config('hide_xdm_data')) {
                Lurn.hideCrossDomainId();
            }
        },

        /**
         * Processes the tracker queue in case user tries to push events
         * before tracker is ready.
         */
        _processQueue: function _processQueue(type) {
            var i, action, events, _wpt;

            _wpt = window.__lurn ? window.__lurn[this.instanceName] : _wpt;
            _wpt = window._w ? window._w[this.instanceName] : _wpt;

            // if _wpt is undefined, means script was loaded asynchronously and
            // there is no queue

            if (_wpt && _wpt._e) {
                events = _wpt._e;
                for (i = 0; i < events.length; i++) {
                    action = events[i];
                    if (typeof action !== 'undefined' && this[action[0]] && (typeof type === 'undefined' || type === action[0])) {
                        this[action[0]].apply(this, Array.prototype.slice.call(action, 1));
                    }
                }
            }
        },

        /**
         * Sets up the tracking cookie
         */
        _setupCookie: function _setupCookie() {
            var url_id = this.getUrlId();

            this.cookie = this.getCookie();

            // overwrite saved cookie if id is in url
            if (url_id) {
                this.cookie = url_id;
            }

            // Setup cookie
            if (!this.cookie || this.cookie.length < 1) {
                this.cookie = Lurn.randomString();
            }

            docCookies.setItem(this.config('cookie_name'), this.cookie, this.config('cookie_expire'), this.config('cookie_path'), this.config('cookie_domain'));

            this.dirtyCookie = true;
        },

        /**
         * Binds some events to measure mouse and keyboard events
         */
        _bindEvents: function _bindEvents() {
            var self = this;

            _on(this, 'mousemove', function () {
                self.moved.apply(self, arguments);
            });
            _on(this, 'keydown', function () {
                self.typed.apply(self, arguments);
            });
            _on(this, 'download', function () {
                self.downloaded.apply(self, arguments);
            });
            _on(this, 'outgoing', function () {
                self.outgoing.apply(self, arguments);
            });
            _on(this, 'auto_decorate', function () {
                self.autoDecorate.apply(self, arguments);
            });
        },

        /**
         * Sets/gets values from dataStore depending on arguments passed
         *
         * @param dataStore Object The tracker property to read/write
         * @param key String/Object Returns property object if key and value is undefined,
         *      acts as a getter if only `key` is defined and a string, and
         *      acts as a setter if `key` and `value` are defined OR if `key` is an object.
         */
        _dataSetter: function _dataSetter(dataStore, key, value) {
            var i;

            if (typeof key === 'undefined') {
                return dataStore;
            }

            if (typeof value === 'undefined') {
                if (typeof key === 'string') {
                    return dataStore[key];
                }
                if ((typeof key === 'undefined' ? 'undefined' : _typeof(key)) === 'object') {
                    for (i in key) {
                        if (key.hasOwnProperty(i)) {
                            if (i.substring(0, 7) === 'cookie_') {
                                this.dirtyCookie = true;
                            }
                            dataStore[i] = key[i];
                        }
                    }
                }
            } else {
                if (key.substring(0, 7) === 'cookie_') {
                    this.dirtyCookie = true;
                }
                dataStore[key] = value;
            }

            return this;
        },

        /**
         * Builds the correct tracking Url and performs an HTTP request
         */
        _push: function _push(options) {
            var _options = options || {},
                random = 'ra=' + Lurn.randomString(),
                queryString,
                endpoint,
                urlParam,
                scriptUrl,
                types = [['visitorData', 'cv_'], ['eventData', 'ce_'], ['sessionData', 'cs_']],
                _type,
                i,
                data = [];

            endpoint = this.getEndpoint(_options.endpoint);

            // Load custom visitor params from url
            Lurn.getVisitorUrlData(this);

            if (this.config('hide_campaign')) {
                Lurn.hideCampaignData();
            }

            data.push(random);

            // push tracker config values
            data.push(Lurn.buildUrlParams(this.getOptionParams()));

            // push eventName if it exists
            if (_options.eventName) {
                data.push('event=' + _options.eventName);
            }

            for (i in types) {
                if (types.hasOwnProperty(i)) {
                    _type = types[i];
                    if (_options[_type[0]]) {
                        urlParam = Lurn.buildUrlParams(_options[_type[0]], _type[1]);
                        if (urlParam) {
                            data.push(urlParam);
                        }
                    }
                }
            }

            queryString = '?' + data.join('&');

            scriptUrl = endpoint + queryString;
            Lurn.loadScript(scriptUrl, _options.callback);
        },

        /*
         * Returns the Lurn cookie string
         */
        getCookie: function getCookie() {
            return docCookies.getItem(this.config('cookie_name'));
        },

        /**
         * Generates a destination endpoint string to use depending on different
         * configuration options
         */
        getEndpoint: function getEndpoint(path) {
            var protocol = this.config('protocol');
            var _protocol = protocol && protocol !== '' ? protocol + ':' : '';
            var _path = path || '';
            var endpoint = _protocol + '//';
            var region = this.config('region');
            var thirdPartyPath;

            if (this.config('third_party') && !this.config('domain')) {
                throw new Error('Error: `domain` is not set.');
            }

            // create endpoint, default is www.lurn.com/track/
            // China region will be cn.t.lurn.com/track
            if (region) {
                endpoint += region + '.t.';
            }
            // else {
            //     endpoint += 'www.';
            // }

            thirdPartyPath = this.config('third_party') ? 'tp/' + this.config('domain') : '';

            if (_path && !Lurn.endsWith(_path, '/')) {
                _path += '/';
            }

            if (thirdPartyPath && !Lurn.startsWith(_path, '/')) {
                thirdPartyPath += '/';
            }

            endpoint += ENDPOINT + thirdPartyPath + _path;

            return endpoint;
        },

        /**
         * Sets configuration options
         */
        config: function config(key, value) {
            var data = this._dataSetter(this.options, key, value);

            // dataSetter returns `this` when it is used as a setter
            if (data === this) {
                // do validation
                if (this.options.ping_interval < 6000) {
                    this.options.ping_interval = 6000;
                } else if (this.options.ping_interval > 60000) {
                    this.options.ping_interval = 60000;
                }

                // set script wide variables for events that are bound on script load
                // since we shouldn't bind per tracker instance
                _outgoing_tracking = this.options.outgoing_tracking;
                _outgoing_pause = this.options.outgoing_pause;
                _download_tracking = this.options.download_tracking;
                _download_pause = this.options.download_pause;
                _auto_decorate = typeof _auto_decorate === 'undefined' && this.options.cross_domain ? this.options.cross_domain : _auto_decorate;
                _outgoing_ignore_subdomain = this.options.outgoing_ignore_subdomain;

                if (this.dirtyCookie && this.loaded) {
                    this._setupCookie();
                }
            }

            return data;
        },

        /**
         * Use to attach custom visit data that doesn't stick to visitor
         * ** Not in use yet
         */
        visit: function visit(key, value) {
            return this._dataSetter(this.sessionData, key, value);
        },

        /**
         * Attach custom visitor data
         */
        identify: function identify(key, value) {
            return this._dataSetter(this.visitorData, key, value);
        },

        /**
         * Generic method to call any tracker method
         */
        call: function call(funcName) {
            if (this[funcName] && typeof this[funcName] === 'function') {
                this[funcName].apply(this, Array.prototype.slice.call(arguments, 1));
            }
        },

        /**
         * Send an event to tracking servr
         */
        track: function track(name, options) {

            var event = {},
                eventName = '',
                cb,
                _hash,
                _cb = arguments[arguments.length - 1];

            // Load campaign params (load first to allow overrides)
            if (!this.config('campaign_once') || !this.sentCampaign) {
                Lurn.extend(event, Lurn.getCampaignData());
                this.sentCampaign = true;
            }

            // Load query params mapping into Lurn event
            Lurn.extend(event, Lurn.mapQueryParams(this.config('map_query_params')));

            if (typeof _cb === 'function') {
                cb = _cb;
            }
            // Track default: pageview
            if (typeof name === 'undefined' || name === cb) {
                eventName = 'pv';
            }
            // Track custom events
            else if (typeof options === 'undefined' || options === cb) {
                    if (typeof name === 'string') {
                        eventName = name;
                    }
                    if ((typeof name === 'undefined' ? 'undefined' : _typeof(name)) === 'object') {
                        if (name.name && name.name === 'pv') {
                            eventName = 'pv';
                        }

                        this._dataSetter(event, name);
                    }
                }
                // Track custom events in format of name,object
                else {
                        this._dataSetter(event, options);
                        eventName = name;
                    }

            // Add some defaults for pageview
            if (eventName === 'pv') {
                event.url = event.url || this.getPageUrl();
                event.title = event.title || this.getPageTitle();
                event.domain = event.domain || this.getDomainName();
                event.uri = event.uri || this.getURI();

                if (this.config('save_url_hash')) {
                    _hash = event.hash || this.getPageHash();
                    if (_hash !== '') {
                        event.hash = _hash;
                    }
                }
            }

            this._push({
                endpoint: 'ce',
                visitorData: this.visitorData,
                sessionData: this.sessionData,
                eventName: eventName,
                eventData: event,
                callback: cb
            });

            this.startPing();
        },

        /**
         * Tracks a single form and then resubmits it
         */
        trackForm: function trackForm(eventName, selector, options) {
            var els;
            var _event = eventName || 'Tracked Form';
            var _options = typeof selector === 'string' ? options || {} : selector || {};
            var bindEl;
            var self = this;

            bindEl = function bindEl(el, ev, props, opts) {
                Lurn.attachEvent(el, 'submit', function (e) {
                    self.trackFormHandler(e, el, ev, _options);
                });
            };

            if (_options.elements) {
                els = _options.elements;
            } else {
                els = Lurn.getElement(selector, _options);
            }

            // attach event if form was found
            if (els && els.length > 0) {
                for (var i in els) {
                    bindEl(els[i], _event, _options);
                }
            }
        },

        trackFormHandler: function trackFormHandler(e, el, eventName, options) {
            var data;
            var personData;
            var trackFinished = false;

            if (!el.getAttribute('data-tracked')) {
                data = Lurn.serializeForm(el, options);

                if (options.identify && typeof options.identify === 'function') {
                    personData = options.identify(data) || {};
                    if (personData) {
                        this.identify(personData);
                    }
                }

                if (options.noSubmit) {
                    this.track(eventName, data, function () {
                        if (typeof options.callback === 'function') {
                            options.callback(data);
                        }
                    });
                } else {
                    e.preventDefault();
                    e.stopPropagation();

                    el.setAttribute('data-tracked', 1);

                    // submit the form if the reply takes less than 250ms
                    this.track(eventName, data, function () {
                        trackFinished = true;

                        if (typeof options.callback === 'function') {
                            options.callback(data);
                        }

                        el.submit();
                    });

                    // set timeout to resubmit to be a hard 250ms
                    // so even if lurn does not reply it will still
                    // submit the form
                    setTimeout(function () {
                        if (!trackFinished) {
                            el.submit();
                        }
                    }, 250);
                }
            }
        },

        /**
         * Tracks clicks
         *
         * @param {String} eventName The name of the event to track
         * @param {String} selector The id of element to track
         * @param {Object} properties Any event properties to track with
         * @param {Object} options (Optional) Options object
         * @param {Array} options.elements Supports an array of elements (jQuery object)
         * @param {Boolean} options.noNav (Default: false) If true, will only perform the track event and let the click event bubble up
         */
        trackClick: function trackClick(eventName, selector, properties, options) {
            var els = [];
            var i;
            var _options = options || {};
            var _event = eventName || 'Item Clicked';
            var bindEl;
            var self = this;

            bindEl = function bindEl(el, ev, props, opts) {
                Lurn.attachEvent(el, 'click', function (e) {
                    self.trackClickHandler(e, el, ev, props, opts);
                });
            };

            /**
             * Support an array of elements
             */
            if (_options.elements) {
                els = _options.elements;
            } else {
                els = Lurn.getElement(selector, _options);
            }

            if (els) {
                for (i = 0; i < els.length; i++) {
                    bindEl(els[i], _event, properties, _options);
                }
            }
        },

        trackClickHandler: function trackClickHandler(e, el, eventName, properties, options) {
            var trackFinished = false;

            if (!el.getAttribute('data-tracked')) {
                if (options.noNav) {
                    this.track(eventName, properties);
                } else {
                    e.preventDefault();

                    el.setAttribute('data-tracked', 1);

                    this.track(eventName, properties, function () {
                        trackFinished = true;

                        if (typeof options.callback === 'function') {
                            options.callback();
                        }

                        el.click();
                    });

                    setTimeout(function () {
                        if (!trackFinished) {
                            el.click();
                        }
                    }, 250);
                }
            }
        },

        startPing: function startPing() {
            var self = this;

            if (typeof this.pingInterval === 'undefined') {
                this.pingInterval = window.setInterval(function () {
                    self.ping();
                }, this.config('ping_interval'));
            }
        },

        stopPing: function stopPing() {
            if (typeof this.pingInterval !== 'undefined') {
                window.clearInterval(this.pingInterval);
                delete this.pingInterval;
            }
        },

        /**
         * Pings tracker with visitor info
         */
        ping: function ping() {
            var now;

            if (this.config('ping') && this.idle < this.config('idle_timeout')) {
                this._push({
                    endpoint: 'ping'
                });
            } else {
                this.stopPing();
            }

            now = new Date();
            if (now - this.last_activity > this.config('idle_threshold')) {
                this.idle = now - this.last_activity;
            }

            return this;
        },

        /**
         * Pushes visitor data to server without sending an event
         */
        push: function push(cb) {
            this._push({
                endpoint: 'identify',
                visitorData: this.visitorData,
                sessionData: this.sessionData,
                callback: cb
            });
            return this;
        },

        /**
         * synchronous sleep
         */
        sleep: function sleep() {},

        // User Action tracking and event handlers

        /**
         * Clicks
         */

        /**
         * Measure when the user last moved their mouse to update idle state
         */
        moved: function moved(e, last_activity) {
            this.last_activity = last_activity;
            this.idle = 0;
        },

        /**
         * Measure when user last typed
         */
        typed: function typed() {
            this.vs = 2;
        },

        downloaded: function downloaded(url) {
            this.track('download', {
                url: url
            });
        },

        outgoing: function outgoing(url) {
            this.track('outgoing', {
                url: url
            });
        },

        /**
         * Event handler for decorating an element with a URL (for now only
         * anchor tags)
         */
        autoDecorate: function autoDecorate(elem) {
            var decorated;
            var canDecorate;
            var xdm = this.config('cross_domain');

            if (xdm) {
                if (typeof xdm === 'string') {
                    canDecorate = elem.href.indexOf(xdm) > -1;
                } else if (xdm.push) {
                    canDecorate = xdm.indexOf(elem.hostname) > -1;
                }

                if (canDecorate) {
                    decorated = this.decorate(elem);

                    if (decorated) {
                        elem.href = decorated;
                        // bind an event handler on mouseup to remove the url
                    }
                }
            }
        },

        /**
         * Resets cookie
         */
        reset: function reset() {
            docCookies.removeItem(this.config('cookie_name'), this.config('cookie_path'), this.config('cookie_domain'));
            this.cookie = null;
            this._setupCookie();
        },

        /**
         * Decorates a given URL with a __lurnid query param with value of
         * the current cookie
         */
        decorate: function decorate(url) {
            var el;
            var query;
            var pathname;
            var host;

            if (typeof url === 'string') {
                el = document.createElement('a');
                el.href = url;
                query = el.search ? '&' : '?';
            } else if (url && url.href) {
                el = url;
            }

            if (el) {
                query = el.search ? '&' : '?';
                pathname = el.pathname && el.pathname.charAt(0) === '/' ? el.pathname : '/' + el.pathname;

                host = el.hostname + (el.port && el.port !== '' && el.port !== '80' && el.port !== '0' ? ':' + el.port : '');

                return el.protocol + '//' + host + pathname + el.search + query + XDM_PARAM_NAME + '=' + this.cookie + el.hash;
            }
        },

        /**
         * Undecorates a URL with __lurnid query param
         */
        undecorate: function undecorate(url) {
            var regex = new RegExp('[?&]+(?:' + XDM_PARAM_NAME + ')=([^&#]*)', 'gi');
            var _url = url;

            if (url && url.href) {
                _url = url.href;
            }

            if (_url) {
                return _url.replace(regex, '');
            }
        },

        getPageUrl: function getPageUrl() {
            if (this.options.ignore_query_url) {
                return Lurn.location('pathname');
            } else {
                return Lurn.location('pathname') + Lurn.location('search');
            }
        },

        getPageHash: function getPageHash() {
            return Lurn.location('hash');
        },

        getPageTitle: function getPageTitle() {
            return document.getElementsByTagName('title').length === 0 ? '' : document.getElementsByTagName('title')[0].innerHTML;
        },

        getDomainName: function getDomainName() {
            return Lurn.location('hostname');
        },

        getURI: function getURI() {
            return Lurn.location('href');
        },

        /**
         * Retrieves a Lurn unique id from a URL's query param (__lurnid)
         *
         * @param {String} href The full URL to extract from
         */
        getUrlId: function getUrlId(href) {
            var _href = href || Lurn.location('href');
            var matches;
            var regex = new RegExp(XDM_PARAM_NAME + '=([^&#]+)');

            matches = _href.match(regex);

            if (matches && matches[1]) {
                return matches[1];
            }
        },

        getOptionParams: function getOptionParams() {
            // default params
            var o = {
                alias: this.config('domain') || Lurn.getHostnameNoWww(),
                instance: this.instanceName,
                ka: this.config('keep_alive') || this.config('ping_interval') * 2,
                meta: docCookies.getItem('lurnMeta') || '',
                screen: window.screen.width + 'x' + window.screen.height,
                language: window.navigator.browserLanguage || window.navigator.language || '',
                app: this.config('app'),
                referer: document.referrer,
                idle: '' + parseInt(this.idle / 1000, 10),
                vs: 'i'
            };

            if (!this.config('domain')) {
                o._warn = 'no_domain';

                if (Lurn.getHostnameNoWww() !== Lurn.getDomain()) {
                    o._warn += ',domain_mismatch';
                }
            }

            // set cookie if configured
            if (this.config('use_cookies')) {
                o.cookie = this.getCookie() || this.cookie;
            }

            // set ip if configured
            if (this.config('ip')) {
                o.ip = this.config('ip');
            }
            // this.vs is 2 after typing so 'writing'
            if (this.vs === 2) {
                o.vs = 'w';
                this.vs = 0;
            } else if (this.idle === 0) {
                o.vs = 'r';
            }

            return o;
        },

        /**
         * Stop ping timers and cleanup any globals.  Shouldn't really
         * be needed by clients.
         */
        dispose: function dispose() {
            this.stopPing();

            for (var id in this.__l) {
                if (this.__l.hasOwnProperty(id)) {
                    _handler[id][this.instanceName] = null;
                }
            }
            this.__l = null;

            // cleanup global
            if (typeof window[this.instanceName] !== 'undefined') {
                try {
                    delete window[this.instanceName];
                } catch (e) {
                    window[this.instanceName] = undefined;
                }
            }
        }
    };

    window.LurnTracker = Tracker;
    window.LurnLoadScript = Lurn.loadScript;

    if (typeof window.exports !== 'undefined') {
        Lurn.Tracker = Tracker;
        window.exports.Lurn = Lurn;

        if (typeof window.lurnLoaded === 'function') {
            window.lurnLoaded();
            window.lurnLoaded = null;
        }
    }

    // Initialize instances & preloaded settings/events
    var _queue = window.__lurn || window._w;
    if (typeof _queue !== 'undefined') {
        for (var name in _queue) {
            if (_queue.hasOwnProperty(name)) {
                var instance = new Tracker(name);
                instance.init();

                // DO NOT REMOVE
                // compatibility with old tracker and chat
                if (typeof window.lurnTracker === 'undefined') {
                    window.lurnTracker = instance;
                }
            }
        }
    }
})(window, document);

/***/ }),

/***/ 89:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(23);


/***/ })

/******/ });