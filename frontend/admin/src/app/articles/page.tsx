'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Article {
  id: string;
  title: string;
  slug: string;
  status: string;
  is_featured?: boolean;
  created_at: string;
  writer?: { display_name?: string; email: string };
  category?: { name: string };
}

export default function AdminArticlesPage() {
  const [articles, setArticles] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('');

  const load = () => {
    const params = new URLSearchParams();
    if (status) params.set('status', status);
    if (search) params.set('search', search);
    const qs = params.toString();
    apiClient
      .get(`/api/v1/admin/articles${qs ? `?${qs}` : ''}`)
      .then((res) => setArticles(unwrapList<Article>(res)))
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

  const toggleFeature = async (article: Article) => {
    try {
      await apiClient.put(
        `/api/v1/admin/articles/${article.id}/${article.is_featured ? 'unfeature' : 'feature'}`
      );
      setArticles(
        articles.map((a) =>
          a.id === article.id ? { ...a, is_featured: !a.is_featured } : a
        )
      );
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  const remove = async (id: string) => {
    if (!confirm('Remove this article permanently?')) return;
    try {
      await apiClient.delete(`/api/v1/admin/articles/${id}`);
      setArticles(articles.filter((a) => a.id !== id));
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Articles...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between gap-4 flex-wrap">
        <h1 className="text-3xl font-display font-bold text-champagne">
          Article Management
        </h1>
        <div className="flex gap-2">
          <input
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && load()}
            placeholder="Search title…"
            className="mylo-input mylo-input-inline text-sm"
          />
          <select
            value={status}
            onChange={(e) => setStatus(e.target.value)}
            className="mylo-input mylo-input-inline text-sm"
          >
            <option value="">All statuses</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="scheduled">Scheduled</option>
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
              <th className="pb-3">Title</th>
              <th className="pb-3">Writer</th>
              <th className="pb-3">Category</th>
              <th className="pb-3">Status</th>
              <th className="pb-3">Featured</th>
              <th className="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {articles.map((article) => (
              <tr
                key={article.id}
                className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors"
              >
                <td className="py-4 text-champagne max-w-xs truncate">{article.title}</td>
                <td className="py-4 text-rose-muted">
                  {article.writer?.display_name || article.writer?.email || '—'}
                </td>
                <td className="py-4 text-rose-muted">{article.category?.name || '—'}</td>
                <td className="py-4">
                  <span className="px-2 py-1 text-xs text-champagne bg-burgundy-deep/20 rounded">
                    {article.status}
                  </span>
                </td>
                <td className="py-4 text-rose-muted">
                  {article.is_featured ? 'Yes' : 'No'}
                </td>
                <td className="py-4 text-right space-x-2 whitespace-nowrap">
                  <button
                    onClick={() => toggleFeature(article)}
                    className="px-2.5 py-1.5 text-xs font-semibold text-champagne hover:bg-champagne/10 border border-champagne/20 rounded-lg transition-colors"
                  >
                    {article.is_featured ? 'Unfeature' : 'Feature'}
                  </button>
                  <button
                    onClick={() => remove(article.id)}
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

      {articles.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No articles found.
        </div>
      )}
    </div>
  );
}
