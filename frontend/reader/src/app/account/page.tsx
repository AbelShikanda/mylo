'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface ReaderProfile {
  id: string;
  email: string;
  display_name?: string;
  email_verified_at?: string | null;
}

export default function AccountPage() {
  const [profile, setProfile] = useState<ReaderProfile | null>(null);
  const [displayName, setDisplayName] = useState('');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [notice, setNotice] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/reader/profile')
      .then((res) => {
        const d = unwrapData<ReaderProfile>(res);
        setProfile(d);
        setDisplayName(d?.display_name ?? '');
        setLoading(false);
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  const save = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setError(null);
    setNotice(null);
    try {
      await apiClient.put('/api/v1/reader/profile', { display_name: displayName });
      setNotice('Account updated.');
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to update account');
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Account…
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-2xl mx-auto py-12 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          Account
        </h1>
        <p className="text-charcoal/70">
          Manage your reader profile.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}
      {notice && (
        <div className="p-3 bg-green-50 border border-green-200 text-green-700 rounded text-sm">
          {notice}
        </div>
      )}

      <form onSubmit={save} className="mylo-card p-6 space-y-5">
        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">
            Email
          </label>
          <input
            type="email"
            value={profile?.email ?? ''}
            disabled
            className="w-full px-3 py-2 border border-burgundy-deep/20 rounded-md bg-ivory-soft text-charcoal/60"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">
            Display name
          </label>
          <input
            type="text"
            value={displayName}
            onChange={(e) => setDisplayName(e.target.value)}
            className="mylo-input"
            placeholder="Your display name"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-charcoal mb-1">
            Email status
          </label>
          <p className="text-sm text-charcoal/70">
            {profile?.email_verified_at ? 'Verified' : 'Not verified'}
          </p>
        </div>
        <button
          type="submit"
          disabled={saving}
          className="px-6 py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine disabled:opacity-60 transition-colors"
        >
          {saving ? 'Saving…' : 'Save changes'}
        </button>
      </form>
    </div>
  );
}
