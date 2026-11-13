import type { Metadata } from 'next';
import './globals.css';
import { ReaderLayout } from '@/components/Layout';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
  title: 'Mylo - Reader Portal',
  description: 'Discover and enjoy premium independent digital magazine content.',
};

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
