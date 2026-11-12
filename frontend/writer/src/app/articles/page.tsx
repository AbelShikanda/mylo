'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Article {
  id: string;
  title: string;
  slug: string;
  status: string;
  created_at: string;
  published_at?: string | null;
  category?: { name: string };
}

export default function ArticlesPage() {
  const [articles, setArticles] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/writer/articles')
      .then((res) => {
        setArticles(res.data as Article[]);
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
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-6">
          Loading articles…
        </h1>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-6">
          Error
        </h1>
        <p className="text-charcoal/70">{error}</p>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          My Articles
        </h1>
        <Link
          href="/create"
          className="px-6 py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors"
        >
          Create Article
        </Link>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b border-burgundy-deep/20 text-charcoal/60 text-sm uppercase">
              <th className="pb-3">Title</th>
              <th className="pb-3">Status</th>
              <th className="pb-3">Category</th>
              <th className="pb-3">Created</th>
              <th className="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {articles.map((article) => (
              <tr
                key={article.id}
                className="border-b border-burgundy-deep/10 hover:bg-ivory-soft/50 transition-colors"
              >
                <td className="py-4 text-charcoal">
                  <Link
                    href={`/articles/${article.id}`}
                    className="text-burgundy-wine hover:underline font-medium"
                  >
                    {article.title}
                  </Link>
                </td>
                <td className="py-4">
                  <span className={`px-2 py-1 rounded-md text-xs font-medium ${
                    article.status === 'published'
                      ? 'bg-green-100 text-green-700'
                      : article.status === 'draft'
                        ? 'bg-ivory-soft text-charcoal/70'
                        : 'bg-burgundy-deep/20 text-burgundy-wine'
                  }`}>
                    {article.status}
                  </span>
                </td>
                <td className="py-4 text-charcoal/70">
                  {article.category?.name || '—'}
                </td>
                <td className="py-4 text-charcoal/70 text-sm">
                  {new Date(article.created_at).toLocaleDateString()}
                </td>
                <td className="py-4 text-right space-x-2">
                  <Link
                    href={`/articles/${article.id}`}
                    className="text-sm text-burgundy-wine hover:underline"
                  >
                    Edit
                  </Link>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {articles.length === 0 && (
        <div className="text-center py-12 text-charcoal/70">
          <p className="text-lg">No articles yet.</p>
          <Link
            href="/create"
            className="mt-4 inline-block px-6 py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors"
          >
            Create your first article
          </Link>
        </div>
      )}
    </div>
  );
}
