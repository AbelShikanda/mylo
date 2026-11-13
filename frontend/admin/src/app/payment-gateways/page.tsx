'use client';

import { useEffect, useState } from 'react';
import { apiClient, unwrapList } from '@/lib/api';
import { authStorage } from '@/lib/auth';

interface Gateway {
  name: string;
  slug: string;
  enabled: boolean;
  sandbox: boolean;
}

export default function PaymentGatewaysPage() {
  const [gateways, setGateways] = useState<Gateway[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [notice, setNotice] = useState<string | null>(null);

  useEffect(() => {
    const token = authStorage.getToken();
    if (!token) {
      window.location.href = '/login';
      return;
    }

    apiClient
      .get('/api/v1/admin/payment-gateways')
      .then((res) => setGateways(unwrapList<Gateway>(res)))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  const toggle = async (gateway: Gateway, field: 'enabled' | 'sandbox') => {
    const updated = { ...gateway, [field]: !gateway[field] };
    try {
      await apiClient.put(`/api/v1/admin/payment-gateways/${gateway.slug}`, {
        enabled: updated.enabled,
        sandbox: updated.sandbox,
      });
      setGateways(gateways.map((g) => (g.slug === gateway.slug ? updated : g)));
      setNotice(`${gateway.name} updated.`);
      setTimeout(() => setNotice(null), 3000);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Something went wrong');
    }
  };

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto py-8 px-4">
        <h1 className="text-3xl font-display font-bold text-champagne mb-6">
          Loading Payment Gateways...
        </h1>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto py-8 px-4 space-y-6">
      <div>
        <h1 className="text-3xl font-display font-bold text-champagne mb-2">
          Payment Gateways
        </h1>
        <p className="text-rose-muted">
          Enable, disable and sandbox payment providers.
        </p>
      </div>

      {error && (
        <div className="p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded text-sm">
          {error}
        </div>
      )}
      {notice && (
        <div className="p-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded text-sm">
          {notice}
        </div>
      )}

      <div className="space-y-4">
        {gateways.map((gateway) => (
          <div
            key={gateway.slug}
            className="mylo-panel p-6 flex items-center justify-between gap-6 flex-wrap"
          >
            <div>
              <h3 className="font-display font-bold text-champagne text-xl">
                {gateway.name}
              </h3>
              <p className="text-xs text-rose-muted uppercase tracking-wide mt-1">
                {gateway.slug}
              </p>
            </div>
            <div className="flex items-center gap-6">
              <label className="flex items-center gap-2 text-sm text-rose-muted cursor-pointer">
                <input
                  type="checkbox"
                  checked={gateway.enabled}
                  onChange={() => toggle(gateway, 'enabled')}
                  className="accent-burgundy-wine"
                />
                Enabled
              </label>
              <label className="flex items-center gap-2 text-sm text-rose-muted cursor-pointer">
                <input
                  type="checkbox"
                  checked={gateway.sandbox}
                  onChange={() => toggle(gateway, 'sandbox')}
                  className="accent-burgundy-wine"
                />
                Sandbox
              </label>
            </div>
          </div>
        ))}
        {gateways.length === 0 && (
          <div className="text-center py-8 text-rose-muted">
            No payment gateways configured.
          </div>
        )}
      </div>
    </div>
  );
}
