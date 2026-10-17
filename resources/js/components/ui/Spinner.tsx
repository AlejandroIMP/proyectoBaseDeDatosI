import React from 'react';
import { cn } from '../../lib/utils';

export const Spinner: React.FC<{ size?: 'sm' | 'md' | 'lg'; className?: string }> = ({
    size = 'md',
    className,
}) => {
    const sizeClasses = {
        sm: 'h-4 w-4 border-2',
        md: 'h-8 w-8 border-3',
        lg: 'h-12 w-12 border-4',
    }[size];

    return (
        <div
            className={cn(
                'animate-spin rounded-full border-[#10b981] border-t-transparent',
                sizeClasses,
                className
            )}
        />
    );
};

