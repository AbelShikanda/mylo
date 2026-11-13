'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapList } from '@/lib/api';

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
  writer: { display_name?: string; email: string };
  product_category?: { name: string };
}

export default function BooksPage() {
  const [books, setBooks] = useState<Book[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    apiClient
      .get<{ data: Book[] }>('/api/v1/books')
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
          Books & Ebooks Marketplace
        </h1>
        <Link href="/" className="text-burgundy-wine hover:underline text-sm">
          ← Back to homepage
        </Link>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {books.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">No books available yet.</p>
        </div>
      ) : (
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {books.map((book) => (
            <div
              key={book.id}
              className="mylo-card p-6"
            >
              {book.cover_image && (
                <img
                  src={book.cover_image}
                  alt={book.title}
                  className="w-full h-48 object-cover rounded-lg mb-4"
                />
              )}
              <div className="flex-1">
                {book.product_category?.name && (
                  <span className="text-[10px] text-burgundy-wine font-bold uppercase tracking-wider block mb-2">
                    {book.product_category.name}
                  </span>
                )}
                <h3 className="font-serif font-bold text-burgundy-deep text-lg mb-2">
                  <Link
                    href={"/books/" + book.slug}
                    className="hover:underline"
                  >
                    {book.title}
                  </Link>
                </h3>
                {book.author && (
                  <p className="text-charcoal/70 text-sm mb-2">
                    by {book.author}
                  </p>
                )}
                <div className="flex items-center gap-2 mb-4">
                  <span className="text-lg font-bold text-burgundy-deep">
                    ${book.price}
                  </span>
                  <span className="text-xs text-charcoal/60">
                    {book.product_type}
                  </span>
                </div>
                <Link
                  href={"/books/" + book.slug}
                  className="w-full py-2 bg-burgundy-deep text-white text-center text-sm font-medium rounded-md hover:bg-burgundy-wine transition-colors"
                >
                  View Details
                </Link>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
