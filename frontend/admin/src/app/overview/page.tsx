'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';
import { DashboardStats } from '@/types/auth';
import Link from 'next/link';

export default function OverviewPage() {
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<DashboardStats>('/api/v1/admin/dashboard/stats')
      .then((res) => {
        setStats(unwrapData<DashboardStats>(res) ?? ({} as DashboardStats));
        setLoading(false);
      })
      .catch((err) => {
        setError(err instanceof Error ? err.message : 'Failed to load dashboard');
        setLoading(false);
      });
  }, []);

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="mylo-skeleton h-28" />
        <div className="grid md:grid-cols-3 lg:grid-cols-6 gap-4">
          {[0, 1, 2, 3, 4, 5].map((i) => (
            <div key={i} className="mylo-skeleton h-28" />
          ))}
        </div>
        <div className="grid lg:grid-cols-2 gap-6">
          <div className="mylo-skeleton h-72" />
          <div className="mylo-skeleton h-72" />
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="mylo-panel max-w-lg mx-auto mt-16 p-8 text-center">
        <p className="eyebrow">Something went wrong</p>
        <h1 className="font-display text-2xl text-champagne mt-2">Could not load the dashboard</h1>
        <p className="text-rose-muted text-sm mt-2">{error}</p>
        <a href="/login" className="mylo-btn mt-6">Back to sign in</a>
      </div>
    );
  }

  const s = stats!;

  const kpis = [
    { label: 'Readers', value: s.users.total_readers, foot: `${s.users.new_registrations} new` },
    { label: 'Writers', value: s.users.total_writers, foot: `${s.users.active_accounts} active` },
    { label: 'Published', value: s.publishing.published_articles, foot: `${s.publishing.scheduled_articles} scheduled` },
    { label: 'Drafts', value: s.publishing.drafts, foot: 'awaiting review' },
    { label: 'Books', value: s.marketplace.total_books, foot: `${s.marketplace.pending_orders} pending orders` },
    { label: 'Revenue', value: `$${s.finance.platform_revenue.toFixed(2)}`, foot: 'platform earnings' },
  ];

  const sections = [
    {
      title: 'Publishing Overview',
      href: '/articles',
      rows: [
        ['Total Articles', s.publishing.total_articles],
        ['Drafts', s.publishing.drafts],
        ['Scheduled', s.publishing.scheduled_articles],
        ['Recently Published', s.publishing.recently_published],
        ['Reported Articles', s.publishing.reported_articles],
      ],
    },
    {
      title: 'Marketplace Overview',
      href: '/books',
      rows: [
        ['Total Books', s.marketplace.total_books],
        ['Ebooks', s.marketplace.ebooks],
        ['Physical Books', s.marketplace.physical_books],
        ['Orders', s.marketplace.orders],
        ['Pending Orders', s.marketplace.pending_orders],
      ],
    },
    {
      title: 'Financial Overview',
      href: '/finance',
      rows: [
        ['Contributions', `$${s.finance.contributions.toFixed(2)}`],
        ['Book Sales', `$${s.finance.book_sales.toFixed(2)}`],
        ['Writer Earnings', `$${s.finance.writer_earnings.toFixed(2)}`],
        ['Pending Payouts', s.finance.pending_payouts],
        ['Refunds', `$${s.finance.refunds.toFixed(2)}`],
      ],
    },
  ];

  const quickActions = [
    { label: 'Feature an article', href: '/articles' },
    { label: 'Moderate comments', href: '/comments' },
    { label: 'Review payouts', href: '/payouts' },
    { label: 'Edit homepage', href: '/homepage' },
    { label: 'Manage categories', href: '/categories' },
    { label: 'View audit logs', href: '/audit-logs' },
  ];

  return (
    <div className="space-y-6">
      {/* Command header */}
      <section className="relative overflow-hidden rounded-2xl border border-champagne/10 bg-gradient-to-br from-[#2A1B24] via-[#1D141A] to-[#171013] px-6 py-6 md:px-8 md:py-8">
        <div
          aria-hidden
          className="absolute inset-0"
          style={{
            background:
              'radial-gradient(560px 200px at 88% -30%, rgba(216,189,156,0.16), transparent 60%), radial-gradient(480px 300px at -10% 130%, rgba(124,44,71,0.45), transparent 60%)',
          }}
        />
        <div className="relative flex flex-col md:flex-row md:items-end md:justify-between gap-5">
          <div>
            <p className="eyebrow !text-champagne">Command Centre</p>
            <h1 className="font-display text-3xl md:text-4xl font-black text-champagne mt-2">
              Administrator Dashboard
            </h1>
            <p className="text-rose-muted text-sm mt-1">
              Platform control centre — manage the Mylo publishing ecosystem.
            </p>
          </div>
          <Link href="/analytics" className="mylo-btn-ghost shrink-0">
            Open analytics →
          </Link>
        </div>
      </section>

      {/* KPI strip */}
      <section aria-label="Key performance indicators" className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        {kpis.map((kpi) => (
          <div key={kpi.label} className="kpi">
            <p className="eyebrow">{kpi.label}</p>
            <p className="kpi-value">{kpi.value}</p>
            <p className="text-[0.62rem] text-grey-muted mt-1">{kpi.foot}</p>
          </div>
        ))}
      </section>

      <div className="grid lg:grid-cols-3 gap-6">
        {/* Quick actions */}
        <section className="mylo-panel p-6">
          <p className="eyebrow">Quick Actions</p>
          <div className="mt-4 space-y-2">
            {quickActions.map((action) => (
              <Link
                key={action.href + action.label}
                href={action.href}
                className="flex items-center justify-between gap-3 px-4 py-3 rounded-xl border border-white/5 bg-white/[0.02] text-sm text-ivory-soft/90 hover:border-champagne/30 hover:text-champagne transition-all no-underline group"
              >
                {action.label}
                <span className="text-champagne opacity-60 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all">→</span>
              </Link>
            ))}
          </div>
        </section>

        {/* Summary panels */}
        {sections.map((section) => (
          <section key={section.title} className="mylo-panel p-6 flex flex-col">
            <div className="flex items-center justify-between">
              <p className="eyebrow">{section.title}</p>
              <Link href={section.href} className="text-[0.65rem] text-champagne/80 hover:text-champagne no-underline">
                Manage →
              </Link>
            </div>
            <dl className="mt-4 divide-y divide-white/5 text-sm flex-1">
              {section.rows.map(([label, value]) => (
                <div key={label as string} className="flex items-center justify-between py-2">
                  <dt className="text-rose-muted">{label}</dt>
                  <dd className="text-champagne font-semibold tabular-nums">{value}</dd>
                </div>
              ))}
            </dl>
          </section>
        ))}
      </div>

      {/* Engagement bar */}
      <section className="mylo-panel p-6">
        <p className="eyebrow">Engagement Snapshot</p>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
          {[
            { label: 'Article Views', value: s.engagement.article_views },
            { label: 'Likes', value: s.engagement.likes },
            { label: 'Comments', value: s.engagement.comments },
            { label: 'Active Accounts', value: s.users.active_accounts },
          ].map((item) => (
            <div key={item.label} className="rounded-xl border border-white/5 bg-white/[0.02] px-4 py-4">
              <p className="text-[0.62rem] uppercase tracking-[0.2em] text-grey-muted">{item.label}</p>
              <p className="font-display text-2xl font-bold text-ivory-soft mt-1.5 tabular-nums">
                {Number(item.value ?? 0).toLocaleString()}
              </p>
            </div>
          ))}
        </div>
        <div className="spark-bar mt-5" aria-hidden>
          {[42, 66, 51, 78, 58, 88, 96].map((h, i) => (
            <span key={i} style={{ height: `${h}%` }} />
          ))}
        </div>
      </section>
    </div>
  );
}
