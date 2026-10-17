import React, { ButtonHTMLAttributes } from 'react';
import { cn } from '../../lib/utils';

export interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    variant?: 'primary' | 'secondary' | 'outline' | 'danger' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
    isLoading?: boolean;
}

export const Button: React.FC<ButtonProps> = ({
    children,
    className,
    variant = 'primary',
    size = 'md',
    isLoading = false,
    disabled,
    ...props
}) => {
    const baseStyles = 'inline-flex items-center justify-center font-medium transition-colors rounded focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed';

    const variants = {
        primary: 'bg-[#10b981] hover:bg-[#059669] text-white focus:ring-[#10b981] shadow-sm',
        secondary: 'bg-[#565e74] hover:bg-[#43495b] text-white focus:ring-[#565e74]',
        outline: 'border border-gray-300 hover:bg-gray-50 text-gray-700 bg-white focus:ring-[#10b981]',
        danger: 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        ghost: 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-gray-300',
    };

    const sizes = {
        sm: 'text-xs px-2.5 py-1.5 gap-1.5',
        md: 'text-sm px-4 py-2 gap-2',
        lg: 'text-base px-5 py-2.5 gap-2.5',
    };

    return (
        <button
            className={cn(baseStyles, variants[variant], sizes[size], className)}
            disabled={disabled || isLoading}
            {...props}
        >
            {isLoading ? (
                <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
            ) : null}
            {children}
        </button>
    );
};

