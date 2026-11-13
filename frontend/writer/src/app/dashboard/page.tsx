'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapList, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Article {
  id: string;
  title: string;
  slug: string;
  status: string;
  category?: { name: string } | null;
  reading_time?: number;
  published_at?: string | null;
  created_at: string;
  updated_at?: string;
}

const statusBadge = (status: string) =>
  status === 'published'
    ? 'mylo-badge mylo-badge-green'
    : status === 'scheduled'
    ? 'mylo-badge mylo-badge-amber'
    : 'mylo-badge mylo-badge-grey';

export default function DashboardPage() {
  const [articles, setArticles] = useState<Article[]>([]);
  const [writerName, setWriterName] = useState<string>('Writer');
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
      .then((res) => setArticles(unwrapList<Article>(res)))
      .catch((err) => setError(err instanceof Error ? err.message : 'Failed to load articles'))
      .finally(() => setLoading(false));

    apiClient
      .get('/api/v1/writer/profile')
      .then((res) => {
        const d = unwrapData<{ display_name?: string | null; email?: string }>(res);
        if (d) setWriterName(d.display_name || d.email?.split('@')[0] || 'Writer');
      })
      .catch(() => undefined);
  }, []);

  const published = articles.filter((a) => a.status === 'published').length;
  const drafts = articles.filter((a) => a.status === 'draft').length;
  const scheduled = articles.filter((a) => a.status === 'scheduled').length;
  const completion = articles.length ? Math.round((published / articles.length) * 100) : 0;

  const stats = [
    { label: 'Published', value: published, hint: 'live on Mylo' },
    { label: 'Drafts', value: drafts, hint: 'in progress' },
    { label: 'Scheduled', value: scheduled, hint: 'queued to publish' },
    { label: 'Total', value: articles.length, hint: 'all time' },
  ];

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="mylo-skeleton h-44" />
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
          {[0, 1, 2, 3].map((i) => (
            <div key={i} className="mylo-skeleton h-28" />
          ))}
        </div>
        <div className="mylo-skeleton h-64" />
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {/* Welcome band */}
      <section className="relative overflow-hidden rounded-2xl border border-burgundy-deep/10 bg-burgundy-deep px-6 py-8 md:px-10 md:py-10">
        <div
          aria-hidden
          className="absolute inset-0"
          style={{
            background:
              'radial-gradient(600px 240px at 92% -20%, rgba(201,169,106,0.35), transparent 60%), radial-gradient(500px 320px at -12% 120%, rgba(114,38,63,0.75), transparent 60%)',
          }}
        />
        <div className="relative flex flex-col md:flex-row md:items-end md:justify-between gap-6">
          <div>
            <p className="eyebrow !text-champagne">Writer Studio</p>
            <h1 className="font-display !text-ivory-soft text-3xl md:text-4xl font-black mt-2">
              Welcome back, {writerName}.
            </h1>
            <p className="text-ivory-soft/75 text-sm md:text-base mt-2 max-w-md">
              {drafts > 0
                ? `You have ${drafts} draft${drafts === 1 ? '' : 's'} waiting for your attention.`
                : 'Your desk is clear. What will you write today?'}
            </p>
          </div>
          <Link href="/create" className="mylo-btn !bg-champagne !text-burgundy-deep shrink-0">
            + New Article
          </Link>
        </div>
      </section>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
          {error}
        </div>
      )}

      {/* Stats */}
      <section aria-label="Publishing statistics" className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((stat) => (
          <div key={stat.label} className="mylo-card p-5">
            <p className="eyebrow">{stat.label}</p>
            <p className="font-display text-4xl font-black text-burgundy-deep mt-2">
              {stat.value}
            </p>
            <p className="text-[0.7rem] text-charcoal/50 mt-1">{stat.hint}</p>
          </div>
        ))}
      </section>

      {/* Completion + quick actions */}
      <div className="grid lg:grid-cols-12 gap-6">
        <div className="lg:col-span-4 mylo-panel p-6 flex flex-col">
          <p className="eyebrow">Publication Health</p>
          <div className="flex items-end gap-3 mt-3">
            <span className="font-display text-5xl font-black text-burgundy-deep">
              {completion}%
            </span>
            <span className="text-xs text-charcoal/50 mb-2">of your articles
              <br />published</span>
          </div>
          <div
            className="mt-4 h-2.5 w-full rounded-full bg-burgundy-deep/10 overflow-hidden"
            role="progressbar"
            aria-valuenow={completion}
            aria-valuemin={0}
            aria-valuemax={100}
            aria-label="Publication completion"
          >
            <div
              className="h-full rounded-full bg-gradient-to-r from-champagne to-burgundy-wine transition-all duration-700"
              style={{ width: `${completion}%` }}
            />
          </div>
          <div className="spark-bar mt-6" aria-hidden>
            {[38, 55, 42, 70, 64, 82, completion || 30].map((h, i) => (
              <span key={i} style={{ height: `${h}%` }} />
            ))}
          </div>
          <p className="text-[0.65rem] text-charcoal/45 mt-2">Last 7 publishing signals</p>
        </div>

        <div className="lg:col-span-8 grid sm:grid-cols-2 gap-4">
          <Link href="/create" className="mylo-card mylo-card-hover p-6 no-underline group">
            <span className="eyebrow">Write</span>
            <h3 className="font-display text-xl font-bold text-burgundy-deep mt-2 group-hover:text-burgundy-wine transition-colors">
              Start a new story
            </h3>
            <p className="text-sm text-charcoal/60 mt-1">Block editor with autosave and scheduling.</p>
            <span className="inline-block mt-4 text-sm font-semibold text-burgundy-wine">Open editor →</span>
          </Link>
          <Link href="/drafts" className="mylo-card mylo-card-hover p-6 no-underline group">
            <span className="eyebrow">Continue</span>
            <h3 className="font-display text-xl font-bold text-burgundy-deep mt-2 group-hover:text-burgundy-wine transition-colors">
              {drafts} draft{drafts === 1 ? '' : 's'} in progress
            </h3>
            <p className="text-sm text-charcoal/60 mt-1">Pick up right where you left off.</p>
            <span className="inline-block mt-4 text-sm font-semibold text-burgundy-wine">Open drafts →</span>
          </Link>
          <Link href="/earnings" className="mylo-card mylo-card-hover p-6 no-underline group">
            <span className="eyebrow">Grow</span>
            <h3 className="font-display text-xl font-bold text-burgundy-deep mt-2 group-hover:text-burgundy-wine transition-colors">
              Earnings & payouts
            </h3>
            <p className="text-sm text-charcoal/60 mt-1">Reader support, book sales and requests.</p>
            <span className="inline-block mt-4 text-sm font-semibold text-burgundy-wine">View earnings →</span>
          </Link>
          <Link href="/analytics" className="mylo-card mylo-card-hover p-6 no-underline group">
            <span className="eyebrow">Measure</span>
            <h3 className="font-display text-xl font-bold text-burgundy-deep mt-2 group-hover:text-burgundy-wine transition-colors">
              Article analytics
            </h3>
            <p className="text-sm text-charcoal/60 mt-1">Reads, likes and comments at a glance.</p>
            <span className="inline-block mt-4 text-sm font-semibold text-burgundy-wine">View analytics →</span>
          </Link>
        </div>
      </div>

      {/* Recent articles */}
      <section>
        <div className="flex items-baseline justify-between border-b-2 border-burgundy-deep/15 pb-2 mb-4">
          <h2 className="eyebrow !text-burgundy-deep">Recent Articles</h2>
          <Link href="/articles" className="text-xs text-burgundy-wine hover:text-burgundy-deep no-underline">
            View all →
          </Link>
        </div>
        {articles.length === 0 ? (
          <div className="mylo-panel text-center py-14">
            <p className="font-display text-2xl text-burgundy-deep">Your byline awaits.</p>
            <p className="text-charcoal/55 text-sm mt-2">Write your first article and it will appear here.</p>
            <Link href="/create" className="mylo-btn mt-6">Start writing</Link>
          </div>
        ) : (
          <div className="mylo-card divide-y divide-burgundy-deep/8 overflow-hidden">
            {articles.slice(0, 5).map((article) => (
              <div key={article.id} className="flex items-center gap-4 px-5 py-4">
                <span className="hidden sm:flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-burgundy-deep/5 font-display text-lg font-bold text-burgundy-wine">
                  {article.title.charAt(0).toUpperCase()}
                </span>
                <div className="min-w-0 flex-1">
                  <p className="font-medium text-charcoal truncate">{article.title}</p>
                  <p className="text-[0.7rem] text-charcoal/50 mt-0.5">
                    {article.category?.name ? `${article.category.name} · ` : ''}
                    {new Date(article.updated_at || article.created_at).toLocaleDateString()}
                  </p>
                </div>
                <span className={`${statusBadge(article.status)} shrink-0`}>{article.status}</span>
              </div>
            ))}
          </div>
        )}
      </section>
    </div>
  );
}
