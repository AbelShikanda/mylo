'use client';

import { useEffect, useState } from 'react';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';

export default function ConfigPage() {
  const [config, setConfig] = useState<Record<string, string>>({});
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<Record<string, string>>('/api/v1/config')
      .then((res) => {
        setConfig(res.data);
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, []);

  const handleUpdate = async (key: string, value: string) => {
    try {
      await apiClient.put(`/api/v1/config/${key}`, { value });
      window.location.reload();
    } catch (err: any) {
      setError(err.message);
    }
  };

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-serif font-bold text-champagne mb-6">
          Loading Configuration...
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
          Platform Configuration
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
              <th className="pb-3">Key</th>
              <th className="pb-3">Value</th>
              <th className="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {Object.entries(config).map(([key, value]) => (
              <tr key={key} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne">{key}</td>
                <td className="py-4">
                  <input
                    type="text"
                    value={value}
                    onChange={(e) =>
                      handleUpdate(key, e.target.value)
                    }
                    className="w-full px-3 py-2 border border-burgundy-wine/30 rounded bg-charcoal/50 text-champagne focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
                  />
                </td>
                <td className="py-4 text-right">
                  <span className="text-xs text-rose-muted">Updated</span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {Object.keys(config).length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No configuration found.
        </div>
      )}
    </div>
  );
}
