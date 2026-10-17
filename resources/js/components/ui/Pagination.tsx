import React from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { Button } from './Button';

export interface PaginationProps {
    currentPage: number;
    lastPage: number;
    total: number;
    from?: number | null;
    to?: number | null;
    onPageChange: (page: number) => void;
}

export const Pagination: React.FC<PaginationProps> = ({
    currentPage,
    lastPage,
    total,
    from,
    to,
    onPageChange,
}) => {
    if (total === 0) return null;

    return (
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 px-2 py-4">
            <div className="text-xs text-gray-500">
                Mostrando <span className="font-semibold text-gray-700">{from || 0}</span> a{' '}
                <span className="font-semibold text-gray-700">{to || 0}</span> de{' '}
                <span className="font-semibold text-gray-700">{total}</span> resultados
            </div>

            <div className="flex items-center gap-1.5">
                <Button
                    variant="outline"
                    size="sm"
                    disabled={currentPage <= 1}
                    onClick={() => onPageChange(currentPage - 1)}
                >
                    <ChevronLeft className="h-4 w-4" />
                    <span className="sr-only sm:not-sr-only">Anterior</span>
                </Button>

                <div className="text-xs font-medium px-3 text-gray-600">
                    Página {currentPage} de {lastPage}
                </div>

                <Button
                    variant="outline"
                    size="sm"
                    disabled={currentPage >= lastPage}
                    onClick={() => onPageChange(currentPage + 1)}
                >
                    <span className="sr-only sm:not-sr-only">Siguiente</span>
                    <ChevronRight className="h-4 w-4" />
                </Button>
            </div>
        </div>
    );
};

