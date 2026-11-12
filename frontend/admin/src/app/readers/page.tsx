'use client';

import { useEffect, useState } from 'react';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';
import { Reader } from '@/types/auth';

export default function ReadersPage() {
  const [readers, setReaders] = useState<Reader[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<Reader[]>('/api/v1/readers')
      .then((res) => {
        setReaders(res.data);
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, []);

  const handleSuspend = async (id: string) => {
    if (!confirm('Suspend this reader?')) return;
    try {
      await apiClient.post(`/api/v1/users/${id}/suspend`);
      setReaders(readers.map((r) => (r.id === id ? { ...r } : r)));
      window.location.reload();
    } catch (err: any) {
      setError(err.message);
    }
  };

  const handleReinstate = async (id: string) => {
    if (!confirm('Reinstate this reader?')) return;
    try {
      await apiClient.post(`/api/v1/users/${id}/reinstate`);
      window.location.reload();
    } catch (err: any) {
      setError(err.message);
    }
  };

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-serif font-bold text-champagne mb-6">
          Loading Readers...
        </h1>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-serif font-bold text-champagne mb-6">
          Error
        </h1>
        <p className="text-rose-muted">{error}</p>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-serif font-bold text-champagne mb-2">
          Reader Management
        </h1>
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b border-burgundy-wine/40 text-rose-muted text-sm uppercase">
              <th className="pb-3">Email</th>
              <th className="pb-3">Display Name</th>
              <th className="pb-3">Status</th>
              <th className="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {readers.map((reader) => (
              <tr key={reader.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne">{reader.email}</td>
                <td className="py-4 text-rose-muted">
                  {reader.display_name || '—'}
                </td>
                <td className="py-4">
                  <span className="px-2 py-1 text-xs text-burgundy-wine bg-burgundy-deep/20 rounded">
                    Active
                  </span>
                </td>
                <td className="py-4 text-right space-x-2">
                  <button
                    onClick={() => handleSuspend(reader.id)}
                    className="px-3 py-1 text-xs text-red-400 hover:bg-red-500/20 rounded transition-colors"
                  >
                    Suspend
                  </button>
                  <button
                    onClick={() => handleReinstate(reader.id)}
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

      {readers.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No readers found.
        </div>
      )}
    </div>
  );
}
