'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Transaction {
  id: string;
  type?: string;
  status?: string;
  amount?: string | number;
  currency?: string;
  reference?: string;
  created_at: string;
  writer?: { display_name?: string; email: string };
}

export default function TransactionsPage() {
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [status, setStatus] = useState('');
  const [type, setType] = useState('');

  const load = () => {
    const params = new URLSearchParams();
    if (status) params.set('status', status);
    if (type) params.set('type', type);
    const qs = params.toString();
    apiClient
      .get(`/api/v1/admin/finance/transactions${qs ? `?${qs}` : ''}`)
      .then((res) => setTransactions(unwrapList<Transaction>(res)))
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
  }, [status, type]);

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Transactions...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between gap-4 flex-wrap">
        <h1 className="text-3xl font-display font-bold text-champagne">
          Transactions
        </h1>
        <div className="flex gap-2">
          <select
            value={status}
            onChange={(e) => setStatus(e.target.value)}
            className="mylo-input mylo-input-inline text-sm"
          >
            <option value="">All statuses</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed</option>
            <option value="reversed">Reversed</option>
          </select>
          <select
            value={type}
            onChange={(e) => setType(e.target.value)}
            className="mylo-input mylo-input-inline text-sm"
          >
            <option value="">All types</option>
            <option value="contribution">Contribution</option>
            <option value="book_sale">Book sale</option>
            <option value="payout">Payout</option>
            <option value="refund">Refund</option>
          </select>
        </div>
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
              <th className="pb-3">Reference</th>
              <th className="pb-3">Type</th>
              <th className="pb-3">Writer</th>
              <th className="pb-3">Amount</th>
              <th className="pb-3">Status</th>
              <th className="pb-3">Date</th>
            </tr>
          </thead>
          <tbody>
            {transactions.map((t) => (
              <tr key={t.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne font-mono text-xs">
                  {t.reference || `${t.id.slice(0, 8)}…`}
                </td>
                <td className="py-4 text-rose-muted">{t.type || '—'}</td>
                <td className="py-4 text-rose-muted">
                  {t.writer?.display_name || t.writer?.email || '—'}
                </td>
                <td className="py-4 text-champagne">
                  {t.currency ? `${t.currency} ` : ''}{t.amount ?? '—'}
                </td>
                <td className="py-4">
                  <span className="px-2 py-1 text-xs text-champagne bg-burgundy-deep/20 rounded">
                    {t.status || '—'}
                  </span>
                </td>
                <td className="py-4 text-rose-muted">
                  {new Date(t.created_at).toLocaleDateString()}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {transactions.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No transactions found.
        </div>
      )}
    </div>
  );
}
