'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { apiClient, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Category {
  id: string;
  name: string;
  slug: string;
  is_featured: boolean;
}

interface PreferenceData {
  selected: { id: string; category_id: string; category_name: string; category_slug: string }[];
  all: Category[];
}

export default function PreferencesPage() {
  const [data, setData] = useState<PreferenceData | null>(null);
  const [selectedIds, setSelectedIds] = useState<string[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState(false);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<{ data: PreferenceData }>('/api/v1/reader/preferences')
      .then((res) => {
        const d = unwrapData<PreferenceData>(res);
        if (!d) { setLoading(false); return; }
        setData(d);
        setSelectedIds(d.selected.map((p) => p.category_id));
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, []);

  const toggleCategory = (id: string) => {
    setSelectedIds((prev) =>
      prev.includes(id) ? prev.filter((pid) => pid !== id) : [...prev, id]
    );
  };

  const isSelected = (id: string) => selectedIds.includes(id);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setError(null);
    setSuccess(false);
    try {
      const token = authStorage.getToken();
      await apiClient.put(
        '/api/v1/reader/preferences',
        { category_ids: selectedIds },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setSuccess(true);
} catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to save preferences');
      } finally {
        setSaving(false);
      }
    };

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <div className="text-center">
          <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-6">
            Loading Preferences…
          </h1>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-6">
          Error
        </h1>
        <p className="text-charcoal/70">{error}</p>
      </div>
    );
  }

  return (
    <div className="max-w-2xl mx-auto py-12 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          Reading Preferences
        </h1>
        <p className="text-charcoal/70">
          Select the categories you&apos;re interested in. We&apos;ll use these to
          personalize your homepage and recommendations.
        </p>
      </div>

      {success && (
        <div className="p-3 bg-green-50 border border-green-200 text-green-700 rounded text-sm">
          Preferences saved successfully!
        </div>
      )}

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      <form onSubmit={handleSave} className="space-y-4 mylo-card p-6">
        <div className="grid md:grid-cols-3 gap-4">
          {data!.all.map((cat) => (
            <label
              key={cat.id}
              className={`flex flex-col items-center gap-2 p-4 rounded-lg border ${
                isSelected(cat.id)
                  ? 'border-burgundy-wine bg-burgundy-deep/10'
                  : 'border-burgundy-deep/20 hover:border-burgundy-wine/50 transition-colors'
              } cursor-pointer`}
              onClick={() => toggleCategory(cat.id)}
            >
              <input
                type="checkbox"
                checked={isSelected(cat.id)}
                onChange={() => toggleCategory(cat.id)}
                className="w-5 h-5 text-burgundy-wine rounded border-burgundy-deep/30 focus:ring-2 focus:ring-burgundy-wine"
                readOnly
              />
              <span className="text-sm font-medium text-charcoal text-center">
                {cat.name}
              </span>
              {cat.is_featured && (
                <span className="text-[10px] text-burgundy-wine font-bold uppercase tracking-wider bg-champagne/20 px-2 py-0.5 rounded">
                  Featured
                </span>
              )}
            </label>
          ))}
        </div>

        <button
          type="submit"
          disabled={saving}
          className="mylo-btn w-full justify-center"
        >
          {saving ? 'Saving…' : 'Save Preferences'}
        </button>
      </form>

      <div className="text-center">
        <Link
          href="/"
          className="text-burgundy-wine hover:underline text-sm"
        >
          ← Back to homepage
        </Link>
      </div>
    </div>
  );
}
