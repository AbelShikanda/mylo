'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface EditorialSelection {
  id: string;
  article_id: string;
  section: string;
  sort_order: number;
  starts_at?: string;
  ends_at?: string;
  article?: { title: string; slug: string };
}

interface ScheduledArticle {
  id: string;
  title: string;
  status: string;
  scheduled_at?: string;
  published_at?: string;
}

export default function EditorialPage() {
  const [selections, setSelections] = useState<EditorialSelection[]>([]);
  const [scheduled, setScheduled] = useState<ScheduledArticle[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [form, setForm] = useState({ article_id: '', section: 'featured', sort_order: '0' });

  const load = () => {
    Promise.allSettled([
      apiClient
        .get('/api/v1/admin/editorial/selections')
        .then((res) => setSelections(unwrapList<EditorialSelection>(res))),
      apiClient
        .get('/api/v1/admin/editorial/scheduled-articles')
        .then((res) => setScheduled(unwrapList<ScheduledArticle>(res))),
    ])
      .then((results) => {
        const failed = results.find((r) => r.status === 'rejected') as PromiseRejectedResult | undefined;
        if (failed) setError(failed.reason?.message ?? 'Failed to load editorial data');
      })
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }
    load();
     
  }, []);

  const createSelection = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await apiClient.post('/api/v1/admin/editorial/selections', {
        article_id: form.article_id.trim(),
        section: form.section.trim(),
        sort_order: parseInt(form.sort_order, 10) || 0,
      });
      setForm({ article_id: '', section: 'featured', sort_order: '0' });
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  const deleteSelection = async (id: string) => {
    if (!confirm('Delete this editorial selection?')) return;
    try {
      await apiClient.delete(`/api/v1/admin/editorial/selections/${id}`);
      setSelections(selections.filter((s) => s.id !== id));
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Editorial…
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-display font-bold text-champagne mb-2">
          Editorial Management
        </h1>
        <p className="text-rose-muted">
          Curate homepage selections and scheduled articles.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      <div className="mylo-panel p-6">
        <h3 className="font-display font-bold text-champagne text-lg mb-4">
          Add Editorial Selection
        </h3>
        <form onSubmit={createSelection} className="grid md:grid-cols-4 gap-4 items-end">
          <div className="md:col-span-2">
            <label className="block text-xs text-rose-muted mb-1 uppercase">
              Article ID
            </label>
            <input
              value={form.article_id}
              onChange={(e) => setForm({ ...form, article_id: e.target.value })}
              required
              className="mylo-input"
              placeholder="UUID of the article"
            />
          </div>
          <div>
            <label className="block text-xs text-rose-muted mb-1 uppercase">
              Section
            </label>
            <input
              value={form.section}
              onChange={(e) => setForm({ ...form, section: e.target.value })}
              required
              className="mylo-input"
              placeholder="featured"
            />
          </div>
          <div className="flex gap-2">
            <input
              type="number"
              min={0}
              value={form.sort_order}
              onChange={(e) => setForm({ ...form, sort_order: e.target.value })}
              className="w-24 px-3 py-2 border border-burgundy-wine/30 rounded bg-charcoal/50 text-champagne focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
            />
            <button
              type="submit"
              className="mylo-btn text-sm"
            >
              Add
            </button>
          </div>
        </form>
      </div>

      <div>
        <h3 className="font-display font-bold text-champagne text-lg mb-3">
          Current Selections
        </h3>
        <div className="overflow-x-auto">
          <table className="mylo-table">
            <thead>
              <tr className="border-b border-burgundy-wine/40 text-rose-muted text-sm uppercase">
                <th className="pb-3">Article</th>
                <th className="pb-3">Section</th>
                <th className="pb-3">Order</th>
                <th className="pb-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              {selections.map((s) => (
                <tr key={s.id} className="border-b border-charcoal/30">
                  <td className="py-3 text-champagne">
                    {s.article?.title || s.article_id}
                  </td>
                  <td className="py-3 text-rose-muted">{s.section}</td>
                  <td className="py-3 text-rose-muted">{s.sort_order}</td>
                  <td className="py-3 text-right">
                    <button
                      onClick={() => deleteSelection(s.id)}
                      className="px-2.5 py-1.5 text-xs font-semibold text-[#F46F6F] hover:bg-red-500/10 border border-red-500/20 rounded-lg transition-colors"
                    >
                      Remove
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        {selections.length === 0 && (
          <div className="text-center py-6 text-rose-muted">No editorial selections yet.</div>
        )}
      </div>

      <div>
        <h3 className="font-display font-bold text-champagne text-lg mb-3">
          Scheduled Articles
        </h3>
        <div className="mylo-card divide-y divide-burgundy-wine/20">
          {scheduled.map((a) => (
            <div key={a.id} className="flex items-center justify-between px-5 py-4">
              <span className="text-champagne">{a.title}</span>
              <span className="text-xs text-rose-muted">
                {a.scheduled_at || a.published_at
                  ? new Date(a.scheduled_at || a.published_at || '').toLocaleString()
                  : 'No date'}
              </span>
            </div>
          ))}
          {scheduled.length === 0 && (
            <div className="text-center py-6 text-rose-muted">No scheduled articles.</div>
          )}
        </div>
      </div>
    </div>
  );
}
