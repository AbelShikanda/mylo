import type { Metadata } from 'next';
import './globals.css';
import { WriterLayout } from '@/components/Layout';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
  title: 'Mylo - Writer Portal',
  description: 'Professional publishing workspace for independent writers.',
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body className="bg-ivory-soft text-charcoal antialiased">
        <WriterLayout>{children}</WriterLayout>
      </body>
    </html>
  );
}
