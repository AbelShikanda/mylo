'use client';

import { useState } from 'react';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';
import { LoginAdminRequest } from '@/types/auth';

export default function LoginPage() {
  const [form, setForm] = useState<LoginAdminRequest>({
    email: '',
    password: '',
  });
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      const response = await apiClient.post<{ administrator: unknown; token: string }>(
        '/api/v1/auth/admin/login',
        form
      );
      authStorage.setToken(response.data.token);
      window.location.href = '/overview';
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-charcoal text-ivory-soft flex items-center justify-center px-4">
      <div className="w-full max-w-md space-y-6">
        <div className="text-center">
          <span className="text-champagne font-serif text-4xl font-bold">Mylo</span>
          <h1 className="text-2xl font-display font-bold text-champagne mt-4">
            Administrator Sign in
          </h1>
          <p className="text-rose-muted text-sm mt-2">
            Platform control centre — authorized access only
          </p>
        </div>

        {error && (
          <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
            {error}
          </div>
        )}

        <form
          onSubmit={onSubmit}
          className="space-y-5 mylo-panel p-8"
        >
          <div>
            <label htmlFor="email" className="mylo-label">
              Email
            </label>
            <input
              id="email"
              name="email"
              type="email"
              required
              autoComplete="username"
              className="mylo-input"
              placeholder="admin@mylo.test"
              value={form.email}
              onChange={(e) => setForm({ ...form, email: e.target.value })}
            />
          </div>

          <div>
            <label htmlFor="password" className="mylo-label">
              Password
            </label>
            <input
              id="password"
              name="password"
              type="password"
              required
              autoComplete="current-password"
              className="mylo-input"
              placeholder="••••••••"
              value={form.password}
              onChange={(e) => setForm({ ...form, password: e.target.value })}
            />
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-champagne text-charcoal font-bold rounded hover:bg-ivory-soft disabled:opacity-60 transition-colors"
          >
            {loading ? 'Signing in…' : 'Sign in'}
          </button>
        </form>

        <p className="text-center text-xs text-rose-muted">
          Administrator accounts are created by invitation only.
        </p>
      </div>
    </div>
  );
}
