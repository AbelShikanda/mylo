import { apiClient } from './api';

const TOKEN_KEY = 'mylo_reader_token';

export const authStorage = {
  getToken(): string | null {
    if (typeof window === 'undefined') return null;
    return localStorage.getItem(TOKEN_KEY);
  },
  setToken(token: string) {
    if (typeof window === 'undefined') return;
    localStorage.setItem(TOKEN_KEY, token);
    apiClient.setToken(token);
  },
  removeToken() {
    if (typeof window === 'undefined') return;
    localStorage.removeItem(TOKEN_KEY);
    apiClient.setToken(null);
  },
};

export function initializeAuth() {
  const token = authStorage.getToken();
  if (token) {
    apiClient.setToken(token);
  }
}
