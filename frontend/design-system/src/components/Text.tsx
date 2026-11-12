import * as React from 'react';
import { cn } from '@/utils/cn';

interface TextProps extends React.HTMLAttributes<HTMLSpanElement> {
  as?: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6' | 'p' | 'span';
}

export const Text = React.forwardRef<HTMLSpanElement, TextProps>(
  ({ className, as: Component = 'span', children, ...props }, ref) => {
    return (
      <Component ref={ref} className={cn('text-charcoal', className)} {...props}>
        {children}
      </Component>
    );
  },
);

Text.displayName = 'Text';

export const Heading = React.forwardRef<HTMLHeadingElement, TextProps>(
  ({ className, as: Component = 'h1', children, ...props }, ref) => {
    return (
      <Component
        ref={ref}
        className={cn(
          'font-serif text-burgundy-deep text-2xl font-bold tracking-tight',
          className,
        )}
        {...props}
      >
        {children}
      </Component>
    );
  },
);

Heading.displayName = 'Heading';
