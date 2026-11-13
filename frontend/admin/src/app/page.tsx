'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { authStorage } from '@/lib/auth';

export default function Home() {
  const router = useRouter();

  useEffect(() => {
    router.replace(authStorage.getToken() ? '/overview' : '/login');
  }, [router]);

  return (
    <div className="flex items-center justify-center h-full">
      <p className="text-rose-muted text-sm">Redirecting…</p>
    </div>
  );
}
