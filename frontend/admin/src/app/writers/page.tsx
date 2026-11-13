'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';
import { Writer } from '@/types/auth';

export default function WritersPage() {
  const [writers, setWriters] = useState<Writer[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<Writer[]>('/api/v1/admin/writers')
      .then((res) => {
        setWriters(unwrapList<Writer>(res));
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, []);

  const handleSuspend = async (id: string) => {
    if (!confirm('Suspend this writer?')) return;
    try {
      await apiClient.put(`/api/v1/admin/writers/${id}/suspend`);
      window.location.reload();
    } catch (err: any) {
      setError(err.message);
    }
  };

  const handleReinstate = async (id: string) => {
    if (!confirm('Reinstate this writer?')) return;
    try {
      await apiClient.put(`/api/v1/admin/writers/${id}/reinstate`);
      window.location.reload();
    } catch (err: any) {
      setError(err.message);
    }
  };

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Writers...
        </h1>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Error
        </h1>
        <p className="text-rose-muted">{error}</p>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-display font-bold text-champagne mb-2">
          Writer Management
        </h1>
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
              <th className="pb-3">Email</th>
              <th className="pb-3">Display Name</th>
              <th className="pb-3">Status</th>
              <th className="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {writers.map((writer) => (
              <tr key={writer.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne">{writer.email}</td>
                <td className="py-4 text-rose-muted">
                  {writer.display_name || '—'}
                </td>
                <td className="py-4">
                  <span className="px-2 py-1 text-xs text-burgundy-wine bg-burgundy-deep/20 rounded">
                    Active
                  </span>
                </td>
                <td className="py-4 text-right space-x-2">
                  <button
                    onClick={() => handleSuspend(writer.id)}
                    className="px-2.5 py-1.5 text-xs font-semibold text-[#F46F6F] hover:bg-red-500/10 border border-red-500/20 rounded-lg transition-colors"
                  >
                    Suspend
                  </button>
                  <button
                    onClick={() => handleReinstate(writer.id)}
                    className="px-3 py-1 text-xs text-burgundy-wine hover:bg-burgundy-deep/20 rounded transition-colors"
                  >
                    Reinstate
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {writers.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No writers found.
        </div>
      )}
    </div>
  );
}
