import { ReaderNavigation } from '@/components/Navigation';

export function ReaderLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen bg-ivory-soft text-charcoal">
      <ReaderNavigation />
      <main className="max-w-4xl mx-auto py-8 px-4">{children}</main>
    </div>
  );
}
