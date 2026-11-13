'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient } from '@/lib/api';
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
  writer: { display_name?: string; email: string };
  product_category?: { name: string };
}

export default function BookDetailPage({ params }: { params: { slug: string } }) {
  const [book, setBook] = useState<Book | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    apiClient
      .get<{ data: Book }>('/api/v1/books/' + params.slug)
      .then((res) => {
        const d = res.data as { data: Book };
        setBook(d.data);
        setLoading(false);
      })
      .catch((err) => setError(err.message));
  }, [params.slug]);

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading…
        </h1>
      </div>
    );
  }

  if (error || !book) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Book not found
        </h1>
        <p className="text-charcoal/70 text-center">{error}</p>
        <Link
          href="/books"
          className="mt-4 inline-block px-6 py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors"
        >
          Browse books
        </Link>
      </div>
    );
  }

  return (
    <div className="max-w-2xl mx-auto py-12 px-4 space-y-8">
      <div className="flex items-start gap-6">
        {book.cover_image && (
          <img
            src={book.cover_image}
            alt={book.title}
            className="w-full h-64 object-cover rounded-lg"
          />
        )}
        <div className="flex-1">
          {book.product_category?.name && (
            <span className="text-[10px] text-burgundy-wine font-bold uppercase tracking-wider block mb-2">
              {book.product_category.name}
            </span>
          )}
          <h1 className="text-4xl font-serif font-bold text-burgundy-deep mb-4">
            {book.title}
          </h1>
          {book.author && (
            <p className="text-charcoal/70 text-lg mb-4">
              by {book.author}
            </p>
          )}
          {book.description && (
            <p className="text-charcoal/70 leading-relaxed mb-6">
              {book.description}
            </p>
          )}
          <div className="flex items-center gap-4 mb-6">
            <span className="text-3xl font-bold text-burgundy-deep">
              ${book.price}
            </span>
            <span className="text-xs text-charcoal/60 bg-ivory-soft px-2 py-1 rounded">
              {book.product_type}
            </span>
            <span className={`px-2 py-1 rounded text-xs font-medium ${
              book.is_available
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700'
            }`}>
              {book.is_available ? 'In Stock' : 'Out of Stock'}
            </span>
          </div>

          {book.is_available && book.product_type === 'ebook' && (
            <button
              className="w-full py-4 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors"
            >
              Buy Ebook
            </button>
          )}

          {book.is_available && book.product_type === 'physical' && (
            <button
              className="w-full py-4 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors"
            >
              Buy Physical Book
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
