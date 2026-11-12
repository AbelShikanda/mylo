'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Article {
  id: string;
  title: string;
  slug: string;
  excerpt?: string;
  featured_image?: string;
  reading_time: number;
  writer: { display_name?: string; email: string };
  category?: { name: string };
}

export default function BookmarksPage() {
  const [bookmarks, setBookmarks] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<{ data: Article[] }>('/api/v1/reader/bookmarks')
      .then((res) => {
        const d = res.data as { data: Article[] };
        setBookmarks(d.data);
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
          Loading Bookmarks…
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
          Saved Articles
        </h1>
        <p className="text-charcoal/70">
          Articles you have bookmarked for later reading.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {bookmarks.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No bookmarks yet.</p>
          <Link
            href="/"
            className="mt-4 inline-block px-6 py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors"
          >
            Browse articles
          </Link>
        </div>
      ) : (
        <div className="space-y-6">
          {bookmarks.map((article) => (
            <div
              key={article.id}
              className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm"
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
                    <span>{Math.max(1, Math.ceil(article.reading_time || 0))} min read</span>
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
