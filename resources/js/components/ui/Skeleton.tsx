import React from 'react';
import { cn } from '../../lib/utils';

export interface SkeletonProps extends React.HTMLAttributes<HTMLDivElement> {
    className?: string;
}

export const Skeleton: React.FC<SkeletonProps> = ({ className, ...props }) => {
    return (
        <div
            className={cn('animate-pulse rounded-md bg-gray-200/80', className)}
            {...props}
        />
    );
};

export const SkeletonText: React.FC<{ lines?: number; className?: string }> = ({
    lines = 3,
    className,
}) => (
    <div className={cn('space-y-2', className)}>
        {Array.from({ length: lines }).map((_, idx) => (
            <Skeleton
                key={idx}
                className={cn('h-4', idx === lines - 1 ? 'w-3/5' : 'w-full')}
            />
        ))}
    </div>
);

export const SkeletonKpiCard: React.FC = () => (
    <div className="rounded-xl border border-gray-200/70 bg-white p-5 shadow-xs">
        <div className="flex items-center justify-between">
            <Skeleton className="h-4 w-28" />
            <Skeleton className="h-10 w-10 rounded-xl" />
        </div>
        <Skeleton className="mt-4 h-8 w-20" />
        <div className="mt-3 flex items-center gap-2">
            <Skeleton className="h-4 w-12 rounded-full" />
            <Skeleton className="h-3 w-28" />
        </div>
    </div>
);

export const SkeletonTableRow: React.FC<{ cols?: number }> = ({ cols = 5 }) => (
    <tr className="border-b border-gray-100">
        {Array.from({ length: cols }).map((_, idx) => (
            <td key={idx} className="px-6 py-4">
                <Skeleton
                    className={cn(
                        'h-4',
                        idx === 0
                            ? 'w-10'
                            : idx === 1
                              ? 'w-36'
                              : idx === cols - 1
                                ? 'w-16 ml-auto'
                                : 'w-24'
                    )}
                />
            </td>
        ))}
    </tr>
);

export const SkeletonTable: React.FC<{ rows?: number; cols?: number }> = ({
    rows = 5,
    cols = 5,
}) => (
    <div className="w-full space-y-3 p-4">
        {Array.from({ length: rows }).map((_, idx) => (
            <div key={idx} className="flex items-center gap-4">
                <Skeleton className="h-4 w-10 shrink-0" />
                <Skeleton className="h-4 flex-1" />
                <Skeleton className="h-4 w-28 shrink-0" />
                <Skeleton className="h-4 w-20 shrink-0" />
                <Skeleton className="h-4 w-16 shrink-0" />
            </div>
        ))}
    </div>
);

export const SkeletonPetCard: React.FC = () => (
    <div className="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-xs">
        <Skeleton className="h-56 w-full rounded-none" />
        <div className="p-5 space-y-3">
            <div className="flex items-center justify-between">
                <Skeleton className="h-5 w-32" />
                <Skeleton className="h-5 w-16 rounded-full" />
            </div>
            <Skeleton className="h-3.5 w-44" />
            <div className="pt-3 flex items-center justify-between border-t border-gray-100">
                <Skeleton className="h-4 w-24" />
                <Skeleton className="h-8 w-24 rounded-lg" />
            </div>
        </div>
    </div>
);

export const SkeletonProgressBar: React.FC = () => (
    <div className="space-y-2">
        <div className="flex justify-between items-center">
            <Skeleton className="h-3.5 w-36" />
            <Skeleton className="h-3.5 w-12" />
        </div>
        <Skeleton className="h-2 w-full rounded-full" />
    </div>
);

