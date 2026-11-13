'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapData, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Earnings {
  contributions_received: string;
  book_sales: string;
  platform_deductions: string;
  available_balance: string;
  pending_balance: string;
  withdrawn_amount: string;
}

interface Payout {
  id: string;
  amount: string;
  status: string;
  processed_at?: string;
  created_at: string;
}

export default function EarningsPage() {
  const [earnings, setEarnings] = useState<Earnings | null>(null);
  const [payouts, setPayouts] = useState<Payout[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [requestAmount, setRequestAmount] = useState('');

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<{ data: Earnings }>('/api/v1/writer/earnings')
      .then((res) => {
        setEarnings(unwrapData<Earnings>(res) ?? ({} as Earnings));
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));

    apiClient
      .get<{ data: Payout[] }>('/api/v1/writer/earnings/payouts')
      .then((res) => {
        setPayouts(unwrapList<Payout>(res));
      })
      .catch((err) => setError(err.message));
  }, []);

  const requestPayout = async (e: React.FormEvent) => {
    e.preventDefault();
    const token = authStorage.getToken();
    try {
      await apiClient.post(
        '/api/v1/writer/earnings/payout-request',
        { amount: parseFloat(requestAmount) },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      window.location.reload();
    } catch (err: any) {
      setError(err.message || 'Failed to request payout');
    }
  };

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Earnings…
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
          Earnings
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

      <div className="grid md:grid-cols-3 gap-6">
        <div className="mylo-card p-6">
          <p className="text-xs text-charcoal/60 uppercase tracking-wider mb-2">
            Contributions Received
          </p>
          <p className="text-2xl font-bold text-burgundy-deep">
            ${earnings?.contributions_received || '0.00'}
          </p>
        </div>
        <div className="mylo-card p-6">
          <p className="text-xs text-charcoal/60 uppercase tracking-wider mb-2">
            Available Balance
          </p>
          <p className="text-2xl font-bold text-burgundy-deep">
            ${earnings?.available_balance || '0.00'}
          </p>
        </div>
        <div className="mylo-card p-6">
          <p className="text-xs text-charcoal/60 uppercase tracking-wider mb-2">
            Pending Balance
          </p>
          <p className="text-2xl font-bold text-burgundy-deep">
            ${earnings?.pending_balance || '0.00'}
          </p>
        </div>
        <div className="mylo-card p-6">
          <p className="text-xs text-charcoal/60 uppercase tracking-wider mb-2">
            Withdrawn
          </p>
          <p className="text-2xl font-bold text-burgundy-deep">
            ${earnings?.withdrawn_amount || '0.00'}
          </p>
        </div>
      </div>

      <div className="mylo-card p-6">
        <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-4">
          Request Payout
        </h3>
        <form onSubmit={requestPayout} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-charcoal mb-1">
              Amount (KES)
            </label>
            <input
              name="amount"
              type="number"
              step="0.01"
              min="1"
              value={requestAmount}
              onChange={(e) => setRequestAmount(e.target.value)}
              className="mylo-input"
              placeholder="50.00"
              required
            />
          </div>
          <button
            type="submit"
            className="w-full py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors"
          >
            Request Payout
          </button>
        </form>
      </div>

      <div className="mylo-card p-6">
        <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-4">
          Payout History
        </h3>
        {payouts.length === 0 ? (
          <p className="text-charcoal/60 text-sm">No payouts yet.</p>
        ) : (
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-burgundy-deep/20 text-charcoal/60 text-sm uppercase">
                <th className="pb-3">Amount</th>
                <th className="pb-3">Status</th>
                <th className="pb-3">Date</th>
              </tr>
            </thead>
            <tbody>
              {payouts.map((p) => (
                <tr key={p.id} className="border-b border-burgundy-deep/10">
                  <td className="py-3 text-charcoal">${p.amount}</td>
                  <td className="py-3">
                    <span className={`px-2 py-1 rounded text-xs font-medium ${
                      p.status === 'pending'
                        ? 'bg-ivory-soft text-charcoal/70'
                        : p.status === 'approved'
                          ? 'bg-green-100 text-green-700'
                          : 'bg-red-100 text-red-700'
                    }`}>
                      {p.status}
                    </span>
                  </td>
                  <td className="py-3 text-charcoal/70 text-sm">
                    {new Date(p.created_at).toLocaleDateString()}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
