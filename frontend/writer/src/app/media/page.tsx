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
  size?: number;
  created_at?: string;
}

export default function MediaPage() {
  const [items, setItems] = useState<MediaItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const load = () => {
    apiClient
      .get('/api/v1/writer/media')
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

  const upload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    setUploading(true);
    setError(null);
    try {
      const token = authStorage.getToken();
      const form = new FormData();
      form.append('file', file);
      const res = await fetch(
        `${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000'}/api/v1/writer/media`,
        {
          method: 'POST',
          headers: { Authorization: `Bearer ${token}` },
          body: form,
        }
      );
      if (!res.ok) {
        throw new Error(`Upload failed (${res.status})`);
      }
      e.target.value = '';
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Upload failed');
    } finally {
      setUploading(false);
    }
  };

  const remove = async (id: string) => {
    if (!confirm('Delete this media item?')) return;
    try {
      await apiClient.delete(`/api/v1/writer/media/${id}`);
      setItems(items.filter((m) => m.id !== id));
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  const mediaUrl = (item: MediaItem) =>
    item.url || item.file_path || item.path || '';

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Media Library…
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto py-12 px-4 space-y-8">
      <div className="flex items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
            Media Library
          </h1>
          <p className="text-charcoal/70">
            Images and files for your articles.
          </p>
        </div>
        <label className="mylo-btn cursor-pointer">
          {uploading ? 'Uploading…' : 'Upload'}
          <input type="file" accept="image/*,application/pdf" onChange={upload} className="hidden" />
        </label>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {items.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No media uploaded yet.</p>
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
                <div className="w-full h-32 bg-ivory-soft flex items-center justify-center text-charcoal/40 text-xs">
                  File
                </div>
              )}
              <div className="p-3">
                <p className="text-xs text-charcoal/60 truncate">
                  {item.alt_text || item.mime_type || 'Untitled'}
                </p>
                <button
                  onClick={() => remove(item.id)}
                  className="mt-2 text-xs text-red-600 hover:underline"
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
