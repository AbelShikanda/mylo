'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapList } from '@/lib/api';

interface Article {
  id: string;
  title: string;
  slug: string;
  excerpt?: string;
  featured_image?: string;
  reading_time: number;
  like_count: number;
  published_at?: string;
  writer: { display_name?: string; email: string };
  category?: { name: string };
}

export default function ArticlesPage() {
  const [articles, setArticles] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    apiClient
      .get('/api/v1/articles')
      .then((res) => setArticles(unwrapList<Article>(res)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="max-w-3xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Articles…
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-3xl mx-auto py-12 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          Articles
        </h1>
        <p className="text-charcoal/70">
          All published stories from the Mylo newsroom.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {articles.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No articles published yet.</p>
        </div>
      ) : (
        <div className="space-y-4">
          {articles.map((article) => (
            <Link
              key={article.id}
              href={`/articles/${article.slug}`}
              className="flex gap-5 mylo-card p-5 hover:shadow-md transition-shadow no-underline"
            >
              {article.featured_image && (
                <img
                  src={article.featured_image}
                  alt={article.title}
                  className="w-28 h-28 object-cover rounded-lg shrink-0"
                />
              )}
              <div className="flex-1 min-w-0">
                {article.category && (
                  <span className="text-[10px] font-semibold text-burgundy-wine uppercase tracking-wider">
                    {article.category.name}
                  </span>
                )}
                <h2 className="font-serif font-bold text-burgundy-deep text-xl leading-snug mt-1">
                  {article.title}
                </h2>
                {article.excerpt && (
                  <p className="text-charcoal/70 text-sm mt-2 line-clamp-2">
                    {article.excerpt}
                  </p>
                )}
                <div className="flex items-center gap-3 text-xs text-charcoal/60 mt-3">
                  <span>{article.writer.display_name || article.writer.email}</span>
                  {article.published_at && (
                    <>
                      <span>•</span>
                      <span>{new Date(article.published_at).toLocaleDateString()}</span>
                    </>
                  )}
                  <span>•</span>
                  <span>{Math.max(1, Math.ceil(article.reading_time || 1))} min read</span>
                </div>
              </div>
            </Link>
          ))}
        </div>
      )}
    </div>
  );
}
