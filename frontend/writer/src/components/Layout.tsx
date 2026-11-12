import { WriterNavigation } from '@/components/Navigation';

export function WriterLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen bg-ivory-soft text-charcoal">
      <WriterNavigation />
      <main className="max-w-6xl mx-auto py-8 px-4">{children}</main>
    </div>
  );
}
