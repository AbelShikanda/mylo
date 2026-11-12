'use client';

import { useEffect, useState } from 'react';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';
import { Permission } from '@/types/auth';

export default function PermissionsPage() {
  const [permissions, setPermissions] = useState<Permission[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<Permission[]>('/api/v1/permissions')
      .then((res) => {
        setPermissions(res.data);
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
        <h1 className="text-3xl font-serif font-bold text-champagne mb-6">
          Loading Permissions...
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
          Permissions
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
              <th className="pb-3">Permission Name</th>
              <th className="pb-3">Slug</th>
              <th className="pb-3">Description</th>
            </tr>
          </thead>
          <tbody>
            {permissions.map((perm) => (
              <tr key={perm.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne">{perm.name}</td>
                <td className="py-4 text-rose-muted">{perm.slug}</td>
                <td className="py-4 text-rose-muted">
                  {perm.description || '—'}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {permissions.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No permissions found.
        </div>
      )}
    </div>
  );
}
