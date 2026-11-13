'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { apiClient, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface CartItem {
  id: string;
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
  const [checkingOut, setCheckingOut] = useState(false);
  const [orderPlaced, setOrderPlaced] = useState(false);

  const loadCart = () => {
    apiClient
      .get<{ data: { items: CartItem[]; total: string } }>('/api/v1/cart')
      .then((res) => {
        setCart(unwrapData<{ items: CartItem[]; total: string }>(res) ?? { items: [], total: '0.00' });
        setLoading(false);
      })
      .catch((err) => setError(err.message));
  };

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }
    loadCart();
  }, []);

  const removeItem = async (itemId: string) => {
    try {
      await apiClient.delete(`/api/v1/cart/items/${itemId}`);
      loadCart();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to remove item');
    }
  };

  const checkout = async () => {
    setCheckingOut(true);
    setError(null);
    try {
      await apiClient.post('/api/v1/checkout', {});
      setOrderPlaced(true);
      setCart({ items: [], total: '0.00' });
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Checkout failed');
    } finally {
      setCheckingOut(false);
    }
  };

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

  if (orderPlaced) {
    return (
      <div className="max-w-2xl mx-auto py-20 px-4 text-center">
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep mb-4">
          Order placed!
        </h1>
        <p className="text-charcoal/70 mb-6">
          Your order has been created. Ebook purchases are available in your library.
        </p>
        <div className="flex items-center justify-center gap-4">
          <Link
            href="/library"
            className="mylo-btn"
          >
            Go to My Library
          </Link>
          <Link href="/books" className="text-burgundy-wine hover:underline">
            Continue shopping
          </Link>
        </div>
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
            className="mt-4 inline-block mylo-btn"
          >
            Browse books
          </Link>
        </div>
      ) : (
        <div className="mylo-card p-6">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-burgundy-deep/20 text-charcoal/60 text-sm uppercase">
                <th className="pb-3">Book</th>
                <th className="pb-3 text-right">Price</th>
                <th className="pb-3 text-right">Qty</th>
                <th className="pb-3 text-right">Total</th>
                <th className="pb-3"></th>
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
                  <td className="py-4 pl-4 text-right">
                    <button
                      onClick={() => removeItem(item.id)}
                      className="text-red-600 hover:underline text-sm"
                    >
                      Remove
                    </button>
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
            onClick={checkout}
            disabled={checkingOut}
            className="mylo-btn w-full justify-center mt-4"
          >
            {checkingOut ? 'Processing…' : 'Place Order'}
          </button>
        </div>
      )}
    </div>
  );
}
