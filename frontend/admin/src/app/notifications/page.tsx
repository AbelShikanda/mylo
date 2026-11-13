'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface AdminNotification {
  id?: string;
  title?: string;
  message?: string;
  body?: string;
  type?: string;
  created_at?: string;
  read_at?: string | null;
}

export default function NotificationsPage() {
  const [notifications, setNotifications] = useState<AdminNotification[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/admin/notifications')
      .then((res) => setNotifications(unwrapList<AdminNotification>(res)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Notifications...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto py-8 px-4 space-y-6">
      <div>
        <h1 className="text-3xl font-display font-bold text-champagne mb-2">
          Notifications
        </h1>
        <p className="text-rose-muted">
          Platform alerts and administrative notifications.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      {notifications.length === 0 ? (
        <div className="text-center py-12 text-rose-muted">
          No notifications yet.
        </div>
      ) : (
        <div className="mylo-card divide-y divide-burgundy-wine/20">
          {notifications.map((n, i) => (
            <div key={n.id ?? i} className="px-5 py-4">
              <div className="flex items-center justify-between gap-4">
                <p className="text-champagne font-medium">
                  {n.title || n.type || 'Notification'}
                </p>
                {n.created_at && (
                  <span className="text-xs text-rose-muted whitespace-nowrap">
                    {new Date(n.created_at).toLocaleString()}
                  </span>
                )}
              </div>
              {(n.message || n.body) && (
                <p className="text-sm text-rose-muted mt-1">{n.message || n.body}</p>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
