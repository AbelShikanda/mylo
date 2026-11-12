import * as React from 'react';
import { cn } from '@/utils/cn';

export interface ContainerProps extends React.HTMLAttributes<HTMLDivElement> {
  maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | 'full';
}

export const Container = React.forwardRef<HTMLDivElement, ContainerProps>(
  ({ className, maxWidth = 'xl', children, ...props }, ref) => {
    const widths = {
      sm: 'max-w-sm',
      md: 'max-w-md',
      lg: 'max-w-lg',
      xl: 'max-w-xl',
      full: 'max-w-full',
    };

    return (
      <div ref={ref} className={cn('mx-auto px-4', widths[maxWidth], className)} {...props}>
        {children}
      </div>
    );
  },
);

Container.displayName = 'Container';
