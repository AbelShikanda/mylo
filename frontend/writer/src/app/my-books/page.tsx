'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Book {
  id: string;
  title: string;
  slug: string;
  description?: string;
  author?: string;
  cover_image?: string;
  price: string;
  product_type: string;
  is_available: boolean;
  inventory_count: number;
  created_at: string;
}

export default function MyBooksPage() {
  const [books, setBooks] = useState<Book[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<{ data: Book[] }>('/api/v1/writer/books')
      .then((res) => {
        setBooks(unwrapList<Book>(res));
        setLoading(false);
      })
      .catch((err) => setError(err.message));
  }, []);

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Books…
        </h1>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Error
        </h1>
        <p className="text-charcoal/70 text-center">{error}</p>
      </div>
    );
  }

  return (
    <div className="max-w-2xl mx-auto py-12 px-4 space-y-8">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-2">
          My Books
        </h1>
        <Link href="/create" className="text-burgundy-wine hover:underline text-sm">
          ← Back to dashboard
        </Link>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {books.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No books yet.</p>
          <Link
            href="/"
            className="mt-4 inline-block mylo-btn"
          >
            Publish your first book
          </Link>
        </div>
      ) : (
        <div className="space-y-6">
          {books.map((book) => (
            <div
              key={book.id}
              className="mylo-card p-6"
            >
              <div className="flex items-start gap-4">
                {book.cover_image && (
                  <img
                    src={book.cover_image}
                    alt={book.title}
                    className="w-32 h-32 object-cover rounded-lg"
                  />
                )}
                <div className="flex-1">
                  <h3 className="font-serif font-bold text-burgundy-deep text-xl mb-2">
                    <Link
                      href={"/books/" + book.slug}
                      className="hover:underline"
                    >
                      {book.title}
                    </Link>
                  </h3>
                  {book.description && (
                    <p className="text-charcoal/70 text-sm mb-3">
                      {book.description}
                    </p>
                  )}
                  <div className="flex items-center gap-3 text-xs text-charcoal/60">
                    <span>{book.author}</span>
                    <span>•</span>
                    <span>{book.product_type}</span>
                    <span>•</span>
                    <span>${book.price}</span>
                  </div>
                  <div className="mt-4 flex gap-2">
                    <span className={`px-2 py-1 rounded text-xs font-medium ${
                      book.is_available
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                    }`}>
                      {book.is_available ? 'Available' : 'Unavailable'}
                    </span>
                    <span className="text-charcoal/70 text-sm">
                      Inventory: {book.inventory_count}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
