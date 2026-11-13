'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Payout {
  id: string;
  amount: string | number;
  currency?: string;
  status: string;
  method?: string;
  reference?: string;
  created_at: string;
  writer?: { display_name?: string; email: string };
}

export default function PayoutsPage() {
  const [payouts, setPayouts] = useState<Payout[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [status, setStatus] = useState('');

  const load = () => {
    const qs = status ? `?status=${encodeURIComponent(status)}` : '';
    apiClient
      .get(`/api/v1/admin/finance/payouts${qs}`)
      .then((res) => setPayouts(unwrapList<Payout>(res)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [status]);

  const act = async (id: string, action: 'approve' | 'reject') => {
    if (!confirm(`${action === 'approve' ? 'Approve' : 'Reject'} this payout request?`)) return;
    try {
      await apiClient.post(`/api/v1/admin/finance/payouts/${id}/${action}`, {});
      setPayouts(
        payouts.map((p) =>
          p.id === id ? { ...p, status: action === 'approve' ? 'approved' : 'rejected' } : p
        )
      );
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Payouts...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between gap-4 flex-wrap">
        <h1 className="text-3xl font-display font-bold text-champagne">
          Payouts
        </h1>
        <select
          value={status}
          onChange={(e) => setStatus(e.target.value)}
          className="mylo-input mylo-input-inline text-sm"
        >
          <option value="">All statuses</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="paid">Paid</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      <div className="overflow-x-auto">
        <table className="mylo-table">
          <thead>
            <tr className="border-b border-burgundy-wine/40 text-rose-muted text-sm uppercase">
              <th className="pb-3">Writer</th>
              <th className="pb-3">Amount</th>
              <th className="pb-3">Method</th>
              <th className="pb-3">Status</th>
              <th className="pb-3">Requested</th>
              <th className="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {payouts.map((payout) => (
              <tr key={payout.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne">
                  {payout.writer?.display_name || payout.writer?.email || '—'}
                </td>
                <td className="py-4 text-champagne">
                  {payout.currency || 'KES'} {payout.amount}
                </td>
                <td className="py-4 text-rose-muted">{payout.method || '—'}</td>
                <td className="py-4">
                  <span className="px-2 py-1 text-xs text-champagne bg-burgundy-deep/20 rounded">
                    {payout.status}
                  </span>
                </td>
                <td className="py-4 text-rose-muted">
                  {new Date(payout.created_at).toLocaleDateString()}
                </td>
                <td className="py-4 text-right space-x-2 whitespace-nowrap">
                  {payout.status === 'pending' && (
                    <>
                      <button
                        onClick={() => act(payout.id, 'approve')}
                        className="px-2.5 py-1.5 text-xs font-semibold text-[#58D89B] hover:bg-emerald-500/10 border border-emerald-500/20 rounded-lg transition-colors"
                      >
                        Approve
                      </button>
                      <button
                        onClick={() => act(payout.id, 'reject')}
                        className="px-2.5 py-1.5 text-xs font-semibold text-[#F46F6F] hover:bg-red-500/10 border border-red-500/20 rounded-lg transition-colors"
                      >
                        Reject
                      </button>
                    </>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {payouts.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No payout requests found.
        </div>
      )}
    </div>
  );
}
