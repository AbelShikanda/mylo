'use client';

import { useState, useEffect, use } from 'react';
import Link from 'next/link';
import { apiClient, unwrapData } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Writer {
  id: string;
  display_name?: string;
  email: string;
}

export default function SupportPage({ params }: { params: Promise<{ writerId: string }> }) {
  const { writerId } = use(params);
  const [writer, setWriter] = useState<Writer | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [amount, setAmount] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [success, setSuccess] = useState(false);

  useEffect(() => {
    apiClient
      .get<{ data: Writer }>(`/api/v1/writer/profile`)
      .then((res) => {
        setWriter(unwrapData<Writer>(res));
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, []);

  const supportWriter = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setError(null);
    setSuccess(false);
    try {
      const token = authStorage.getToken();
      await apiClient.post(
        '/api/v1/reader/contributions',
        {
          writer_id: writerId,
          amount: parseFloat(amount),
          currency: 'KES',
          type: 'one-time',
        },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setSuccess(true);
} catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to process contribution');
      } finally {
      setSubmitting(false);
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

  return (
    <div className="max-w-2xl mx-auto py-12 px-4 space-y-8">
      <div className="flex items-center justify-between">
        <Link href="/" className="text-burgundy-wine hover:underline">
          ← Back to homepage
        </Link>
        <h1 className="text-3xl font-serif font-bold text-burgundy-deep">
          Support Writer
        </h1>
      </div>

      {error && (
        <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
          {error}
        </div>
      )}

      {success && (
        <div className="p-3 bg-green-50 border border-green-200 text-green-700 rounded text-sm">
          Thank you for your contribution!
        </div>
      )}

      {writer && (
        <div className="mylo-card p-6">
          <div className="flex items-center gap-4 mb-6">
            <div className="w-16 h-16 rounded-full bg-burgundy-deep/20 flex items-center justify-center">
              <span className="text-burgundy-deep font-bold text-xl">{writer.display_name?.charAt(0) || 'W'}</span>
            </div>
            <div>
              <h2 className="font-serif font-bold text-burgundy-deep text-xl">
                {writer.display_name || writer.email}
              </h2>
              <p className="text-charcoal/60 text-sm">{writer.email}</p>
            </div>
          </div>

          <form onSubmit={supportWriter} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-charcoal mb-1">
                Contribution Amount (KES)
              </label>
              <input
                name="amount"
                type="number"
                step="0.01"
                min="1"
                value={amount}
                onChange={(e) => setAmount(e.target.value)}
                className="mylo-input"
                placeholder="100.00"
                required
              />
            </div>
            <button
              type="submit"
              disabled={submitting}
              className="mylo-btn w-full justify-center"
            >
              {submitting ? 'Processing…' : 'Contribute'}
            </button>
          </form>
        </div>
      )}

      {!writer && !error && !success && (
        <div className="text-center py-16 text-charcoal/70">
          <p className="text-lg">Writer not found.</p>
        </div>
      )}
    </div>
  );
}
