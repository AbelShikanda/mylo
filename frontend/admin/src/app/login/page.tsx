'use client';

import Link from 'next/link';
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
      const response = await apiClient.post<{ administrator: any; token: string }>(
        '/api/v1/auth/admin/login',
        form
      );
      authStorage.setToken(response.data.token);
      window.location.href = '/overview';
    } catch (err: any) {
      setError(err.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-md mx-auto py-12 px-4">
      <div className="text-center mb-8">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">Administrator Sign in</h1>
        <p className="text-charcoal/70">Platform control centre — authorized access only</p>
      </div>

      {error && (
        <div className="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      <form onSubmit={onSubmit} className="space-y-4 bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">Email</label>
          <input
            name="email"
            type="email"
            className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            placeholder="admin@example.com"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">Password</label>
          <input
            name="password"
            type="password"
            className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            placeholder="••••••••"
            value={form.password}
            onChange={(e) => setForm({ ...form, password: e.target.value })}
          />
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine disabled:opacity-60 transition-colors"
        >
          {loading ? 'Signing in...' : 'Sign in'}
        </button>
      </form>

      <p className="mt-6 text-center text-xs text-charcoal/50">
        Administrator accounts are created by invitation only.
      </p>
    </div>
  );
}
