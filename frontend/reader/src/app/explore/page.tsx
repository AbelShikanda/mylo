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
  writer: { display_name?: string; email: string };
  category?: { name: string };
}

interface Category {
  id: string;
  name: string;
  slug: string;
}

export default function ExplorePage() {
  const [articles, setArticles] = useState<Article[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [query, setQuery] = useState('');
  const [activeCategory, setActiveCategory] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    apiClient
      .get('/api/v1/articles')
      .then((res) => setArticles(unwrapList<Article>(res)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));

    apiClient
      .get('/api/v1/categories')
      .then((res) => setCategories(unwrapList<Category>(res)))
      .catch(() => setCategories([]));
  }, []);

  const q = query.trim().toLowerCase();
  const filtered = articles.filter((a) => {
    if (activeCategory && a.category?.name !== activeCategory) return false;
    if (!q) return true;
    return (
      a.title.toLowerCase().includes(q) ||
      a.excerpt?.toLowerCase().includes(q) ||
      a.writer.display_name?.toLowerCase().includes(q) ||
      a.writer.email.toLowerCase().includes(q)
    );
  });

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Explore…
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto py-12 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          Explore
        </h1>
        <p className="text-charcoal/70">
          Discover stories, essays and features from independent writers.
        </p>
      </div>

      <input
        type="text"
        value={query}
        onChange={(e) => setQuery(e.target.value)}
        placeholder="Search articles, writers…"
        className="w-full px-4 py-3 border border-burgundy-deep/30 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy-wine bg-white"
      />

      {categories.length > 0 && (
        <div className="flex gap-2 overflow-x-auto pb-2">
          <button
            onClick={() => setActiveCategory(null)}
            className={`px-4 py-2 text-sm rounded-full whitespace-nowrap transition-colors ${
              activeCategory === null
                ? 'bg-burgundy-deep text-white'
                : 'bg-burgundy-deep/10 text-burgundy-wine hover:bg-burgundy-deep/20'
            }`}
          >
            All
          </button>
          {categories.map((c) => (
            <button
              key={c.slug}
              onClick={() => setActiveCategory(activeCategory === c.name ? null : c.name)}
              className={`px-4 py-2 text-sm rounded-full whitespace-nowrap transition-colors ${
                activeCategory === c.name
                  ? 'bg-burgundy-deep text-white'
                  : 'bg-burgundy-deep/10 text-burgundy-wine hover:bg-burgundy-deep/20'
              }`}
            >
              {c.name}
            </button>
          ))}
        </div>
      )}

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {filtered.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No articles found.</p>
        </div>
      ) : (
        <div className="grid md:grid-cols-2 gap-6">
          {filtered.map((article) => (
            <Link
              key={article.id}
              href={`/articles/${article.slug}`}
              className="mylo-card overflow-hidden hover:shadow-md transition-shadow no-underline group"
            >
              {article.featured_image && (
                <img
                  src={article.featured_image}
                  alt={article.title}
                  className="w-full h-40 object-cover"
                />
              )}
              <div className="p-5">
                {article.category && (
                  <span className="text-[10px] font-semibold text-burgundy-wine uppercase tracking-wider">
                    {article.category.name}
                  </span>
                )}
                <h2 className="font-serif font-bold text-burgundy-deep text-xl mt-1 mb-2 group-hover:underline">
                  {article.title}
                </h2>
                {article.excerpt && (
                  <p className="text-charcoal/70 text-sm mb-3 line-clamp-2">
                    {article.excerpt}
                  </p>
                )}
                <div className="flex items-center gap-3 text-xs text-charcoal/60">
                  <span>{article.writer.display_name || article.writer.email}</span>
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
