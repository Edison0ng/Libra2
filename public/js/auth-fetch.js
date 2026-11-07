(function () {
  var PUBLIC = ['/api/login', '/api/register', '/api/forgot-password', '/api/reset-password', '/api/test'];
  var orig = window.fetch.bind(window);
  window.fetch = function (input, init) {
    try {
      var url = typeof input === 'string' ? input : (input && input.url) || '';
      var path = url.replace(/^https?:\/\/[^/]+/, '').split('?')[0];
      if (path.indexOf('/api/') === 0 && PUBLIC.indexOf(path) === -1) {
        init = init || {};
        var h = new Headers(init.headers || (typeof input !== 'string' && input.headers) || {});
        var t = localStorage.getItem('libra_token');
        if (t && !h.has('Authorization')) h.set('Authorization', 'Bearer ' + t);
        if (!h.has('Accept')) h.set('Accept', 'application/json');
        init.headers = h;
        return orig(input, init).then(function (res) {
          if (res.status === 401) {
            ['libra_token', 'libra_user', 'libra_remember'].forEach(function (k) { try { localStorage.removeItem(k); } catch (e) {} });
            try { sessionStorage.removeItem('libra_active'); } catch (e) {}
            if (location.pathname !== '/login') location.href = '/login';
          }
          return res;
        });
      }
    } catch (e) {}
    return orig(input, init);
  };
})();