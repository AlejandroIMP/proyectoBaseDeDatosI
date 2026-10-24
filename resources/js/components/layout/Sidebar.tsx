import React, { useEffect } from 'react';
import { NavLink } from 'react-router-dom';
import {
    LayoutDashboard,
    PawPrint,
    Stethoscope,
    FileText,
    FileCheck2,
    CalendarHeart,
    HandHeart,
    Users,
    Settings,
    Building2,
    Shield,
    HeartHandshake,
    X,
} from 'lucide-react';
import { cn } from '../../lib/utils';
import { useAuth } from '../../contexts/AuthContext';
import { Badge } from '../ui/Badge';

interface NavItem {
    name: string;
    path: string;
    icon: React.ComponentType<{ className?: string }>;
    allowedRoles?: string[];
}

export interface SidebarProps {
    isMobileOpen?: boolean;
    onCloseMobile?: () => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ isMobileOpen = false, onCloseMobile }) => {
    const { user, hasAnyRole, primaryRole } = useAuth();

    // Cerrar con Escape si está abierto en móvil
    useEffect(() => {
        const handleKeyDown = (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isMobileOpen) {
                onCloseMobile?.();
            }
        };
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [isMobileOpen, onCloseMobile]);

    const navItems: NavItem[] = [
        {
            name: 'Dashboard',
            path: '/admin/dashboard',
            icon: LayoutDashboard,
            allowedRoles: ['Administrador', 'Veterinario', 'Recepcionista', 'Voluntario'],
        },
        {
            name: 'Catálogo de Mascotas',
            path: '/admin/pets',
            icon: PawPrint,
            allowedRoles: ['Administrador', 'Veterinario', 'Recepcionista', 'Voluntario'],
        },
        {
            name: 'Rescates y Entradas',
            path: '/admin/rescues',
            icon: HeartHandshake,
            allowedRoles: ['Administrador', 'Veterinario', 'Voluntario'],
        },
        {
            name: 'Registros Médicos',
            path: '/admin/medical',
            icon: Stethoscope,
            allowedRoles: ['Administrador', 'Veterinario', 'Recepcionista'],
        },
        {
            name: 'Solicitudes y Adopciones',
            path: '/admin/adoptions',
            icon: FileText,
            allowedRoles: ['Administrador', 'Recepcionista'],
        },
        {
            name: 'Contratos',
            path: '/admin/contracts',
            icon: FileCheck2,
            allowedRoles: ['Administrador', 'Recepcionista'],
        },
        {
            name: 'Post-Adopción',
            path: '/admin/follow-ups',
            icon: CalendarHeart,
            allowedRoles: ['Administrador', 'Veterinario', 'Voluntario'],
        },
        {
            name: 'Donaciones y Donantes',
            path: '/admin/donations',
            icon: HandHeart,
            allowedRoles: ['Administrador', 'Recepcionista'],
        },
        {
            name: 'Personas y Solicitantes',
            path: '/admin/people',
            icon: Users,
            allowedRoles: ['Administrador', 'Recepcionista'],
        },
        {
            name: 'Refugios y Sedes',
            path: '/admin/shelters',
            icon: Building2,
            allowedRoles: ['Administrador'],
        },
        {
            name: 'Usuarios y Permisos',
            path: '/admin/users',
            icon: Shield,
            allowedRoles: ['Administrador'],
        },
    ];

    // Filtrar elementos de acuerdo a los roles asignados al usuario
    const visibleNavItems = navItems.filter((item) => {
        if (!item.allowedRoles || item.allowedRoles.length === 0) return true;
        return hasAnyRole(item.allowedRoles);
    });

    const canSeeSettings = hasAnyRole(['Administrador']);

    const getRoleBadgeVariant = (role: string): 'success' | 'info' | 'warning' | 'neutral' => {
        const lower = role.toLowerCase();
        if (lower.includes('admin')) return 'success';
        if (lower.includes('vet')) return 'info';
        if (lower.includes('recep')) return 'warning';
        return 'neutral';
    };

    return (
        <>
            {/* Backdrop en móviles/tablets (< lg) */}
            {isMobileOpen && (
                <div
                    className="fixed inset-0 z-40 bg-black/50 backdrop-blur-xs transition-opacity lg:hidden"
                    onClick={onCloseMobile}
                    aria-hidden="true"
                />
            )}

            <aside
                className={cn(
                    'fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] border-r border-gray-200 bg-white flex flex-col justify-between shrink-0 select-none shadow-2xl transition-transform duration-300 ease-in-out lg:static lg:w-64 lg:shadow-none lg:translate-x-0',
                    isMobileOpen ? 'translate-x-0' : '-translate-x-full'
                )}
            >
                {/* Cabecera del Sidebar con Rol de Usuario y botón cerrar en móvil */}
                <div>
                    <div className="p-5 sm:p-6 border-b border-gray-100 flex items-center justify-between gap-3">
                        <div className="flex items-center gap-3 min-w-0">
                            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#10b981]/10 text-[#006c49]">
                                <PawPrint className="h-6 w-6 text-[#10b981]" />
                            </div>
                            <div className="min-w-0 flex-1">
                                <h2 className="text-lg font-black tracking-tight text-[#0b1c30] flex items-center gap-1.5 truncate">
                                    Refugios Conectados
                                </h2>
                                <div className="mt-0.5">
                                    <Badge variant={getRoleBadgeVariant(primaryRole)} size="sm">
                                        {primaryRole.toUpperCase()}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        {/* Botón de cierre en pantallas móviles */}
                        <button
                            type="button"
                            onClick={onCloseMobile}
                            className="lg:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
                            title="Cerrar menú lateral"
                        >
                            <X className="h-5 w-5" />
                        </button>
                    </div>

                    {/* Navegación Filtrada por Rol */}
                    <nav className="p-3 space-y-1 overflow-y-auto max-h-[calc(100vh-140px)]">
                        {visibleNavItems.map((item) => (
                            <NavLink
                                key={item.path}
                                to={item.path}
                                onClick={() => onCloseMobile?.()}
                                className={({ isActive }) =>
                                    cn(
                                        'flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors',
                                        isActive
                                            ? 'bg-[#e6f7f0] text-[#006c49] font-semibold border-r-4 border-[#006c49]'
                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                    )
                                }
                            >
                                <item.icon className="h-4 w-4 shrink-0" />
                                <span className="truncate">{item.name}</span>
                            </NavLink>
                        ))}
                    </nav>
                </div>

                {/* Pie del Sidebar: Configuraciones (Exclusivo Administrador) */}
                {canSeeSettings && (
                    <div className="p-3 border-t border-gray-100">
                        <NavLink
                            to="/admin/settings"
                            onClick={() => onCloseMobile?.()}
                            className={({ isActive }) =>
                                cn(
                                    'flex items-center gap-3 px-3.5 py-2.5 rounded-md text-sm font-medium transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                    isActive ? 'bg-[#e6f7f0] text-[#006c49]' : ''
                                )
                            }
                        >
                            <Settings className="h-4 w-4 text-gray-500" />
                            <span>Configuraciones</span>
                        </NavLink>
                    </div>
                )}
            </aside>
        </>
    );
};
