'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';

export default function FinancePage() {
  const [overview, setOverview] = useState<Record<string, unknown> | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/admin/finance/overview')
      .then((res) => {
        setOverview(unwrapData<Record<string, unknown>>(res) ?? {});
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, []);

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Finance...
        </h1>
      </div>
    );
  }

  const entries = overview ? Object.entries(overview) : [];

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div>
        <h1 className="text-3xl font-display font-bold text-champagne mb-2">
          Financial Management
        </h1>
        <p className="text-rose-muted">
          Platform revenue, payouts, refunds and transactions.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      {entries.length > 0 ? (
        <div className="grid md:grid-cols-3 gap-4">
          {entries.map(([key, value]) => (
            <div key={key} className="mylo-panel p-5">
              <p className="text-xs uppercase text-rose-muted tracking-wide">
                {key.replace(/_/g, ' ')}
              </p>
              <p className="text-2xl font-bold text-champagne mt-2">
                {typeof value === 'number' ? value.toLocaleString() : String(value ?? '—')}
              </p>
            </div>
          ))}
        </div>
      ) : (
        !error && (
          <div className="text-center py-12 text-rose-muted">
            No financial data available yet.
          </div>
        )
      )}
    </div>
  );
}
