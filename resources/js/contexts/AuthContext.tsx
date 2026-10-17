import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { api, setAuthToken, getAuthToken } from '../api/client';
import { User } from '../types/models';

interface AuthContextType {
    user: User | null;
    token: string | null;
    isLoading: boolean;
    login: (email: string, password: string, deviceName?: string) => Promise<void>;
    register: (name: string, email: string, password: string, deviceName?: string) => Promise<void>;
    logout: () => Promise<void>;
    setUser: (user: User | null) => void;
    isAuthenticated: boolean;
    hasRole: (roleName: string) => boolean;
    hasAnyRole: (roleNames: string[]) => boolean;
    primaryRole: string;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const [user, setUser] = useState<User | null>(null);
    const [token, setToken] = useState<string | null>(getAuthToken());
    const [isLoading, setIsLoading] = useState<boolean>(true);

    useEffect(() => {
        const initAuth = async () => {
            const savedToken = getAuthToken();
            if (savedToken) {
                try {
                    const userData = await api.get<User>('/user');
                    setUser(userData);
                } catch (err) {
                    setAuthToken(null);
                    setToken(null);
                    setUser(null);
                }
            }
            setIsLoading(false);
        };

        initAuth();

        const handleUnauthorized = () => {
            setUser(null);
            setToken(null);
        };

        window.addEventListener('auth:unauthorized', handleUnauthorized);
        return () => window.removeEventListener('auth:unauthorized', handleUnauthorized);
    }, []);

    const login = async (email: string, password: string, deviceName = 'browser') => {
        const response = await api.post<{ token: string; user: User }>('/login', {
            email,
            password,
            device_name: deviceName,
        });

        setAuthToken(response.token);
        setToken(response.token);
        setUser(response.user);
    };

    const register = async (name: string, email: string, password: string, deviceName = 'browser') => {
        const response = await api.post<{ token: string; user: User }>('/register', {
            name,
            email,
            password,
            device_name: deviceName,
        });

        setAuthToken(response.token);
        setToken(response.token);
        setUser(response.user);
    };

    const logout = async () => {
        try {
            await api.post('/logout');
        } catch (e) {
            // Ignorar si ya falló
        } finally {
            setAuthToken(null);
            setToken(null);
            setUser(null);
        }
    };

    const hasRole = (roleName: string): boolean => {
        if (!user || !user.roles || user.roles.length === 0) return false;
        return user.roles.some((r) => r.name.toLowerCase() === roleName.toLowerCase());
    };

    const hasAnyRole = (roleNames: string[]): boolean => {
        if (!user || !user.roles || user.roles.length === 0) return false;
        if (hasRole('Administrador')) return true;
        return roleNames.some((r) => hasRole(r));
    };

    const primaryRole = user?.roles && user.roles.length > 0 ? user.roles[0].name : 'Usuario';

    return (
        <AuthContext.Provider
            value={{
                user,
                token,
                isLoading,
                login,
                register,
                logout,
                setUser,
                isAuthenticated: !!token && !!user,
                hasRole,
                hasAnyRole,
                primaryRole,
            }}
        >
            {children}
        </AuthContext.Provider>
    );
};


export const useAuth = (): AuthContextType => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};

