import React from 'react';
import { Link, NavLink } from 'react-router-dom';
import { PawPrint, Wifi, User as UserIcon, Shield } from 'lucide-react';
import { cn } from '../../lib/utils';
import { useAuth } from '../../hooks/useAuth';

interface PublicLayoutProps {
    children: React.ReactNode;
}

export const PublicLayout: React.FC<PublicLayoutProps> = ({ children }) => {
    const { isAuthenticated, user } = useAuth();

    return (
        <div className="min-h-screen bg-[#f8f9ff] flex flex-col">
            {/* Topbar Refugio Conectado (Exacta a la Imagen 3) */}
            <header className="sticky top-0 z-40 bg-white border-b border-gray-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    {/* Logo */}
                    <div className="flex items-center gap-2 sm:gap-3 min-w-0">
                        <PawPrint className="h-6 w-6 sm:h-7 sm:w-7 text-[#006c49] shrink-0" />
                        <Link to="/catalog" className="text-base sm:text-xl font-bold text-[#006c49] tracking-tight truncate">
                            Refugio Conectado
                        </Link>
                    </div>

                    {/* Navegación Central (Mascotas, Donaciones, Mis Solicitudes) */}
                    <nav className="hidden md:flex items-center gap-8">
                        <NavLink
                            to="/catalog"
                            className={({ isActive }) =>
                                cn(
                                    'text-sm font-semibold transition-colors pb-1 border-b-2',
                                    isActive
                                        ? 'text-[#006c49] border-[#006c49]'
                                        : 'text-gray-600 border-transparent hover:text-gray-900'
                                )
                            }
                        >
                            Mascotas
                        </NavLink>

                        <NavLink
                            to="/public/donations"
                            className={({ isActive }) =>
                                cn(
                                    'text-sm font-semibold transition-colors pb-1 border-b-2',
                                    isActive
                                        ? 'text-[#006c49] border-[#006c49]'
                                        : 'text-gray-600 border-transparent hover:text-gray-900'
                                )
                            }
                        >
                            Donaciones
                        </NavLink>

                        <NavLink
                            to="/public/my-applications"
                            className={({ isActive }) =>
                                cn(
                                    'text-sm font-semibold transition-colors pb-1 border-b-2',
                                    isActive
                                        ? 'text-[#006c49] border-[#006c49]'
                                        : 'text-gray-600 border-transparent hover:text-gray-900'
                                )
                            }
                        >
                            Mis Solicitudes
                        </NavLink>
                    </nav>

                    {/* Lado Derecho: Estado LAN y Perfil / Acceso Admin */}
                    <div className="flex items-center gap-2 sm:gap-4 shrink-0">
                        <div className="hidden sm:inline-flex items-center gap-1.5 text-xs text-gray-500 bg-[#e5eeff] px-3 py-1 rounded-full font-medium">
                            <Wifi className="h-3 w-3 text-[#10b981]" />
                            <span>LAN: Conectado</span>
                        </div>

                        {isAuthenticated ? (
                            <div className="flex items-center gap-2 sm:gap-3">
                                <Link
                                    to="/admin/dashboard"
                                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 sm:px-3 py-1.5 rounded-md hover:bg-emerald-100 transition-colors"
                                >
                                    <Shield className="h-3.5 w-3.5 shrink-0" />
                                    <span className="hidden xs:inline">Panel Operativo</span>
                                    <span className="xs:hidden">Admin</span>
                                </Link>

                                <div className="h-8 w-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs border border-emerald-300 shrink-0">
                                    {user?.name ? user.name.charAt(0).toUpperCase() : <UserIcon className="h-4 w-4" />}
                                </div>
                            </div>
                        ) : (
                            <Link
                                to="/login"
                                className="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-[#10b981] hover:bg-[#059669] px-3 sm:px-4 py-2 rounded-md shadow-xs transition-colors shrink-0"
                            >
                                <UserIcon className="h-3.5 w-3.5" />
                                <span>Iniciar Sesión</span>
                            </Link>
                        )}
                    </div>
                </div>

                {/* Sub-barra de navegación móvil para pantallas pequeñas */}
                <div className="flex md:hidden border-t border-gray-100 bg-white px-3 py-2 justify-around text-xs font-semibold">
                    <NavLink
                        to="/catalog"
                        className={({ isActive }) =>
                            isActive ? 'text-[#006c49] font-bold border-b-2 border-[#006c49] pb-0.5' : 'text-gray-600 hover:text-gray-900'
                        }
                    >
                        Mascotas
                    </NavLink>
                    <NavLink
                        to="/public/donations"
                        className={({ isActive }) =>
                            isActive ? 'text-[#006c49] font-bold border-b-2 border-[#006c49] pb-0.5' : 'text-gray-600 hover:text-gray-900'
                        }
                    >
                        Donaciones
                    </NavLink>
                    <NavLink
                        to="/public/my-applications"
                        className={({ isActive }) =>
                            isActive ? 'text-[#006c49] font-bold border-b-2 border-[#006c49] pb-0.5' : 'text-gray-600 hover:text-gray-900'
                        }
                    >
                        Mis Solicitudes
                    </NavLink>
                </div>
            </header>

            {/* Contenido público */}
            <main className="flex-1 max-w-7xl mx-auto w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
                {children}
            </main>
        </div>
    );
};

