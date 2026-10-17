// Cliente API HTTP con soporte para Sanctum Bearer token y JSON

const BASE_URL = '/api';

export class ApiError extends Error {
    constructor(
        public status: number,
        public message: string,
        public errors?: Record<string, string[]>
    ) {
        super(message);
        this.name = 'ApiError';
    }
}

export function getAuthToken(): string | null {
    return localStorage.getItem('shelter_auth_token');
}

export function setAuthToken(token: string | null): void {
    if (token) {
        localStorage.setItem('shelter_auth_token', token);
    } else {
        localStorage.removeItem('shelter_auth_token');
    }
}

export async function apiRequest<T = any>(
    endpoint: string,
    options: RequestInit = {}
): Promise<T> {
    const token = getAuthToken();
    const url = endpoint.startsWith('http') 
        ? endpoint 
        : `${BASE_URL}${endpoint.startsWith('/') ? endpoint : `/${endpoint}`}`;

    const headers: Record<string, string> = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...(options.headers as Record<string, string> || {}),
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch(url, {
        ...options,
        headers,
    });

    if (response.status === 204) {
        return null as unknown as T;
    }

    let data: any;
    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
        data = await response.json();
    } else {
        data = await response.text();
    }

    if (!response.ok) {
        if (response.status === 401) {
            setAuthToken(null);
            // Si no estamos en la página de login, podemos avisar o redirigir
            window.dispatchEvent(new CustomEvent('auth:unauthorized'));
        }

        const errorMessage = data?.message || `Error ${response.status}: ${response.statusText}`;
        throw new ApiError(response.status, errorMessage, data?.errors);
    }

    return data as T;
}

export const api = {
    get: <T = any>(endpoint: string, params?: Record<string, any>) => {
        let url = endpoint;
        if (params) {
            const query = new URLSearchParams();
            Object.entries(params).forEach(([key, val]) => {
                if (val !== undefined && val !== null && val !== '') {
                    query.append(key, String(val));
                }
            });
            const qs = query.toString();
            if (qs) url += `${url.includes('?') ? '&' : '?'}${qs}`;
        }
        return apiRequest<T>(url, { method: 'GET' });
    },

    post: <T = any>(endpoint: string, data?: any) => {
        return apiRequest<T>(endpoint, {
            method: 'POST',
            body: data ? JSON.stringify(data) : undefined,
        });
    },

    put: <T = any>(endpoint: string, data?: any) => {
        return apiRequest<T>(endpoint, {
            method: 'PUT',
            body: data ? JSON.stringify(data) : undefined,
        });
    },

    patch: <T = any>(endpoint: string, data?: any) => {
        return apiRequest<T>(endpoint, {
            method: 'PATCH',
            body: data ? JSON.stringify(data) : undefined,
        });
    },

    delete: <T = any>(endpoint: string) => {
        return apiRequest<T>(endpoint, { method: 'DELETE' });
    },
};

