'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface MediaItem {
  id: string;
  url?: string;
  file_path?: string;
  path?: string;
  alt_text?: string;
  mime_type?: string;
  created_at?: string;
}

export default function AdminMediaPage() {
  const [items, setItems] = useState<MediaItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const load = () => {
    apiClient
      .get('/api/v1/admin/media')
      .then((res) => setItems(unwrapList<MediaItem>(res)))
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
  }, []);

  const remove = async (id: string) => {
    if (!confirm('Delete this media item?')) return;
    try {
      await apiClient.delete(`/api/v1/admin/media/${id}`);
      setItems(items.filter((m) => m.id !== id));
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  const mediaUrl = (item: MediaItem) => item.url || item.file_path || item.path || '';

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Media...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <h1 className="text-3xl font-display font-bold text-champagne">
        Media Library
      </h1>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      {items.length === 0 ? (
        <div className="text-center py-12 text-rose-muted">
          No media assets found.
        </div>
      ) : (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {items.map((item) => (
            <div
              key={item.id}
              className="mylo-card overflow-hidden"
            >
              {mediaUrl(item) ? (
                <img
                  src={mediaUrl(item)}
                  alt={item.alt_text || 'Media asset'}
                  className="w-full h-32 object-cover"
                />
              ) : (
                <div className="w-full h-32 bg-charcoal/50 flex items-center justify-center text-rose-muted text-xs">
                  File
                </div>
              )}
              <div className="p-3">
                <p className="text-xs text-rose-muted truncate">
                  {item.alt_text || item.mime_type || 'Untitled'}
                </p>
                <button
                  onClick={() => remove(item.id)}
                  className="mt-2 text-xs text-red-400 hover:underline"
                >
                  Delete
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
