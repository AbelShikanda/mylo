'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { authStorage } from '@/lib/auth';

const writerNav = [
  { name: 'Dashboard', href: '/dashboard' },
  { name: 'My Articles', href: '/articles' },
  { name: 'Create Article', href: '/create' },
  { name: 'Drafts', href: '/drafts' },
  { name: 'Scheduled', href: '/scheduled' },
  { name: 'Media Library', href: '/media' },
  { name: 'My Books', href: '/books' },
  { name: 'Comments', href: '/comments' },
  { name: 'Financial Support', href: '/support' },
  { name: 'Earnings', href: '/earnings' },
  { name: 'Analytics', href: '/analytics' },
  { name: 'Settings', href: '/settings' },
];

export function WriterNavigation() {
  const pathname = usePathname();
  const token = authStorage.getToken();

  return (
    <nav className="border-b border-burgundy-deep/20 bg-ivory-soft/80 backdrop-blur sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          <Link href="/dashboard" className="flex items-center gap-2">
            <span className="text-burgundy-deep font-serif text-xl font-bold">Mylo</span>
            <span className="text-charcoal font-serif text-sm">Writer Portal</span>
          </Link>

          <div className="flex items-center gap-1">
            {token ? (
              <>
                <Link href="/settings" className="px-4 py-2 text-sm font-medium text-charcoal hover:text-burgundy-wine transition-colors">
                  Settings
                </Link>
                <button
                  onClick={() => {
                    authStorage.removeToken();
                    window.location.href = '/login';
                  }}
                  className="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors rounded-md"
                >
                  Sign out
                </button>
              </>
            ) : (
              <>
                <Link href="/login" className="px-4 py-2 text-sm font-medium text-charcoal hover:text-burgundy-wine transition-colors">
                  Sign in
                </Link>
                <Link
                  href="/register"
                  className="px-4 py-2 text-sm font-medium text-white bg-burgundy-deep hover:bg-burgundy-wine transition-colors rounded-md"
                >
                  Register
                </Link>
              </>
            )}
          </div>
        </div>

        <div className="flex gap-1 overflow-x-auto pb-2 scrollbar-hide">
          {writerNav.map((item) => (
            <Link
              key={item.name}
              href={item.href}
              className={`px-5 py-2 text-sm font-medium whitespace-nowrap transition-colors ${
                pathname === item.href
                  ? 'bg-burgundy-deep text-white rounded-md'
                  : 'text-charcoal hover:text-burgundy-wine'
              }`}
            >
              {item.name}
            </Link>
          ))}
        </div>
      </div>
    </nav>
  );
}
