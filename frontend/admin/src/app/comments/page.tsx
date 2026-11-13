'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Comment {
  id: string;
  content: string;
  created_at: string;
  article?: { title: string };
  reader?: { display_name?: string; email: string };
  writer?: { display_name?: string; email: string };
}

export default function AdminCommentsPage() {
  const [comments, setComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [search, setSearch] = useState('');

  const load = () => {
    const qs = search ? `?search=${encodeURIComponent(search)}` : '';
    apiClient
      .get(`/api/v1/admin/comments${qs}`)
      .then((res) => setComments(unwrapList<Comment>(res)))
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
  }, []);

  const remove = async (id: string) => {
    if (!confirm('Remove this comment?')) return;
    try {
      await apiClient.delete(`/api/v1/admin/comments/${id}`);
      setComments(comments.filter((c) => c.id !== id));
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Comments...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between gap-4 flex-wrap">
        <h1 className="text-3xl font-display font-bold text-champagne">
          Comments and Moderation
        </h1>
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          onKeyDown={(e) => e.key === 'Enter' && load()}
          placeholder="Search comment text…"
          className="mylo-input mylo-input-inline text-sm"
        />
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
              <th className="pb-3">Comment</th>
              <th className="pb-3">Article</th>
              <th className="pb-3">Author</th>
              <th className="pb-3">Date</th>
              <th className="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {comments.map((comment) => (
              <tr key={comment.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors align-top">
                <td className="py-4 text-champagne max-w-md">{comment.content}</td>
                <td className="py-4 text-rose-muted">{comment.article?.title || '—'}</td>
                <td className="py-4 text-rose-muted">
                  {comment.reader?.display_name || comment.reader?.email || comment.writer?.display_name || '—'}
                </td>
                <td className="py-4 text-rose-muted whitespace-nowrap">
                  {new Date(comment.created_at).toLocaleDateString()}
                </td>
                <td className="py-4 text-right">
                  <button
                    onClick={() => remove(comment.id)}
                    className="px-2.5 py-1.5 text-xs font-semibold text-[#F46F6F] hover:bg-red-500/10 border border-red-500/20 rounded-lg transition-colors"
                  >
                    Remove
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {comments.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No comments found.
        </div>
      )}
    </div>
  );
}
