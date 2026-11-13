'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { apiClient, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface WriterProfile {
  id: string;
  display_name?: string;
  support_active: boolean;
}

export default function SupportPage() {
  const [profile, setProfile] = useState<WriterProfile | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [supportActive, setSupportActive] = useState(false);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<{ data: WriterProfile }>('/api/v1/writer/profile')
      .then((res) => {
        const d = unwrapData<WriterProfile>(res);
        setProfile(d);
        setSupportActive(!!d?.support_active);
        setLoading(false);
      })
      .catch((err) => setError(err.message));
  }, []);

  const toggleSupport = async (e: React.FormEvent) => {
    e.preventDefault();
    const token = authStorage.getToken();
    try {
      const endpoint = supportActive
        ? '/api/v1/writer/support/deactivate'
        : '/api/v1/writer/support/activate';
      await apiClient.post(endpoint, {}, { headers: { Authorization: `Bearer ${token}` } });
      setSupportActive(!supportActive);
    } catch (err: any) {
      setError(err.message || 'Failed to update support status');
    }
  };

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Support Settings…
        </h1>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Error
        </h1>
        <p className="text-charcoal/70 text-center">{error}</p>
      </div>
    );
  }

  return (
    <div className="max-w-2xl mx-auto py-12 px-4 space-y-8">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          Financial Support
        </h1>
        <Link href="/dashboard" className="text-burgundy-wine hover:underline text-sm">
          ← Back to dashboard
        </Link>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      <div className="mylo-card p-6">
        <h2 className="font-serif font-bold text-burgundy-deep text-lg mb-4">
          Accept Financial Support
        </h2>
        <p className="text-charcoal/70 mb-4">
          Enable financial support to allow readers to contribute to your work.
          Readers can make one-time or recurring contributions without following you.
        </p>

        <form onSubmit={toggleSupport} className="space-y-4">
          <div className="flex items-center justify-between">
            <span className="text-charcoal">
              {supportActive ? 'Enabled' : 'Disabled'}
            </span>
            <button
              type="submit"
              className={`px-4 py-2 rounded-md text-sm font-medium transition-colors ${
                supportActive
                  ? 'bg-red-100 text-red-700 hover:bg-red-200'
                  : 'bg-burgundy-deep text-white hover:bg-burgundy-wine'
              }`}
            >
              {supportActive ? 'Deactivate' : 'Activate'}
            </button>
          </div>
        </form>

        <div className="bg-ivory-soft p-4 rounded-lg border border-burgundy-deep/20 mt-4">
          <h3 className="font-serif font-bold text-burgundy-deep text-sm mb-2">
            How it works
          </h3>
          <ul className="text-sm text-charcoal/70 space-y-2 list-disc list-inside">
            <li>Readers can make one-time or recurring contributions</li>
            <li>Contributions are processed through M-Pesa or international cards</li>
            <li>Platform commission is automatically deducted</li>
            <li>You can request payouts from your available balance</li>
          </ul>
        </div>
      </div>
    </div>
  );
}
