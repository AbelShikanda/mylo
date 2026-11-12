import { AdminNavigation } from '@/components/Navigation';

export function AdminLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen bg-charcoal text-ivory-soft">
      <AdminNavigation>{children}</AdminNavigation>
    </div>
  );
}
