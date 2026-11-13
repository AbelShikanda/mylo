'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';

const REPORT_ENDPOINTS = [
  { key: 'platform_overview', label: 'Platform Overview', path: '/api/v1/admin/analytics/platform-overview' },
  { key: 'registration_trends', label: 'Registration Trends', path: '/api/v1/admin/analytics/registration-trends' },
  { key: 'reader_engagement', label: 'Reader Engagement', path: '/api/v1/admin/analytics/reader-engagement' },
  { key: 'writer_activity', label: 'Writer Activity', path: '/api/v1/admin/analytics/writer-activity' },
  { key: 'article_performance', label: 'Article Performance', path: '/api/v1/admin/analytics/article-performance' },
  { key: 'category_popularity', label: 'Category Popularity', path: '/api/v1/admin/analytics/category-popularity' },
  { key: 'book_sales', label: 'Book Sales', path: '/api/v1/admin/analytics/book-sales' },
  { key: 'financial_contributions', label: 'Financial Contributions', path: '/api/v1/admin/analytics/financial-contributions' },
  { key: 'payouts', label: 'Payouts', path: '/api/v1/admin/analytics/payouts' },
  { key: 'failed_transactions', label: 'Failed Transactions', path: '/api/v1/admin/analytics/failed-transactions' },
  { key: 'moderation_activity', label: 'Moderation Activity', path: '/api/v1/admin/analytics/moderation-activity' },
] as const;

type ReportKey = (typeof REPORT_ENDPOINTS)[number]['key'];

export default function AnalyticsPage() {
  const [active, setActive] = useState<ReportKey>('platform_overview');
  const [data, setData] = useState<Record<string, unknown> | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    const endpoint = REPORT_ENDPOINTS.find((r) => r.key === active)!;
    apiClient
      .get(endpoint.path)
      .then((res) => {
        const d = unwrapData<Record<string, unknown>>(res);
        setData(d && typeof d === 'object' ? d : {});
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, [active]);

  const renderValue = (value: unknown): string => {
    if (value === null || value === undefined) return '—';
    if (typeof value === 'number') return value.toLocaleString();
    if (typeof value === 'object') return JSON.stringify(value);
    return String(value);
  };

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div>
        <h1 className="text-3xl font-display font-bold text-champagne mb-2">
          Analytics and Reports
        </h1>
        <p className="text-rose-muted">
          Platform performance across publishing, marketplace and finance.
        </p>
      </div>

      <div className="flex gap-2 overflow-x-auto pb-2">
        {REPORT_ENDPOINTS.map((report) => (
          <button
            key={report.key}
            onClick={() => {
              setLoading(true);
              setError(null);
              setActive(report.key);
            }}
            className={`px-4 py-2 text-sm whitespace-nowrap rounded-md transition-colors ${
              active === report.key
                ? 'bg-burgundy-deep text-white'
                : 'text-rose-muted hover:text-champagne hover:bg-burgundy-deep/20'
            }`}
          >
            {report.label}
          </button>
        ))}
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      {loading ? (
        <div className="text-rose-muted py-12 text-center">Loading report…</div>
      ) : data && Object.keys(data).length > 0 ? (
        <div className="grid md:grid-cols-3 gap-4">
          {Object.entries(data).map(([key, value]) => (
            <div key={key} className="mylo-panel p-5">
              <p className="text-xs uppercase text-rose-muted tracking-wide">
                {key.replace(/_/g, ' ')}
              </p>
              <p className="text-xl font-bold text-champagne mt-2 break-words">
                {renderValue(value)}
              </p>
            </div>
          ))}
        </div>
      ) : (
        !error && (
          <div className="text-center py-12 text-rose-muted">
            No data available for this report yet.
          </div>
        )
      )}
    </div>
  );
}
