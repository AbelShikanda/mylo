'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useState } from 'react';
import { authStorage } from '@/lib/auth';

const writerNav = [
  { name: 'Dashboard', href: '/dashboard' },
  { name: 'My Articles', href: '/articles' },
  { name: 'Create', href: '/create' },
  { name: 'Drafts', href: '/drafts' },
  { name: 'Scheduled', href: '/scheduled' },
  { name: 'Media Library', href: '/media' },
  { name: 'My Books', href: '/my-books' },
  { name: 'Comments', href: '/comments' },
  { name: 'Support', href: '/support' },
  { name: 'Earnings', href: '/earnings' },
  { name: 'Analytics', href: '/analytics' },
  { name: 'Settings', href: '/settings' },
];

export function WriterNavigation() {
  const pathname = usePathname();
  const token = authStorage.getToken();
  const [menuOpen, setMenuOpen] = useState(false);

  const signOut = () => {
    authStorage.removeToken();
    window.location.href = '/login';
  };

  return (
    <header className="sticky top-0 z-50 border-b border-burgundy-deep/10 bg-ivory-soft/85 backdrop-blur-xl">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">
        <div className="flex items-center justify-between h-16 gap-4">
          <Link href="/dashboard" className="flex items-baseline gap-2 sm:gap-3 shrink-0 group no-underline">
            <span className="font-display text-2xl sm:text-[1.75rem] font-black text-burgundy-deep tracking-tight group-hover:text-burgundy-wine transition-colors">
              Mylo
            </span>
            <span className="eyebrow !text-[0.55rem] hidden sm:inline">
              Writer Studio
            </span>
          </Link>

          <div className="flex items-center gap-2 sm:gap-3 shrink-0">
            {token ? (
              <>
                <Link
                  href="/settings"
                  className="hidden sm:inline-flex px-3 py-2 text-sm font-medium text-charcoal hover:text-burgundy-wine transition-colors no-underline"
                >
                  Settings
                </Link>
                <Link href="/create" className="mylo-btn !py-2 !px-4 sm:!px-5 !text-xs sm:!text-sm">
                  + Write
                </Link>
                <button
                  onClick={signOut}
                  className="px-3 py-2 text-sm font-medium text-burgundy-wine hover:bg-burgundy-wine/10 rounded-full transition-colors"
                >
                  Sign out
                </button>
              </>
            ) : (
              <>
                <Link
                  href="/login"
                  className="px-3 py-2 text-sm font-medium text-charcoal hover:text-burgundy-wine transition-colors no-underline"
                >
                  Sign in
                </Link>
                <Link href="/register" className="mylo-btn !py-2 !px-4 sm:!px-5 !text-xs sm:!text-sm">
                  Register
                </Link>
              </>
            )}
            <button
              className="md:hidden p-2 text-burgundy-deep"
              aria-label="Toggle navigation"
              aria-expanded={menuOpen}
              onClick={() => setMenuOpen(!menuOpen)}
            >
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round">
                {menuOpen ? (
                  <>
                    <path d="M6 6l12 12" />
                    <path d="M6 18L18 6" />
                  </>
                ) : (
                  <>
                    <path d="M4 7h16" />
                    <path d="M4 12h16" />
                    <path d="M4 17h16" />
                  </>
                )}
              </svg>
            </button>
          </div>
        </div>

        <nav
          aria-label="Writer sections"
          className="hidden md:flex items-center gap-1 overflow-x-auto pb-2 -mx-2 px-2 scrollbar-none"
        >
          {writerNav.map((item) => {
            const active = pathname === item.href;
            return (
              <Link
                key={item.name}
                href={item.href}
                aria-current={active ? 'page' : undefined}
                className={`whitespace-nowrap px-4 py-2 text-[0.8rem] font-medium rounded-full transition-all duration-300 no-underline ${
                  active
                    ? 'bg-burgundy-deep text-ivory-soft shadow-[0_6px_16px_-6px_rgba(58,14,30,0.55)]'
                    : 'text-charcoal/80 hover:text-burgundy-deep hover:bg-burgundy-deep/5'
                }`}
              >
                {item.name}
              </Link>
            );
          })}
        </nav>
      </div>

      {menuOpen && (
        <div className="md:hidden border-t border-burgundy-deep/10 bg-ivory-soft px-4 py-3">
          <nav className="grid grid-cols-2 gap-1" aria-label="Writer sections">
            {writerNav.map((item) => {
              const active = pathname === item.href;
              return (
                <Link
                  key={item.name}
                  href={item.href}
                  onClick={() => setMenuOpen(false)}
                  className={`px-4 py-2.5 text-sm rounded-xl transition-colors no-underline ${
                    active ? 'bg-burgundy-deep text-ivory-soft' : 'text-charcoal hover:bg-burgundy-deep/5'
                  }`}
                >
                  {item.name}
                </Link>
              );
            })}
          </nav>
        </div>
      )}
    </header>
  );
}
