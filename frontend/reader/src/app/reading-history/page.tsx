'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Article {
  id: string;
  title: string;
  slug: string;
  excerpt?: string;
  featured_image?: string;
  reading_time: number;
  duration_seconds: number;
  read_at: string;
  writer: { display_name?: string; email: string };
  category?: { name: string };
}

export default function ReadingHistoryPage() {
  const [history, setHistory] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<{ data: Article[] }>('/api/v1/reader/reading-history')
      .then((res) => {
        setHistory(unwrapList<Article>(res));
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, []);

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Reading History…
        </h1>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Error
        </h1>
        <p className="text-charcoal/70 text-center">{error}</p>
      </div>
    );
  }

  return (
    <div className="max-w-2xl mx-auto py-12 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          Reading History
        </h1>
        <p className="text-charcoal/70">
          Articles you have recently read.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {history.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No reading history yet.</p>
          <Link
            href="/"
            className="mt-4 inline-block mylo-btn"
          >
            Browse articles
          </Link>
        </div>
      ) : (
        <div className="space-y-6">
          {history.map((article) => (
            <div
              key={article.id}
              className="mylo-card p-6"
            >
              <div className="flex items-start gap-4">
                {article.featured_image && (
                  <img
                    src={article.featured_image}
                    alt={article.title}
                    className="w-32 h-32 object-cover rounded-lg"
                  />
                )}
                <div className="flex-1">
                  <h3 className="font-serif font-bold text-burgundy-deep text-xl mb-2">
                    <Link
                      href={"/articles/" + article.slug}
                      className="hover:underline"
                    >
                      {article.title}
                    </Link>
                  </h3>
                  {article.excerpt && (
                    <p className="text-charcoal/70 text-sm mb-3">
                      {article.excerpt}
                    </p>
                  )}
                  <div className="flex items-center gap-3 text-xs text-charcoal/60">
                    <span>{article.writer.display_name || article.writer.email}</span>
                    <span>•</span>
                    <span>{new Date(article.read_at).toLocaleDateString()}</span>
                    <span>•</span>
                    <span>{Math.round(article.duration_seconds / 60)} min</span>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
