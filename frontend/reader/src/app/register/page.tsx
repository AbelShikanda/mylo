'use client';

import Link from 'next/link';
import { useState } from 'react';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';
import { RegisterReaderRequest } from '@/types/auth';

export default function RegisterPage() {
  const [form, setForm] = useState<RegisterReaderRequest>({
    email: '',
    password: '',
    password_confirmation: '',
    display_name: '',
  });
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      const response = await apiClient.post<{ reader: any; token: string }>(
        '/api/v1/auth/reader/register',
        form
      );
      authStorage.setToken(response.data.token);
      window.location.href = '/';
    } catch (err: any) {
      setError(err.message || 'Registration failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-md mx-auto py-12 px-4">
      <div className="text-center mb-8">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">Create Reader Account</h1>
        <p className="text-charcoal/70">Join Mylo to discover and enjoy independent publishing.</p>
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
            placeholder="you@example.com"
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

        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">Confirm Password</label>
          <input
            name="password_confirmation"
            type="password"
            className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            placeholder="••••••••"
            value={form.password_confirmation}
            onChange={(e) => setForm({ ...form, password_confirmation: e.target.value })}
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">Display Name (optional)</label>
          <input
            name="display_name"
            type="text"
            className="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            placeholder="Your display name"
            value={form.display_name}
            onChange={(e) => setForm({ ...form, display_name: e.target.value })}
          />
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine disabled:opacity-60 transition-colors"
        >
          {loading ? 'Creating account...' : 'Create account'}
        </button>
      </form>

      <p className="mt-6 text-center text-sm text-charcoal/70">
        Already have an account?{' '}
        <Link href="/login" className="text-burgundy-wine font-medium hover:underline">
          Sign in
        </Link>
      </p>
    </div>
  );
}
