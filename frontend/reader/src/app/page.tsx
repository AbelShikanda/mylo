'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapList, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';

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

interface PreferenceData {
  selected: { category_name: string }[];
  all: { name: string; slug: string }[];
}

function readingMinutes(article: Article) {
  return Math.max(1, Math.ceil(article.reading_time || 1));
}

export default function Home() {
  const [articles, setArticles] = useState<Article[]>([]);
  const [preferences, setPreferences] = useState<PreferenceData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = authStorage.getToken();

    apiClient
      .get('/api/v1/articles')
      .then((res) => setArticles(unwrapList<Article>(res)))
      .catch(() => setArticles([]))
      .finally(() => setLoading(false));

    if (token) {
      apiClient
        .get('/api/v1/reader/preferences')
        .then((res) => setPreferences(unwrapData<PreferenceData>(res)))
        .catch(() => setPreferences(null));
    }
  }, []);

  const [lead, ...rest] = articles;
  const featured = rest.filter((a) => a.featured_image).slice(0, 3);
  const latest = rest.slice(0, 6);

  return (
    <div className="space-y-10 md:space-y-14">
      {/* Masthead */}
      <section className="relative overflow-hidden rounded-[22px] border border-burgundy-deep/10 bg-burgundy-deep text-ivory-soft px-6 py-12 md:px-14 md:py-16">
        <div
          aria-hidden
          className="absolute inset-0 opacity-60"
          style={{
            background:
              'radial-gradient(700px 320px at 88% -20%, rgba(201,169,106,0.38), transparent 60%), radial-gradient(500px 380px at -10% 110%, rgba(114,38,63,0.7), transparent 60%)',
          }}
        />
        <div className="relative max-w-3xl">
          <p className="eyebrow !text-champagne mb-4">Est. MMXXIV — Independent Publishing</p>
          <h1 className="font-display !text-ivory-soft text-4xl sm:text-5xl lg:text-6xl font-black leading-[1.02]">
            Stories worth
            <br />
            <span className="italic text-champagne">sitting with.</span>
          </h1>
          <p className="mt-5 text-ivory-soft/80 text-base md:text-lg max-w-xl">
            Essays, features and reporting from independent African writers —
            curated with care, read without noise.
          </p>
          <div className="mt-7 flex flex-wrap items-center gap-3">
            <Link href="/explore" className="mylo-btn !bg-champagne !text-burgundy-deep">
              Start exploring
            </Link>
            <Link
              href="/register"
              className="text-sm text-ivory-soft/80 hover:text-champagne transition-colors underline-offset-4 hover:underline"
            >
              Join Mylo — it’s free
            </Link>
          </div>
        </div>
      </section>

      {preferences && preferences.selected.length > 0 && (
        <section aria-label="Your interests" className="flex flex-wrap items-center gap-2">
          <span className="eyebrow mr-1">For you</span>
          {preferences.selected.map((p) => (
            <Link
              key={p.category_name}
              href={`/explore`}
              className="mylo-badge mylo-badge-gold hover:scale-105 transition-transform no-underline"
            >
              {p.category_name}
            </Link>
          ))}
        </section>
      )}

      {loading ? (
        <div className="grid lg:grid-cols-12 gap-6">
          <div className="lg:col-span-8 mylo-skeleton h-96" />
          <div className="lg:col-span-4 space-y-4">
            <div className="mylo-skeleton h-28" />
            <div className="mylo-skeleton h-28" />
            <div className="mylo-skeleton h-28" />
          </div>
        </div>
      ) : lead ? (
        <>
          {/* Lead story + featured rail */}
          <section className="grid lg:grid-cols-12 gap-6">
            <Link
              href={`/articles/${lead.slug}`}
              className="lg:col-span-8 relative rounded-[22px] overflow-hidden min-h-[420px] lg:min-h-[560px] flex flex-col justify-end group no-underline border border-burgundy-deep/10 shadow-[0_24px_48px_-20px_rgba(58,14,30,0.45)]"
            >
              {lead.featured_image ? (
                <img
                  src={lead.featured_image}
                  alt={lead.title}
                  className="absolute inset-0 h-full w-full object-cover group-hover:scale-[1.03] transition-transform duration-[1.4s]"
                />
              ) : (
                <div
                  aria-hidden
                  className="absolute inset-0"
                  style={{
                    background:
                      'radial-gradient(600px 400px at 70% 0%, rgba(201,169,106,0.5), transparent 60%), linear-gradient(160deg, #54172B 0%, #3A0E1E 100%)',
                  }}
                />
              )}
              <div
                aria-hidden
                className="absolute inset-0 bg-gradient-to-t from-burgundy-deep/95 via-burgundy-deep/35 to-transparent"
              />
              <div className="relative p-6 md:p-9">
                {lead.category && (
                  <span className="mylo-badge mylo-badge-gold !bg-champagne/15 !text-champagne !border-champagne/40">
                    {lead.category.name}
                  </span>
                )}
                <h2 className="font-display !text-ivory-soft text-3xl md:text-5xl font-black leading-[1.08] mt-3">
                  {lead.title}
                </h2>
                {lead.excerpt && (
                  <p className="mt-3 text-ivory-soft/85 text-sm md:text-base max-w-xl line-clamp-2">
                    {lead.excerpt}
                  </p>
                )}
                <p className="mt-4 text-xs text-ivory-soft/70 tracking-wide">
                  {lead.writer.display_name || lead.writer.email} · {readingMinutes(lead)} min read
                </p>
              </div>
            </Link>

            <div className="lg:col-span-4 flex flex-col gap-4">
              <div className="flex items-baseline justify-between border-b-2 border-burgundy-deep/15 pb-2">
                <h2 className="eyebrow !text-burgundy-deep">Featured</h2>
                <Link href="/explore" className="text-xs text-burgundy-wine hover:text-burgundy-deep no-underline">
                  View all →
                </Link>
              </div>
              {featured.map((article) => (
                <Link
                  key={article.id}
                  href={`/articles/${article.slug}`}
                  className="mylo-card mylo-card-hover p-5 flex gap-4 no-underline"
                >
                  <div
                    aria-hidden
                    className="shrink-0 w-1 self-stretch rounded-full bg-gradient-to-b from-champagne via-burgundy-wine/60 to-transparent"
                  />
                  <div>
                    {article.category && (
                      <span className="eyebrow">{article.category.name}</span>
                    )}
                    <h3 className="font-display text-lg font-bold text-burgundy-deep leading-snug mt-1">
                      {article.title}
                    </h3>
                    <p className="mt-2 text-[0.7rem] text-charcoal/55">
                      {readingMinutes(article)} min read
                    </p>
                  </div>
                </Link>
              ))}
              {featured.length === 0 && (
                <div className="text-sm text-charcoal/50 italic">No featured stories yet.</div>
              )}
            </div>
          </section>

          {/* Latest */}
          <section>
            <div className="flex items-baseline justify-between border-b-2 border-burgundy-deep/15 pb-2 mb-5">
              <h2 className="eyebrow !text-burgundy-deep">Recently Published</h2>
              <Link href="/articles" className="text-xs text-burgundy-wine hover:text-burgundy-deep no-underline">
                All articles →
              </Link>
            </div>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
              {latest.map((article) => (
                <Link
                  key={article.id}
                  href={`/articles/${article.slug}`}
                  className="mylo-card mylo-card-hover overflow-hidden no-underline group"
                >
                  {article.featured_image && (
                    <div className="h-40 overflow-hidden">
                      <img
                        src={article.featured_image}
                        alt={article.title}
                        className="h-full w-full object-cover group-hover:scale-[1.04] transition-transform duration-700"
                      />
                    </div>
                  )}
                  <div className="p-5">
                    {article.category && (
                      <span className="eyebrow">{article.category.name}</span>
                    )}
                    <h3 className="font-display text-xl font-bold text-burgundy-deep leading-snug mt-2 group-hover:text-burgundy-wine transition-colors">
                      {article.title}
                    </h3>
                    <p className="mt-3 text-xs text-charcoal/60 flex items-center gap-2">
                      <span>{article.writer.display_name || article.writer.email}</span>
                      <span aria-hidden>·</span>
                      <span>{readingMinutes(article)} min</span>
                    </p>
                  </div>
                </Link>
              ))}
            </div>
          </section>
        </>
      ) : (
        <div className="text-center py-20">
          <p className="font-display text-2xl text-burgundy-deep">Nothing published yet.</p>
          <p className="text-charcoal/60 mt-2">The newsroom is warming up — check back soon.</p>
        </div>
      )}
    </div>
  );
}
