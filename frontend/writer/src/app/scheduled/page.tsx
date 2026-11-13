'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface ScheduledArticle {
  id: string;
  title: string;
  slug: string;
  scheduled_at?: string;
  published_at?: string;
  category?: { name: string };
}

export default function ScheduledPage() {
  const [articles, setArticles] = useState<ScheduledArticle[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/writer/scheduled')
      .then((res) => setArticles(unwrapList<ScheduledArticle>(res)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="max-w-3xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Scheduled Articles…
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-3xl mx-auto py-12 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          Scheduled
        </h1>
        <p className="text-charcoal/70">
          Articles queued for publication.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {articles.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">Nothing scheduled.</p>
          <p className="text-sm mt-2">Schedule a publish date from the article editor.</p>
        </div>
      ) : (
        <div className="mylo-card divide-y divide-burgundy-deep/10">
          {articles.map((article) => (
            <div key={article.id} className="px-5 py-4">
              <p className="font-medium text-charcoal">{article.title}</p>
              <p className="text-xs text-charcoal/50 mt-1">
                {article.category?.name ? `${article.category.name} • ` : ''}
                Publishes{' '}
                {new Date(article.scheduled_at || article.published_at || '').toLocaleString()}
              </p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
