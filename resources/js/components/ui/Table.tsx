import React from 'react';
import { cn } from '../../lib/utils';
import { SkeletonTableRow } from './Skeleton';
import { Inbox } from 'lucide-react';

export interface Column<T> {
    header: string;
    accessorKey?: keyof T;
    cell?: (item: T) => React.ReactNode;
    className?: string;
}

export interface TableProps<T> {
    columns: Column<T>[];
    data: T[];
    isLoading?: boolean;
    emptyMessage?: string;
    onRowClick?: (item: T) => void;
    skeletonRowCount?: number;
}

export function Table<T extends { id?: number | string }>({
    columns,
    data,
    isLoading = false,
    emptyMessage = 'No se encontraron registros en el sistema.',
    onRowClick,
    skeletonRowCount = 5,
}: TableProps<T>) {
    return (
        <div className="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-xs touch-pan-x">
            <table className="w-full text-left text-sm text-gray-600">
                <thead className="bg-[#eff4ff] text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200">
                    <tr>
                        {columns.map((col, idx) => (
                            <th key={idx} className={cn('px-3.5 sm:px-6 py-2.5 sm:py-3.5', col.className)}>
                                {col.header}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                    {isLoading ? (
                        Array.from({ length: skeletonRowCount }).map((_, rIdx) => (
                            <SkeletonTableRow key={rIdx} cols={columns.length} />
                        ))
                    ) : data.length === 0 ? (
                        <tr>
                            <td colSpan={columns.length} className="px-4 sm:px-6 py-12 text-center text-gray-500">
                                <div className="flex flex-col items-center justify-center gap-2">
                                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                        <Inbox className="h-5 w-5" />
                                    </div>
                                    <span className="text-sm font-medium text-gray-600">{emptyMessage}</span>
                                </div>
                            </td>
                        </tr>
                    ) : (
                        data.map((item, rowIdx) => (
                            <tr
                                key={item.id ?? rowIdx}
                                onClick={() => onRowClick && onRowClick(item)}
                                className={cn(
                                    'transition-colors hover:bg-gray-50/80',
                                    onRowClick ? 'cursor-pointer' : '',
                                    rowIdx % 2 === 1 ? 'bg-gray-50/30' : ''
                                )}
                            >
                                {columns.map((col, colIdx) => (
                                    <td key={colIdx} className={cn('px-3.5 sm:px-6 py-2.5 sm:py-3.5 whitespace-nowrap text-xs sm:text-sm text-gray-800', col.className)}>
                                        {col.cell
                                            ? col.cell(item)
                                            : col.accessorKey
                                              ? String(item[col.accessorKey] ?? '—')
                                              : null}
                                    </td>
                                ))}
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
        </div>
    );
}
