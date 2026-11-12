'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { authStorage } from '@/lib/auth';

const adminNav = [
  { name: 'Overview', href: '/overview' },
  { name: 'Readers', href: '/readers' },
  { name: 'Writers', href: '/writers' },
  { name: 'Articles', href: '/articles' },
  { name: 'Editorial Management', href: '/editorial' },
  { name: 'Categories and Tags', href: '/categories' },
  { name: 'Comments and Moderation', href: '/comments' },
  { name: 'Media Library', href: '/media' },
  { name: 'Books and Marketplace', href: '/books' },
  { name: 'Orders', href: '/orders' },
  { name: 'Financial Management', href: '/finance' },
  { name: 'Transactions', href: '/transactions' },
  { name: 'Payouts', href: '/payouts' },
  { name: 'Payment Gateways', href: '/payment-gateways' },
  { name: 'Homepage Management', href: '/homepage' },
  { name: 'Notifications', href: '/notifications' },
  { name: 'Analytics and Reports', href: '/analytics' },
  { name: 'Administrator Accounts', href: '/administrators' },
  { name: 'Roles and Permissions', href: '/roles' },
  { name: 'Audit Logs', href: '/audit-logs' },
  { name: 'Platform Settings', href: '/settings' },
];

export function AdminNavigation({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const token = authStorage.getToken();

  return (
    <div className="flex flex-col h-screen">
      <aside className="hidden md:flex flex-col w-64 border-r border-burgundy-deep/30 bg-charcoal text-ivory-soft shrink-0">
        <div className="p-6">
          <div className="flex items-center gap-2 mb-8">
            <span className="text-champagne font-serif text-xl font-bold">Mylo</span>
            <span className="text-rose-muted font-serif text-xs tracking-wider uppercase">Admin</span>
          </div>
          <nav className="space-y-0.5">
            {adminNav.map((item) => (
              <Link
                key={item.name}
                href={item.href}
                className={`block px-4 py-3 text-sm font-medium transition-colors rounded-lg ${
                  pathname === item.href
                    ? 'bg-burgundy-deep text-white'
                    : 'text-rose-muted hover:text-champagne hover:bg-burgundy-deep/20'
                }`}
              >
                {item.name}
              </Link>
            ))}
          </nav>
        </div>
        <div className="p-6 mt-auto border-t border-burgundy-deep/30">
          <button
            onClick={() => {
              authStorage.removeToken();
              window.location.href = '/login';
            }}
            className="w-full px-4 py-2 text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors rounded-lg"
          >
            Sign out
          </button>
        </div>
      </aside>

      <div className="flex-1 overflow-hidden flex flex-col">
        <header className="h-16 border-b border-burgundy-deep/30 bg-charcoal/50 backdrop-blur flex items-center justify-between px-8 z-10 shrink-0">
          <div className="flex items-center gap-2">
            <span className="text-burgundy-deep font-serif text-xl font-bold">Mylo</span>
            <span className="text-rose-muted font-serif text-xs font-bold tracking-wider uppercase">Administrator Portal</span>
          </div>
          <div className="flex items-center gap-4">
            <span className="text-sm text-charcoal/400 font-medium">Platform Control Centre</span>
          </div>
        </header>

        <main className="flex-1 overflow-y-auto p-8">
          {children}
        </main>
      </div>
    </div>
  );
}
