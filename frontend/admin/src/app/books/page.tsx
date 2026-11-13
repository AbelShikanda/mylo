'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Book {
  id: string;
  title: string;
  slug: string;
  price: string;
  product_type: string;
  is_available: boolean;
  created_at: string;
  writer?: { display_name?: string; email: string };
}

export default function AdminBooksPage() {
  const [books, setBooks] = useState<Book[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [search, setSearch] = useState('');

  const load = () => {
    const qs = search ? `?search=${encodeURIComponent(search)}` : '';
    apiClient
      .get(`/api/v1/admin/books${qs}`)
      .then((res) => setBooks(unwrapList<Book>(res)))
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
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  if (loading) {
    return (
      <div className="max-w-6xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Books...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto py-8 px-4 space-y-6">
      <div className="flex items-center justify-between gap-4 flex-wrap">
        <h1 className="text-3xl font-display font-bold text-champagne">
          Books and Marketplace
        </h1>
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          onKeyDown={(e) => e.key === 'Enter' && load()}
          placeholder="Search title…"
          className="mylo-input mylo-input-inline text-sm"
        />
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}

      <div className="overflow-x-auto">
        <table className="mylo-table">
          <thead>
            <tr className="border-b border-burgundy-wine/40 text-rose-muted text-sm uppercase">
              <th className="pb-3">Title</th>
              <th className="pb-3">Writer</th>
              <th className="pb-3">Type</th>
              <th className="pb-3">Price</th>
              <th className="pb-3">Availability</th>
            </tr>
          </thead>
          <tbody>
            {books.map((book) => (
              <tr key={book.id} className="border-b border-charcoal/30 hover:bg-burgundy-deep/10 transition-colors">
                <td className="py-4 text-champagne">{book.title}</td>
                <td className="py-4 text-rose-muted">
                  {book.writer?.display_name || book.writer?.email || '—'}
                </td>
                <td className="py-4 text-rose-muted">{book.product_type}</td>
                <td className="py-4 text-rose-muted">${book.price}</td>
                <td className="py-4">
                  <span className={`px-2 py-1 text-xs rounded ${book.is_available ? 'text-green-400 bg-green-500/10' : 'text-red-400 bg-red-500/10'}`}>
                    {book.is_available ? 'Available' : 'Unavailable'}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {books.length === 0 && (
        <div className="text-center py-8 text-rose-muted">
          No books found.
        </div>
      )}
    </div>
  );
}
