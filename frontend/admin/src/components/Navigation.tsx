'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useState } from 'react';
import { authStorage } from '@/lib/auth';

interface NavItem {
  name: string;
  href: string;
  icon: string;
}

const icon = (path: string) => (
  <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden>
    {path.split('|').map((d, i) => (
      <path key={i} d={d} />
    ))}
  </svg>
);

const NAV_GROUPS: { group: string; items: NavItem[] }[] = [
  {
    group: 'Command',
    items: [{ name: 'Overview', href: '/overview', icon: 'M3 12l9-9 9 9|M5 10v10a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V10' }],
  },
  {
    group: 'People',
    items: [
      { name: 'Readers', href: '/readers', icon: 'M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2|M19.5 7.5a3 3 0 1 1-3-3|M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8' },
      { name: 'Writers', href: '/writers', icon: 'M12 20h9|M16.5 3.5a2.121 2.121 0 0 1 3 3L10 16l-4 1 1-4L16.5 3.5' },
      { name: 'Administrators', href: '/administrators', icon: 'M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z' },
    ],
  },
  {
    group: 'Content',
    items: [
      { name: 'Articles', href: '/articles', icon: 'M6 3h9l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1|M9 12h6|M9 16h6|M9 8h2' },
      { name: 'Editorial', href: '/editorial', icon: 'M12 6v6l4 2|M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20' },
      { name: 'Comments', href: '/comments', icon: 'M21 12a8 8 0 0 1-8 8H5l-3 3V12a8 8 0 0 1 8-8h3a8 8 0 0 1 8 8' },
      { name: 'Media Library', href: '/media', icon: 'M3 5a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5|M8.5 10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3|M21 15l-5-5L5 21' },
      { name: 'Categories & Tags', href: '/categories', icon: 'M20.59 13.41L11 3.83A2 2 0 0 0 9.59 3H4a1 1 0 0 0-1 1v5.59a2 2 0 0 0 .59 1.42l9.58 9.59a2 2 0 0 0 2.83 0l4.59-4.59a2 2 0 0 0 0-2.6' },
    ],
  },
  {
    group: 'Marketplace',
    items: [
      { name: 'Books', href: '/books', icon: 'M4 5a2 2 0 0 1 2-2h13a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a2 2 0 0 1-2-2V5|M8 7h9|M8 11h9' },
      { name: 'Orders', href: '/orders', icon: 'M6 6h15l-1.5 9a2 2 0 0 1-2 1.7H8.4a2 2 0 0 1-2-1.6L5 4H2|M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2|M16 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2' },
    ],
  },
  {
    group: 'Finance',
    items: [
      { name: 'Financial Overview', href: '/finance', icon: 'M3 17l6-6 4 4 8-8|M21 7v6h-6' },
      { name: 'Transactions', href: '/transactions', icon: 'M8 7h13|M8 12h13|M8 17h13|M3 7h.01|M3 12h.01|M3 17h.01' },
      { name: 'Payouts', href: '/payouts', icon: 'M12 1v22|M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6' },
      { name: 'Payment Gateways', href: '/payment-gateways', icon: 'M2 8h20M2 12h20|M3 6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6' },
    ],
  },
  {
    group: 'Platform',
    items: [
      { name: 'Homepage', href: '/homepage', icon: 'M3 12l9-9 9 9|M9 21V12h6v9|M4 10v11a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V10' },
      { name: 'Notifications', href: '/notifications', icon: 'M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9|M13.7 21a2 2 0 0 1-3.4 0' },
      { name: 'Analytics', href: '/analytics', icon: 'M18 20V10|M12 20V4|M6 20v-6' },
      { name: 'Audit Logs', href: '/audit-logs', icon: 'M9 12l2 2 4-5|M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20' },
      { name: 'Roles & Permissions', href: '/roles', icon: 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6|M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z' },
      { name: 'Permissions', href: '/permissions', icon: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10' },
      { name: 'Platform Settings', href: '/settings', icon: 'M4 21v-7|M4 10V3|M12 21v-9|M12 8V3|M20 21v-5|M20 12V3|M1 14h6|M9 8h6|M17 16h6' },
    ],
  },
];

export function AdminNavigation({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const [menuOpen, setMenuOpen] = useState(false);

  if (pathname === '/login' || pathname === '/') {
    return <>{children}</>;
  }

  const signOut = () => {
    authStorage.removeToken();
    window.location.href = '/login';
  };

  const nav = (
    <nav aria-label="Admin sections" className="space-y-0.5">
      {NAV_GROUPS.map(({ group, items }) => (
        <div key={group}>
          <p className="admin-nav-group">{group}</p>
          {items.map((item) => (
            <Link
              key={item.name}
              href={item.href}
              onClick={() => setMenuOpen(false)}
              aria-current={pathname === item.href ? 'page' : undefined}
              className={`admin-nav-item ${pathname === item.href ? 'active' : ''}`}
            >
              {icon(item.icon)}
              {item.name}
            </Link>
          ))}
        </div>
      ))}
    </nav>
  );

  return (
    <div className="flex h-screen overflow-hidden bg-abyss">
      <aside className="hidden lg:flex flex-col w-[17rem] shrink-0 border-r border-white/5 bg-[#161014]">
        <div className="flex items-center gap-2.5 px-5 h-16 border-b border-white/5 shrink-0">
          <div className="h-8 w-8 rounded-lg bg-gradient-to-br from-champagne to-[#8a6a2f] flex items-center justify-center shadow-[0_4px_14px_-4px_rgba(216,189,156,0.4)]">
            <span className="font-display text-base font-black text-[#241720]">M</span>
          </div>
          <div className="leading-tight">
            <p className="font-display text-lg font-bold text-champagne tracking-tight">Mylo</p>
            <p className="text-[0.55rem] uppercase tracking-[0.24em] text-grey-muted">Admin Console</p>
          </div>
        </div>
        <div className="flex-1 overflow-y-auto px-3 py-3 scrollbar-thin">
          {nav}
        </div>
        <div className="p-4 border-t border-white/5">
          <button
            onClick={signOut}
            className="flex items-center gap-2 w-full px-3 py-2.5 text-sm font-medium text-[#E39BB4]/80 hover:text-[#F46F6F] hover:bg-red-500/5 rounded-lg transition-colors"
          >
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden>
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4|M16 17l5-5-5-5|M21 12H9" />
            </svg>
            Sign out
          </button>
        </div>
      </aside>

      <div className="flex-1 overflow-hidden flex flex-col">
        <header className="h-16 border-b border-white/5 bg-[#161014]/80 backdrop-blur flex items-center justify-between px-4 md:px-6 shrink-0">
          <div className="flex items-center gap-3">
            <button
              className="lg:hidden p-2 text-champagne"
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
            <div className="lg:hidden flex items-center gap-2">
              <span className="font-display text-lg font-bold text-champagne">Mylo</span>
              <span className="text-[0.6rem] uppercase tracking-[0.22em] text-grey-muted">Admin</span>
            </div>
          </div>
          <span className="hidden md:inline-flex items-center gap-2 text-xs text-rose-muted border border-white/5 rounded-full pl-2 pr-3 py-1.5 bg-white/[0.02]">
            <span className="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.7)]" />
            Platform Control Centre
          </span>
        </header>

        {menuOpen && (
          <div className="lg:hidden border-b border-white/5 bg-[#161014] px-3 py-3 max-h-[70vh] overflow-y-auto">
            {nav}
            <button
              onClick={signOut}
              className="flex items-center gap-2 w-full px-3 py-2.5 mt-2 text-sm font-medium text-[#E39BB4] border-t border-white/5 pt-3"
            >
              Sign out
            </button>
          </div>
        )}

        <main className="flex-1 overflow-y-auto p-4 md:p-7">
          {children}
        </main>
      </div>
    </div>
  );
}
