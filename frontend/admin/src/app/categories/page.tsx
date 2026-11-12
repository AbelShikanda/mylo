'use client';

import { useEffect, useState } from 'react';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';
import { Category } from '@/types/auth';

export default function CategoriesPage() {
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [newCategory, setNewCategory] = useState({
    name: '',
    slug: '',
    description: '',
  });

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<Category[]>('/api/v1/categories')
      .then((res) => {
        setCategories(res.data);
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, []);

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await apiClient.post('/api/v1/categories', newCategory);
      setNewCategory({ name: '', slug: '', description: '' });
      window.location.reload();
    } catch (err: any) {
      setError(err.message);
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Delete this category?')) return;
    try {
      await apiClient.delete(`/api/v1/categories/${id}`);
      window.location.reload();
    } catch (err: any) {
      setError(err.message);
    }
  };

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-serif font-bold text-champagne mb-6">
          Loading Categories...
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

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-serif font-bold text-champagne mb-2">
          Category Management
        </h1>
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      <div className="bg-burgundy-deep/30 p-6 rounded-lg border border-burgundy-wine/40 mb-6">
        <h3 className="font-serif font-bold text-champagne text-lg mb-4">
          Create Category
        </h3>
        <form onSubmit={handleCreate} className="space-y-4">
          <div className="grid md:grid-cols-3 gap-4">
            <div>
              <label className="block text-xs text-rose-muted mb-1 uppercase">
                Name
              </label>
              <input
                name="name"
                value={newCategory.name}
                onChange={(e) =>
                  setNewCategory({ ...newCategory, name: e.target.value })
                }
                className="w-full px-3 py-2 border border-burgundy-wine/30 rounded bg-charcoal/50 text-champagne focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
                placeholder="Food and Culinary"
              />
            </div>
            <div>
              <label className="block text-xs text-rose-muted mb-1 uppercase">
                Slug
              </label>
              <input
                name="slug"
                value={newCategory.slug}
                onChange={(e) =>
                  setNewCategory({ ...newCategory, slug: e.target.value })
                }
                className="w-full px-3 py-2 border border-burgundy-wine/30 rounded bg-charcoal/50 text-champagne focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
                placeholder="food-culinary"
              />
            </div>
            <div>
              <label className="block text-xs text-rose-muted mb-1 uppercase">
                Description
              </label>
              <input
                name="description"
                value={newCategory.description}
                onChange={(e) =>
                  setNewCategory({
                    ...newCategory,
                    description: e.target.value,
                  })
                }
                className="w-full px-3 py-2 border border-burgundy-wine/30 rounded bg-charcoal/50 text-champagne focus:outline-none focus:ring-2 focus:ring-burgundy-wine"
                placeholder="Category description"
              />
            </div>
          </div>
          <button
            type="submit"
            className="px-4 py-2 bg-burgundy-deep text-white text-sm font-medium rounded hover:bg-burgundy-wine transition-colors"
          >
            Create Category
          </button>
        </form>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b border-burgundy-wine/40 text-rose-muted text-sm uppercase">
              <th className="pb-3">Name</th>
              <th className="pb-3">Slug</th>
              <th className="pb-3">Status</th>
              <th className="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            {categories.map((cat) => (
              <tr key={cat.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne">{cat.name}</td>
                <td className="py-4 text-rose-muted">{cat.slug}</td>
                <td className="py-4">
                  <span className="px-2 py-1 text-xs text-burgundy-wine bg-burgundy-deep/20 rounded">
                    {cat.is_active ? 'Active' : 'Inactive'}
                  </span>
                </td>
                <td className="py-4 text-right">
                  <button
                    onClick={() => handleDelete(cat.id)}
                    className="px-3 py-1 text-xs text-red-400 hover:bg-red-500/20 rounded transition-colors"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {categories.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No categories found.
        </div>
      )}
    </div>
  );
}
