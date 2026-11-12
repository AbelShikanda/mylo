'use client';

import { useEffect, useState } from 'react';
import { apiClient } from '@/lib/api';
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
      .get<DashboardStats>('/api/v1/dashboard/stats')
      .then((res) => {
        setStats(res.data);
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
        <h1 className="text-3xl font-serif font-bold text-champagne mb-6">
          Loading Dashboard...
        </h1>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-serif font-bold text-champagne mb-6">
          Error
        </h1>
        <p className="text-rose-muted">{error}</p>
      </div>
    );
  }

  const s = stats!;

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div>
        <h1 className="text-3xl font-serif font-bold text-champagne mb-2">
          Administrator Dashboard
        </h1>
        <p className="text-rose-muted">
          Platform control centre — manage the Mylo publishing ecosystem.
        </p>
      </div>

      <div className="grid md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">
            Total Readers
          </p>
          <p className="text-2xl font-bold text-champagne">
            {s.users.total_readers}
          </p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">
            Total Writers
          </p>
          <p className="text-2xl font-bold text-champagne">
            {s.users.total_writers}
          </p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">
            Published Articles
          </p>
          <p className="text-2xl font-bold text-champagne">
            {s.publishing.published_articles}
          </p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">
            Drafts
          </p>
          <p className="text-2xl font-bold text-champagne">{s.publishing.drafts}</p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">
            Total Books
          </p>
          <p className="text-2xl font-bold text-champagne">
            {s.marketplace.total_books}
          </p>
        </div>
        <div className="bg-burgundy-deep/30 p-4 rounded-lg border border-burgundy-wine/40">
          <p className="text-xs text-rose-muted mb-1 uppercase tracking-wider">
            Platform Revenue
          </p>
          <p className="text-2xl font-bold text-champagne">
            ${s.finance.platform_revenue.toFixed(2)}
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="bg-burgundy-deep/30 p-6 rounded-lg border border-burgundy-wine/40">
          <h3 className="font-serif font-bold text-champagne text-lg mb-4">
            Quick Actions
          </h3>
          <div className="space-y-3">
            <Link
              href="/readers"
              className="block px-4 py-3 text-sm text-burgundy-wine hover:bg-burgundy-deep/20 rounded-lg transition-colors border border-transparent hover:border-burgundy-wine/30"
            >
              Manage Readers
            </Link>
            <Link
              href="/writers"
              className="block px-4 py-3 text-sm text-burgundy-wine hover:bg-burgundy-deep/20 rounded-lg transition-colors border border-transparent hover:border-burgundy-wine/30"
            >
              Manage Writers
            </Link>
            <Link
              href="/categories"
              className="block px-4 py-3 text-sm text-burgundy-wine hover:bg-burgundy-deep/20 rounded-lg transition-colors border border-transparent hover:border-burgundy-wine/30"
            >
              Manage Categories
            </Link>
            <Link
              href="/audit-logs"
              className="block px-4 py-3 text-sm text-burgundy-wine hover:bg-burgundy-deep/20 rounded-lg transition-colors border border-transparent hover:border-burgundy-wine/30"
            >
              View Audit Logs
            </Link>
          </div>
        </div>

        <div className="bg-burgundy-deep/30 p-6 rounded-lg border border-burgundy-wine/40">
          <h3 className="font-serif font-bold text-champagne text-lg mb-4">
            Publishing Overview
          </h3>
          <div className="space-y-3 text-sm">
            <div className="flex justify-between">
              <span className="text-rose-muted">Total Articles</span>
              <span className="text-champagne">{s.publishing.total_articles}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-rose-muted">Drafts</span>
              <span className="text-champagne">{s.publishing.drafts}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-rose-muted">Scheduled</span>
              <span className="text-champagne">
                {s.publishing.scheduled_articles}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-rose-muted">Reported Articles</span>
              <span className="text-champagne">
                {s.publishing.reported_articles}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="bg-burgundy-deep/30 p-6 rounded-lg border border-burgundy-wine/40">
          <h3 className="font-serif font-bold text-champagne text-lg mb-4">
            Marketplace Overview
          </h3>
          <div className="space-y-3 text-sm">
            <div className="flex justify-between">
              <span className="text-rose-muted">Total Books</span>
              <span className="text-champagne">{s.marketplace.total_books}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-rose-muted">Ebooks</span>
              <span className="text-champagne">{s.marketplace.ebooks}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-rose-muted">Physical Books</span>
              <span className="text-champagne">
                {s.marketplace.physical_books}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-rose-muted">Pending Orders</span>
              <span className="text-champagne">
                {s.marketplace.pending_orders}
              </span>
            </div>
          </div>
        </div>

        <div className="bg-burgundy-deep/30 p-6 rounded-lg border border-burgundy-wine/40">
          <h3 className="font-serif font-bold text-champagne text-lg mb-4">
            Financial Overview
          </h3>
          <div className="space-y-3 text-sm">
            <div className="flex justify-between">
              <span className="text-rose-muted">Contributions</span>
              <span className="text-champagne">
                ${s.finance.contributions.toFixed(2)}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-rose-muted">Writer Earnings</span>
              <span className="text-champagne">
                ${s.finance.writer_earnings.toFixed(2)}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-rose-muted">Pending Payouts</span>
              <span className="text-champagne">
                {s.finance.pending_payouts}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-rose-muted">Refunds</span>
              <span className="text-champagne">${s.finance.refunds.toFixed(2)}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
