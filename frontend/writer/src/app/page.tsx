'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { authStorage } from '@/lib/auth';

export default function Home() {
  const router = useRouter();

  useEffect(() => {
    router.replace(authStorage.getToken() ? '/dashboard' : '/login');
  }, [router]);

  return (
    <div className="flex items-center justify-center py-24">
      <p className="text-charcoal/50 text-sm">Redirecting to your workspace…</p>
    </div>
  );
}
