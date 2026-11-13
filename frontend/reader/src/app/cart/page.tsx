'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface CartItem {
  book_id: string;
  title: string;
  slug: string;
  price: string;
  product_type: string;
  cover_image?: string;
  quantity: number;
}

export default function CartPage() {
  const [cart, setCart] = useState<{ items: CartItem[]; total: string } | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get<{ data: { items: CartItem[]; total: string } }>('/api/v1/cart')
      .then((res) => {
        const d = res.data as { data: { items: CartItem[]; total: string } };
        setCart(d.data);
        setLoading(false);
      })
      .catch((err) => setError(err.message));
  }, []);

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep text-center mb-6">
          Loading Cart…
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
          Shopping Cart
        </h1>
        <Link href="/books" className="text-burgundy-wine hover:underline text-sm">
          ← Continue shopping
        </Link>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {cart!.items.length === 0 ? (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">Your cart is empty.</p>
          <Link
            href="/books"
            className="mt-4 inline-block px-6 py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors"
          >
            Browse books
          </Link>
        </div>
      ) : (
        <div className="bg-white p-6 rounded-lg border border-burgundy-deep/20 shadow-sm">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-burgundy-deep/20 text-charcoal/60 text-sm uppercase">
                <th className="pb-3">Book</th>
                <th className="pb-3 text-right">Price</th>
                <th className="pb-3 text-right">Qty</th>
                <th className="pb-3 text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              {cart!.items.map((item) => (
                <tr key={item.book_id} className="border-b border-burgundy-deep/10">
                  <td className="py-4 text-charcoal">{item.title}</td>
                  <td className="py-4 text-right text-charcoal">${item.price}</td>
                  <td className="py-4 text-right text-charcoal">{item.quantity}</td>
                  <td className="py-4 text-right text-charcoal">
                    ${(parseFloat(item.price) * item.quantity).toFixed(2)}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
          <div className="mt-6 flex justify-between border-t border-burgundy-deep/20 pt-4">
            <span className="text-lg font-bold text-charcoal">Total:</span>
            <span className="text-lg font-bold text-burgundy-deep">
              ${cart!.total}
            </span>
          </div>
          <button
            className="w-full py-3 bg-burgundy-deep text-white font-medium rounded-md hover:bg-burgundy-wine transition-colors mt-4"
          >
            Proceed to Checkout
          </button>
        </div>
      )}
    </div>
  );
}
