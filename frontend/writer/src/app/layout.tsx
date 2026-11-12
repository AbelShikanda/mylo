import type { Metadata } from 'next';
import { WriterLayout } from '@/components/Layout';
import { initializeAuth } from '@/lib/auth';

export const metadata: Metadata = {
  title: 'Mylo - Writer Portal',
  description: 'Professional publishing workspace for independent writers.',
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
        <WriterLayout>{children}</WriterLayout>
      </body>
    </html>
  );
}
