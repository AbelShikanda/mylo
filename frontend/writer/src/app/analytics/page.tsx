'use client';

import { useEffect, useState } from 'react';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface AnalyticsData {
  total_views?: number;
  total_likes?: number;
  total_comments?: number;
  total_reads?: number;
  views?: number;
  likes?: number;
  comments?: number;
  [key: string]: unknown;
}

export default function AnalyticsPage() {
  const [data, setData] = useState<AnalyticsData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/writer/analytics')
      .then((res) => {
        const d = (res as { data?: unknown }).data;
        setData(
          d && typeof d === 'object' && !Array.isArray(d)
            ? (d as AnalyticsData)
            : {}
        );
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, []);

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Analytics…
        </h1>
      </div>
    );
  }

  const stats = [
    { label: 'Total Reads', value: data?.total_reads ?? data?.views ?? 0 },
    { label: 'Total Likes', value: data?.total_likes ?? data?.likes ?? 0 },
    { label: 'Total Comments', value: data?.total_comments ?? data?.comments ?? 0 },
  ];

  return (
    <div className="max-w-4xl mx-auto py-12 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          Analytics
        </h1>
        <p className="text-charcoal/70">
          How your articles are performing.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {stats.map((stat) => (
          <div
            key={stat.label}
            className="mylo-card p-6"
          >
            <p className="text-xs uppercase text-charcoal/60 tracking-wide">
              {stat.label}
            </p>
            <p className="text-3xl font-bold text-burgundy-deep mt-2">
              {Number(stat.value).toLocaleString()}
            </p>
          </div>
        ))}
      </div>

      <div className="mylo-card p-6">
        <p className="text-charcoal/70 text-sm">
          Detailed analytics (per-article performance, trends and reader behaviour) will appear here as your articles gather readers.
        </p>
      </div>
    </div>
  );
}
