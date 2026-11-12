import type { Metadata } from 'next';
import { ReaderLayout } from '@/components/Layout';
import { initializeAuth } from '@/lib/auth';

export const metadata: Metadata = {
  title: 'Mylo - Reader Portal',
  description: 'Discover and enjoy premium independent digital magazine content.',
};

initializeAuth();

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body className="bg-ivory-soft text-charcoal antialiased">
        <ReaderLayout>{children}</ReaderLayout>
      </body>
    </html>
  );
}
