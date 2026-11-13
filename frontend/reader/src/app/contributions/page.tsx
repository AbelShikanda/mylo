'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Contribution {
  id: string;
  amount: string;
  currency: string;
  type: string;
  status: string;
  created_at: string;
  writer: { display_name?: string; email: string };
}

export default function ContributionsPage() {
  const [contributions, setContributions] = useState<Contribution[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<{ data: Contribution[] }>('/api/v1/reader/contributions')
      .then((res) => {
        setContributions(unwrapList<Contribution>(res));
        setLoading(false);
      })
      .catch((err) => setError(err.message));
  }, []);

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Contributions…
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
          Contribution History
        </h1>
        <Link href="/" className="text-burgundy-wine hover:underline text-sm">
          ← Back to homepage
        </Link>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {contributions.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No contributions yet.</p>
          <Link
            href="/"
            className="mt-4 inline-block mylo-btn"
          >
            Browse writers
          </Link>
        </div>
      ) : (
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b border-burgundy-deep/20 text-charcoal/60 text-sm uppercase">
              <th className="pb-3">Writer</th>
              <th className="pb-3">Amount</th>
              <th className="pb-3">Type</th>
              <th className="pb-3">Status</th>
              <th className="pb-3">Date</th>
            </tr>
          </thead>
            <tbody>
              {contributions.map((c) => (
                <tr key={c.id} className="border-b border-burgundy-deep/10">
                  <td className="py-4 text-charcoal">{c.writer.display_name || c.writer.email}</td>
                  <td className="py-4 text-charcoal">${c.amount}</td>
                  <td className="py-4 text-charcoal/70 text-sm">{c.type}</td>
                  <td className="py-4">
                    <span className={`px-2 py-1 rounded text-xs font-medium ${
                      c.status === 'completed'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-ivory-soft text-charcoal/70'
                    }`}>
                      {c.status}
                    </span>
                  </td>
                  <td className="py-4 text-charcoal/70 text-sm">
                    {new Date(c.created_at).toLocaleDateString()}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
    </div>
  );
}
