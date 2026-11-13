'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Order {
  id: string;
  status: string;
  total?: string;
  total_amount?: string;
  created_at: string;
  reader?: { display_name?: string; email: string };
}

export default function AdminOrdersPage() {
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [status, setStatus] = useState('');

  const load = () => {
    const qs = status ? `?status=${encodeURIComponent(status)}` : '';
    apiClient
      .get(`/api/v1/admin/orders${qs}`)
      .then((res) => setOrders(unwrapList<Order>(res)))
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

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Orders...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between gap-4 flex-wrap">
        <h1 className="text-3xl font-display font-bold text-champagne">
          Orders
        </h1>
        <select
          value={status}
          onChange={(e) => setStatus(e.target.value)}
          className="mylo-input mylo-input-inline text-sm"
        >
          <option value="">All statuses</option>
          <option value="pending">Pending</option>
          <option value="paid">Paid</option>
          <option value="completed">Completed</option>
          <option value="failed">Failed</option>
          <option value="refunded">Refunded</option>
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
              <th className="pb-3">Order</th>
              <th className="pb-3">Reader</th>
              <th className="pb-3">Total</th>
              <th className="pb-3">Status</th>
              <th className="pb-3">Date</th>
            </tr>
          </thead>
          <tbody>
            {orders.map((order) => (
              <tr key={order.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne font-mono text-xs">{order.id.slice(0, 8)}…</td>
                <td className="py-4 text-rose-muted">
                  {order.reader?.display_name || order.reader?.email || '—'}
                </td>
                <td className="py-4 text-rose-muted">
                  {order.total ?? order.total_amount ?? '—'}
                </td>
                <td className="py-4">
                  <span className="px-2 py-1 text-xs text-champagne bg-burgundy-deep/20 rounded">
                    {order.status}
                  </span>
                </td>
                <td className="py-4 text-rose-muted">
                  {new Date(order.created_at).toLocaleDateString()}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {orders.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No orders found.
        </div>
      )}
    </div>
  );
}
