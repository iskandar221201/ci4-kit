/**
 * auth.js — Token management for CI4 Kit Views Layer
 * Token key: _ck_token (distinct from _fk_token to avoid conflicts)
 */
const auth = {
  TOKEN_KEY: '_ck_token',

  getToken() {
    return localStorage.getItem(this.TOKEN_KEY)
  },

  setToken(token) {
    localStorage.setItem(this.TOKEN_KEY, token)
  },

  clearToken() {
    localStorage.removeItem(this.TOKEN_KEY)
  },

  isAuthenticated() {
    return !!this.getToken()
  },

  logout() {
    this.clearToken()
    window.location.href = '/login'
  },
}
