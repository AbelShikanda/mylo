import { WriterNavigation } from '@/components/Navigation';

export function WriterLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen">
      <WriterNavigation />
      <main className="max-w-7xl mx-auto px-4 sm:px-6 py-8 md:py-10">{children}</main>
      <footer className="border-t border-burgundy-deep/10 mt-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 py-6 flex flex-wrap items-center justify-between gap-4 text-xs text-charcoal/60">
          <span className="font-display text-sm font-bold text-burgundy-deep">Mylo · Writer Studio</span>
          <span>Where serious writing gets room to breathe.</span>
          <span>© {new Date().getFullYear()} Mylo</span>
        </div>
      </footer>
    </div>
  );
}
