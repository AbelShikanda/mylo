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
  like_count: number;
  writer: { display_name?: string; email: string };
  category?: { name: string };
}

interface PreferenceData {
  selected: { category_name: string }[];
  all: { name: string; slug: string }[];
}

export default function Home() {
  const [featured, setFeatured] = useState<Article[]>([]);
  const [recent, setRecent] = useState<Article[]>([]);
  const [personalized, setPersonalized] = useState<Article[]>([]);
  const [preferences, setPreferences] = useState<PreferenceData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = authStorage.getToken();

    // Fetch featured and recent articles
    apiClient.get<{ data: Article[] }>('/api/v1/articles').then((res) => {
      const d = res.data as { data: Article[] };
      setFeatured(d.data.slice(0, 4));
      setRecent(d.data.slice(0, 6));
      setLoading(false);
    });

    // Fetch preferences if user is logged in
    if (token) {
      apiClient
        .get<{ data: PreferenceData }>('/api/v1/reader/preferences')
        .then((res) => {
          const d = (res.data as unknown) as PreferenceData;
          setPreferences(d);
        })
        .catch(() => {
          // Ignore errors
        });
    }
  }, []);

  return (
    <div className="space-y-12">
      <div className="text-center py-16">
        <h1 className="text-4xl font-serif font-bold text-burgundy-deep mb-4">
          Mylo Reader Portal
        </h1>
        <p className="text-lg text-charcoal/80 max-w-2xl mx-auto">
          Discover and enjoy premium independent digital magazine content.
          Content comes first — no social networking, no follower pressure.
        </p>
      </div>

      {preferences && preferences.selected.length > 0 && (
        <div className="bg-burgundy-deep/5 p-6 rounded-xl border border-burgundy-deep/10 mb-8">
          <h2 className="font-serif font-bold text-burgundy-deep text-lg mb-3">
            For You
          </h2>
          <div className="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            {preferences.selected.map((p) => (
              <span
                key={p.category_name}
                className="px-3 py-1 text-xs text-burgundy-wine bg-champagne/20 rounded-full border border-champagne/30"
              >
                {p.category_name}
              </span>
            ))}
          </div>
        </div>
      )}

      {featured.length > 0 && (
        <div>
          <h2 className="font-serif font-bold text-burgundy-deep text-xl mb-4">
            Featured Stories
          </h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {featured.map((article) => (
              <article
                key={article.id}
                className="group bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm hover:border-burgundy-wine/50 transition-colors"
              >
                {article.featured_image && (
                  <img
                    src={article.featured_image}
                    alt={article.title}
                    className="w-full h-32 object-cover rounded-lg mb-4 group-hover:scale-105 transition-transform"
                  />
                )}
                {article.category && (
                  <span className="text-[10px] text-burgundy-wine font-bold uppercase tracking-wider block mb-2">
                    {article.category.name}
                  </span>
                )}
                <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-2">
                  <Link
                    href={`/articles/${article.slug}`}
                    className="hover:underline"
                  >
                    {article.title}
                  </Link>
                </h3>
                <div className="flex items-center gap-2 text-xs text-charcoal/60">
                  <span>
                    {article.writer.display_name || article.writer.email}
                  </span>
                  <span>•</span>
                  <span>{article.reading_time} min read</span>
                </div>
              </article>
            ))}
          </div>
        </div>
      )}

      {personalized.length > 0 && (
        <div>
          <h2 className="font-serif font-bold text-burgundy-deep text-xl mb-4">
            Recommended for You
          </h2>
          <div className="grid md:grid-cols-3 gap-6">
            {personalized.map((article) => (
              <article
                key={article.id}
                className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm hover:border-burgundy-wine/50 transition-colors"
              >
                {article.featured_image && (
                  <img
                    src={article.featured_image}
                    alt={article.title}
                    className="w-full h-32 object-cover rounded-lg mb-4 group-hover:scale-105 transition-transform"
                  />
                )}
                {article.category && (
                  <span className="text-[10px] text-burgundy-wine font-bold uppercase tracking-wider block mb-2">
                    {article.category.name}
                  </span>
                )}
                <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-2">
                  <Link
                    href={`/articles/${article.slug}`}
                    className="hover:underline"
                  >
                    {article.title}
                  </Link>
                </h3>
                <div className="flex items-center gap-2 text-xs text-charcoal/60">
                  <span>
                    {article.writer.display_name || article.writer.email}
                  </span>
                  <span>•</span>
                  <span>{article.reading_time} min read</span>
                </div>
              </article>
            ))}
          </div>
        </div>
      )}

      {recent.length > 0 && (
        <div>
          <h2 className="font-serif font-bold text-burgundy-deep text-xl mb-4">
            Recently Published
          </h2>
          <div className="grid md:grid-cols-3 gap-6">
            {recent.map((article) => (
              <article
                key={article.id}
                className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm hover:border-burgundy-wine/50 transition-colors"
              >
                {article.featured_image && (
                  <img
                    src={article.featured_image}
                    alt={article.title}
                    className="w-full h-32 object-cover rounded-lg mb-4 group-hover:scale-105 transition-transform"
                  />
                )}
                {article.category && (
                  <span className="text-[10px] text-burgundy-wine font-bold uppercase tracking-wider block mb-2">
                    {article.category.name}
                  </span>
                )}
                <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-2">
                  <Link
                    href={`/articles/${article.slug}`}
                    className="hover:underline"
                  >
                    {article.title}
                  </Link>
                </h3>
                <div className="flex items-center gap-2 text-xs text-charcoal/60">
                  <span>
                    {article.writer.display_name || article.writer.email}
                  </span>
                  <span>•</span>
                  <span>{article.reading_time} min read</span>
                </div>
              </article>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
