'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface HomepageSection {
  id: string;
  name: string;
  type: string;
  sort_order: number;
  is_active: boolean;
}

export default function HomepagePage() {
  const [sections, setSections] = useState<HomepageSection[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [form, setForm] = useState({ name: '', type: 'featured', sort_order: '0' });

  const load = () => {
    apiClient
      .get('/api/v1/admin/homepage/sections')
      .then((res) => setSections(unwrapList<HomepageSection>(res)))
      .catch((err) => setError(err.message))
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

  const createSection = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await apiClient.post('/api/v1/admin/homepage/sections', {
        name: form.name.trim(),
        type: form.type.trim(),
        sort_order: parseInt(form.sort_order, 10) || 0,
        is_active: true,
      });
      setForm({ name: '', type: 'featured', sort_order: '0' });
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  const toggleActive = async (section: HomepageSection) => {
    try {
      await apiClient.put(`/api/v1/admin/homepage/sections/${section.id}`, {
        is_active: !section.is_active,
      });
      setSections(
        sections.map((s) =>
          s.id === section.id ? { ...s, is_active: !s.is_active } : s
        )
      );
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Homepage...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div>
        <h1 className="text-3xl font-display font-bold text-champagne mb-2">
          Homepage Management
        </h1>
        <p className="text-rose-muted">
          Configure the sections shown on the reader homepage.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      <div className="mylo-panel p-6">
        <h3 className="font-display font-bold text-champagne text-lg mb-4">
          Add Section
        </h3>
        <form onSubmit={createSection} className="grid md:grid-cols-4 gap-4 items-end">
          <div>
            <label className="block text-xs text-rose-muted mb-1 uppercase">
              Name
            </label>
            <input
              value={form.name}
              onChange={(e) => setForm({ ...form, name: e.target.value })}
              required
              className="mylo-input"
              placeholder="Featured Stories"
            />
          </div>
          <div>
            <label className="block text-xs text-rose-muted mb-1 uppercase">
              Type
            </label>
            <input
              value={form.type}
              onChange={(e) => setForm({ ...form, type: e.target.value })}
              required
              className="mylo-input"
              placeholder="featured"
            />
          </div>
          <div>
            <label className="block text-xs text-rose-muted mb-1 uppercase">
              Sort Order
            </label>
            <input
              type="number"
              min={0}
              value={form.sort_order}
              onChange={(e) => setForm({ ...form, sort_order: e.target.value })}
              className="mylo-input"
            />
          </div>
          <button
            type="submit"
            className="mylo-btn text-sm"
          >
            Add Section
          </button>
        </form>
      </div>

      <div className="overflow-x-auto">
        <table className="mylo-table">
          <thead>
            <tr className="border-b border-burgundy-wine/40 text-rose-muted text-sm uppercase">
              <th className="pb-3">Name</th>
              <th className="pb-3">Type</th>
              <th className="pb-3">Order</th>
              <th className="pb-3">Status</th>
              <th className="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {sections.map((section) => (
              <tr key={section.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne">{section.name}</td>
                <td className="py-4 text-rose-muted">{section.type}</td>
                <td className="py-4 text-rose-muted">{section.sort_order}</td>
                <td className="py-4">
                  <span className={`px-2 py-1 text-xs rounded ${section.is_active ? 'text-green-400 bg-green-500/10' : 'text-rose-muted bg-charcoal/40'}`}>
                    {section.is_active ? 'Active' : 'Inactive'}
                  </span>
                </td>
                <td className="py-4 text-right">
                  <button
                    onClick={() => toggleActive(section)}
                    className="px-2.5 py-1.5 text-xs font-semibold text-champagne hover:bg-champagne/10 border border-champagne/20 rounded-lg transition-colors"
                  >
                    {section.is_active ? 'Deactivate' : 'Activate'}
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {sections.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No homepage sections configured.
        </div>
      )}
    </div>
  );
}
