import React from 'react';
import { cn } from '../../lib/utils';

export interface CardProps extends Omit<React.HTMLAttributes<HTMLDivElement>, 'title'> {
    title?: React.ReactNode;
    subtitle?: React.ReactNode;
    action?: React.ReactNode;
}

export const Card: React.FC<CardProps> = ({
    children,
    className,
    title,
    subtitle,
    action,
    ...props
}) => {
    return (
        <div
            className={cn(
                'bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden',
                className
            )}
            {...props}
        >
            {(title || action) && (
                <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        {typeof title === 'string' ? (
                            <h3 className="text-base font-semibold text-gray-900">{title}</h3>
                        ) : (
                            title
                        )}
                        {subtitle && <p className="text-xs text-gray-500 mt-0.5">{subtitle}</p>}
                    </div>
                    {action && <div>{action}</div>}
                </div>
            )}
            <div className="p-6">{children}</div>
        </div>
    );
};
