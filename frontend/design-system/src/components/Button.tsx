import * as React from 'react';
import { cn } from '@/utils/cn';

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'ghost' | 'danger';
  size?: 'sm' | 'md' | 'lg';
}

export const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant = 'primary', size = 'md', children, disabled, ...props }, ref) => {
    const variants = {
      primary: 'bg-burgundy-deep text-white hover:bg-burgundy-wine focus-visible:ring-2 focus-visible:ring-burgundy-wine focus-visible:ring-offset-2',
      secondary: 'bg-charcoal text-white hover:bg-burgundy-dark focus-visible:ring-2 focus-visible:ring-burgundy-wine focus-visible:ring-offset-2',
      ghost: 'text-burgundy-deep hover:bg-ivory-soft focus-visible:ring-2 focus-visible:ring-burgundy-wine focus-visible:ring-offset-2',
      danger: 'bg-red-600 text-white hover:bg-red-700 focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2',
    };

    const sizes = {
      sm: 'px-3 py-1.5 text-sm',
      md: 'px-4 py-2 text-base',
      lg: 'px-6 py-3 text-lg',
    };

    return (
      <button
        ref={ref}
        className={cn(
          'inline-flex items-center justify-center rounded-md font-medium transition-colors disabled:opacity-50 disabled:pointer-events-none',
          variants[variant],
          sizes[size],
          className,
        )}
        disabled={disabled}
        {...props}
      >
        {children}
      </button>
    );
  },
);

Button.displayName = 'Button';
