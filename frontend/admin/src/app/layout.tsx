import type { Metadata } from 'next';
import { AdminLayout } from '@/components/Layout';
import { initializeAuth } from '@/lib/auth';

export const metadata: Metadata = {
  title: 'Mylo - Administrator Portal',
  description: 'Platform control centre for managing the Mylo publishing ecosystem.',
};

initializeAuth();

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body className="bg-charcoal text-ivory-soft antialiased">
        <AdminLayout>{children}</AdminLayout>
      </body>
    </html>
  );
}
