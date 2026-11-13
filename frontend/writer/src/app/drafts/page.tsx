'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Draft {
  id: string;
  title: string;
  updated_at?: string;
  created_at: string;
  category?: { name: string };
}

export default function DraftsPage() {
  const [drafts, setDrafts] = useState<Draft[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/writer/drafts')
      .then((res) => setDrafts(unwrapList<Draft>(res)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="max-w-3xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Drafts…
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-3xl mx-auto py-12 px-4 space-y-8">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
            Drafts
          </h1>
          <p className="text-charcoal/70">
            Unfinished articles waiting for your attention.
          </p>
        </div>
        <Link
          href="/create"
          className="mylo-btn"
        >
          New Article
        </Link>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {drafts.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No drafts yet.</p>
          <Link href="/create" className="text-burgundy-wine hover:underline mt-4 inline-block">
            Start your first article →
          </Link>
        </div>
      ) : (
        <div className="mylo-card divide-y divide-burgundy-deep/10">
          {drafts.map((draft) => (
            <div key={draft.id} className="flex items-center justify-between px-5 py-4">
              <div className="min-w-0">
                <p className="font-medium text-charcoal truncate">
                  {draft.title || 'Untitled draft'}
                </p>
                <p className="text-xs text-charcoal/50 mt-1">
                  {draft.category?.name ? `${draft.category.name} • ` : ''}
                  Last edited {new Date(draft.updated_at || draft.created_at).toLocaleDateString()}
                </p>
              </div>
              <Link
                href="/create"
                className="text-sm text-burgundy-wine hover:underline ml-4 shrink-0"
              >
                Continue →
              </Link>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
