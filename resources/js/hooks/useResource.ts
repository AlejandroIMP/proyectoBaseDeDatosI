import { useState, useEffect, useCallback } from 'react';
import { api, ApiError } from '../api/client';
import { PaginationResponse } from '../types/models';

export interface UseResourceOptions {
    endpoint: string;
    initialPage?: number;
    perPage?: number;
    initialParams?: Record<string, any>;
    autoFetch?: boolean;
}

export function useResource<T extends { id: number | string }>(options: UseResourceOptions) {
    const {
        endpoint,
        initialPage = 1,
        perPage = 15,
        initialParams = {},
        autoFetch = true,
    } = options;

    const [items, setItems] = useState<T[]>([]);
    const [pagination, setPagination] = useState<Omit<PaginationResponse<T>, 'data'>>({
        current_page: initialPage,
        last_page: 1,
        per_page: perPage,
        total: 0,
        from: null,
        to: null,
    });
    const [page, setPage] = useState<number>(initialPage);
    const [params, setParams] = useState<Record<string, any>>(initialParams);
    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchItems = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const response = await api.get<PaginationResponse<T>>(endpoint, {
                page,
                per_page: perPage,
                ...params,
            });

            // Soporta respuesta paginada de Laravel o array plano
            if (response && Array.isArray((response as any).data)) {
                setItems(response.data);
                setPagination({
                    current_page: response.current_page,
                    last_page: response.last_page,
                    per_page: response.per_page,
                    total: response.total,
                    from: response.from,
                    to: response.to,
                });
            } else if (Array.isArray(response)) {
                setItems(response as any);
                setPagination({
                    current_page: 1,
                    last_page: 1,
                    per_page: (response as any).length,
                    total: (response as any).length,
                    from: 1,
                    to: (response as any).length,
                });
            }
        } catch (err: any) {
            setError(err?.message || 'Error al cargar los datos');
        } finally {
            setIsLoading(false);
        }
    }, [endpoint, page, perPage, params]);

    useEffect(() => {
        if (autoFetch) {
            fetchItems();
        }
    }, [fetchItems, autoFetch]);

    const createItem = async (data: Partial<T>): Promise<T> => {
        setIsLoading(true);
        try {
            const created = await api.post<T>(endpoint, data);
            await fetchItems();
            return created;
        } catch (err: any) {
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const updateItem = async (id: number | string, data: Partial<T>): Promise<T> => {
        setIsLoading(true);
        try {
            const updated = await api.put<T>(`${endpoint}/${id}`, data);
            await fetchItems();
            return updated;
        } catch (err: any) {
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    const deleteItem = async (id: number | string): Promise<void> => {
        setIsLoading(true);
        try {
            await api.delete(`${endpoint}/${id}`);
            await fetchItems();
        } catch (err: any) {
            throw err;
        } finally {
            setIsLoading(false);
        }
    };

    return {
        items,
        pagination,
        page,
        setPage,
        params,
        setParams,
        isLoading,
        error,
        refetch: fetchItems,
        createItem,
        updateItem,
        deleteItem,
    };
}

