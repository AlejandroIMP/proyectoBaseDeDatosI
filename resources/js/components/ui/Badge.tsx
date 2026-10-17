import React from 'react';
import { cn } from '../../lib/utils';

export interface BadgeProps extends React.HTMLAttributes<HTMLSpanElement> {
    variant?: 'success' | 'warning' | 'info' | 'danger' | 'neutral';
    size?: 'sm' | 'md';
}

export const Badge: React.FC<BadgeProps> = ({
    children,
    className,
    variant = 'neutral',
    size = 'sm',
    ...props
}) => {
    const variants = {
        success: 'bg-[#d1fae5] text-[#065f46] border border-[#a7f3d0]',
        warning: 'bg-[#fef3c7] text-[#92400e] border border-[#fde68a]',
        info: 'bg-[#dbeafe] text-[#1e40af] border border-[#bfdbfe]',
        danger: 'bg-[#fee2e2] text-[#991b1b] border border-[#fecaca]',
        neutral: 'bg-gray-100 text-gray-700 border border-gray-200',
    };

    const sizes = {
        sm: 'text-[11px] font-semibold px-2.5 py-0.5 rounded-full uppercase tracking-wider',
        md: 'text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider',
    };

    return (
        <span
            className={cn('inline-flex items-center gap-1', variants[variant], sizes[size], className)}
            {...props}
        >
            {children}
        </span>
    );
};

