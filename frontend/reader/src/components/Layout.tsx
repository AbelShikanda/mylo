import { ReaderNavigation } from '@/components/Navigation';

export function ReaderLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen">
      <ReaderNavigation />
      <main className="max-w-7xl mx-auto px-4 sm:px-6 py-8 md:py-12">{children}</main>
      <footer className="border-t border-burgundy-deep/10 mt-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 py-6 flex flex-wrap items-center justify-between gap-4 text-xs text-charcoal/60">
          <span className="font-display text-sm font-bold text-burgundy-deep">Mylo</span>
          <span>Premium independent publishing — content first, always.</span>
          <span>© {new Date().getFullYear()} Mylo</span>
        </div>
      </footer>
    </div>
  );
}
