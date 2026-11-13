'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface LibraryItem {
  id: string;
  book?: {
    title: string;
    slug: string;
    cover_image?: string;
    author?: string;
  };
  title?: string;
  slug?: string;
  cover_image?: string;
  author?: string;
  purchased_at?: string;
  created_at?: string;
}

export default function LibraryPage() {
  const [items, setItems] = useState<LibraryItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/reader/library')
      .then((res) => setItems(unwrapList<LibraryItem>(res)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="max-w-3xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Your Library…
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-3xl mx-auto py-12 px-4 space-y-8">
      <div>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          My Library
        </h1>
        <p className="text-charcoal/70">
          Ebooks and titles you have purchased.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {items.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">Your library is empty.</p>
          <Link
            href="/books"
            className="mt-4 inline-block mylo-btn"
          >
            Browse the bookshop
          </Link>
        </div>
      ) : (
        <div className="grid md:grid-cols-2 gap-6">
          {items.map((item) => {
            const title = item.book?.title ?? item.title ?? 'Untitled';
            const author = item.book?.author ?? item.author;
            const cover = item.book?.cover_image ?? item.cover_image;
            return (
              <div
                key={item.id}
                className="mylo-card p-5 flex gap-4"
              >
                {cover && (
                  <img
                    src={cover}
                    alt={title}
                    className="w-24 h-32 object-cover rounded"
                  />
                )}
                <div>
                  <h2 className="font-serif font-bold text-burgundy-deep text-lg leading-snug">
                    {title}
                  </h2>
                  {author && (
                    <p className="text-charcoal/70 text-sm mt-1">by {author}</p>
                  )}
                  {(item.purchased_at || item.created_at) && (
                    <p className="text-charcoal/50 text-xs mt-3">
                      Purchased{' '}
                      {new Date(item.purchased_at || item.created_at || '').toLocaleDateString()}
                    </p>
                  )}
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}
