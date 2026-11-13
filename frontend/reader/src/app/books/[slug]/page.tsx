'use client';

import { useEffect, useState, use } from 'react';
import Link from 'next/link';
import { apiClient, unwrapData } from '@/lib/api';
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

export default function BookDetailPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = use(params);

  const [book, setBook] = useState<Book | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [adding, setAdding] = useState(false);
  const [notice, setNotice] = useState<string | null>(null);
  const [cartError, setCartError] = useState<string | null>(null);

  useEffect(() => {
    apiClient
      .get<{ data: Book }>('/api/v1/books/' + slug)
      .then((res) => {
        setBook(unwrapData<Book>(res));
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  }, [slug]);

  const addToCart = async () => {
    if (!book) return;
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }
    setAdding(true);
    setCartError(null);
    setNotice(null);
    try {
      await apiClient.post('/api/v1/cart/items', {
        book_id: book.id,
        quantity: 1,
      });
      setNotice('Added to your cart.');
    } catch (err) {
      setCartError(err instanceof Error ? err.message : 'Failed to add to cart');
    } finally {
      setAdding(false);
    }
  };

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
          className="mt-4 inline-block mylo-btn"
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

          {cartError && (
            <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
              {cartError}
            </div>
          )}

          {notice && (
            <div className="p-3 bg-green-50 border border-green-200 text-green-700 rounded text-sm">
              {notice}{' '}
              <Link href="/cart" className="font-medium underline">
                Go to cart →
              </Link>
            </div>
          )}

          {book.is_available && (
            <button
              onClick={addToCart}
              disabled={adding}
              className="mylo-btn w-full justify-center"
            >
              {adding ? 'Adding…' : book.product_type === 'ebook' ? 'Add Ebook to Cart' : 'Add to Cart'}
            </button>
          )}
        </div>
      </div>
    </div>
  );
}