import React, { useState, useEffect, useRef } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';
import { Badge } from '../ui/Badge';
import { api } from '../../api/client';
import {
    Building,
    Wifi,
    Bell,
    User as UserIcon,
    LogOut,
    ExternalLink,
    ChevronDown,
    Check,
    Calendar,
    Heart,
    FileText,
    CheckCircle2,
    RotateCw,
    Menu,
} from 'lucide-react';

interface NotificationItem {
    id: string;
    title: string;
    description: string;
    time: string;
    link: string;
    unread: boolean;
    type: 'adoption' | 'medical' | 'donation' | 'rescue';
}

export interface HeaderProps {
    onToggleMobileNav?: () => void;
}

export const Header: React.FC<HeaderProps> = ({ onToggleMobileNav }) => {
    const { user, logout, primaryRole } = useAuth();
    const navigate = useNavigate();

    // Estados para los dropdowns
    const [profileOpen, setProfileOpen] = useState(false);
    const [branchOpen, setBranchOpen] = useState(false);
    const [notificationsOpen, setNotificationsOpen] = useState(false);

    // Sedes
    const [shelters, setShelters] = useState<{ id: number; name: string }[]>([
        { id: 0, name: 'Todas las Sedes (Red Global)' },
        { id: 1, name: 'Albergue Central (Sede Principal)' },
        { id: 2, name: 'Refugio Norte (Cuarentena)' },
        { id: 3, name: 'Refugio Sur (Adopciones)' },
    ]);
    const [selectedShelterId, setSelectedShelterId] = useState<number>(() => {
        const saved = localStorage.getItem('shelter_active_id');
        return saved ? parseInt(saved, 10) : 0;
    });

    // Función utilitaria para cálculo relativo de tiempo
    const calculateTimeAgo = (dateStr?: string): string => {
        if (!dateStr) return 'Reciente';
        try {
            const date = new Date(dateStr);
            const now = new Date();
            const diffMs = now.getTime() - date.getTime();
            if (diffMs < 0) return 'Próximamente';
            const diffMins = Math.floor(diffMs / (1000 * 60));
            if (diffMins < 1) return 'Ahora mismo';
            if (diffMins < 60) return `Hace ${diffMins} min`;
            const diffHours = Math.floor(diffMins / (1000 * 60 * 60));
            if (diffHours < 24) return `Hace ${diffHours} hr${diffHours > 1 ? 's' : ''}`;
            const diffDays = Math.floor(diffHours / 24);
            if (diffDays === 1) return 'Ayer';
            if (diffDays < 7) return `Hace ${diffDays} días`;
            return date.toLocaleDateString('es-GT', { day: '2-digit', month: 'short' });
        } catch {
            return 'Reciente';
        }
    };

    // Notificaciones
    const [notifications, setNotifications] = useState<NotificationItem[]>([
        {
            id: 'notif-1',
            title: 'Sistema Conectado',
            description: 'Red de albergues y sincronización de base de datos activa.',
            time: 'Hoy',
            link: '/admin',
            unread: false,
            type: 'rescue',
        },
    ]);

    const headerRef = useRef<HTMLElement>(null);

    // Cargar sedes desde la API
    useEffect(() => {
        api.get('shelters')
            .then((res: any) => {
                const list = Array.isArray(res) ? res : res?.data;
                if (list && list.length > 0) {
                    setShelters([
                        { id: 0, name: 'Todas las Sedes (Red Global)' },
                        ...list.map((s: any) => ({ id: s.id, name: s.name })),
                    ]);
                }
            })
            .catch(() => {});
    }, []);

    // Cargar notificaciones dinámicas en vivo
    const fetchDynamicNotifications = async () => {
        try {
            const rawRead = localStorage.getItem('shelter_read_notifs');
            const readIds: string[] = rawRead ? JSON.parse(rawRead) : [];

            const [adoptionsRes, apptsRes, donationsRes] = await Promise.allSettled([
                api.get('adoptions', { per_page: 5, direction: 'desc' }),
                api.get('appointments', { per_page: 4, direction: 'desc' }),
                api.get('donations', { per_page: 3, direction: 'desc' }),
            ]);

            const dynamicList: NotificationItem[] = [];

            // 1. Solicitudes de Adopción
            if (adoptionsRes.status === 'fulfilled' && adoptionsRes.value) {
                const aData = adoptionsRes.value;
                const list = Array.isArray(aData) ? aData : aData.data;
                if (Array.isArray(list)) {
                    list.forEach((adop: any) => {
                        const notifId = `adop-${adop.id}`;
                        const isPending = !adop.approved_at && !adop.delivered_at;
                        dynamicList.push({
                            id: notifId,
                            title: `Solicitud: ${adop.pet?.name || 'Mascota #' + adop.pet_id}`,
                            description: `${adop.person?.name || 'Postulante'} ha enviado postulación (${adop.adoption_status?.name || 'En Revisión'}).`,
                            time: calculateTimeAgo(adop.requested_at || adop.created_at),
                            link: `/admin/adoptions/eval/${adop.id}`,
                            unread: isPending && !readIds.includes(notifId),
                            type: 'adoption',
                        });
                    });
                }
            }

            // 2. Citas Médicas
            if (apptsRes.status === 'fulfilled' && apptsRes.value) {
                const apData = apptsRes.value;
                const list = Array.isArray(apData) ? apData : apData.data;
                if (Array.isArray(list)) {
                    list.forEach((ap: any) => {
                        const notifId = `appt-${ap.id}`;
                        const isPending = !ap.status || ap.status === 'pending';
                        dynamicList.push({
                            id: notifId,
                            title: `Cita Médica: ${ap.pet?.name || 'Mascota #' + ap.pet_id}`,
                            description: `${ap.appointment_type?.name || 'Chequeo'} programada con ${ap.person?.name || ap.user?.name || 'Especialista'}.`,
                            time: calculateTimeAgo(ap.appointment_date || ap.created_at),
                            link: '/admin/medical',
                            unread: isPending && !readIds.includes(notifId),
                            type: 'medical',
                        });
                    });
                }
            }

            // 3. Donaciones
            if (donationsRes.status === 'fulfilled' && donationsRes.value) {
                const dData = donationsRes.value;
                const list = Array.isArray(dData) ? dData : dData.data;
                if (Array.isArray(list)) {
                    list.forEach((d: any) => {
                        const notifId = `don-${d.id}`;
                        const amount = parseFloat(d.amount || 0).toLocaleString('es-GT', { minimumFractionDigits: 2 });
                        dynamicList.push({
                            id: notifId,
                            title: 'Nueva Donación Recibida',
                            description: `Aporte de Q ${amount} de ${d.donor?.person?.name || 'Donante'}.`,
                            time: calculateTimeAgo(d.donated_at || d.created_at),
                            link: '/admin/donations',
                            unread: !readIds.includes(notifId),
                            type: 'donation',
                        });
                    });
                }
            }

            if (dynamicList.length > 0) {
                setNotifications(dynamicList);
            }
        } catch {
            // Mantener estado actual si hay fallo de conexión
        }
    };

    useEffect(() => {
        fetchDynamicNotifications();
    }, []);

    // Cerrar popovers al hacer clic fuera
    useEffect(() => {
        const handleClickOutside = (e: MouseEvent) => {
            if (headerRef.current && !headerRef.current.contains(e.target as Node)) {
                setProfileOpen(false);
                setBranchOpen(false);
                setNotificationsOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleSelectShelter = (id: number) => {
        setSelectedShelterId(id);
        localStorage.setItem('shelter_active_id', String(id));
        setBranchOpen(false);
        // Notificar a otras vistas que la sede activa cambió
        window.dispatchEvent(new CustomEvent('shelter:branch-changed', { detail: { shelterId: id } }));
    };

    const handleMarkAllAsRead = () => {
        setNotifications((prev) => {
            const allIds = prev.map((n) => n.id);
            const rawRead = localStorage.getItem('shelter_read_notifs');
            const existingRead: string[] = rawRead ? JSON.parse(rawRead) : [];
            const updated = Array.from(new Set([...existingRead, ...allIds]));
            localStorage.setItem('shelter_read_notifs', JSON.stringify(updated));
            return prev.map((n) => ({ ...n, unread: false }));
        });
    };

    const handleNotificationClick = (item: NotificationItem) => {
        setNotifications((prev) => {
            const rawRead = localStorage.getItem('shelter_read_notifs');
            const existingRead: string[] = rawRead ? JSON.parse(rawRead) : [];
            if (!existingRead.includes(item.id)) {
                localStorage.setItem('shelter_read_notifs', JSON.stringify([...existingRead, item.id]));
            }
            return prev.map((n) => (n.id === item.id ? { ...n, unread: false } : n));
        });
        setNotificationsOpen(false);
        navigate(item.link);
    };

    const handleLogout = async () => {
        await logout();
        navigate('/login');
    };

    const unreadCount = notifications.filter((n) => n.unread).length;
    const currentShelterName = shelters.find((s) => s.id === selectedShelterId)?.name || 'Sede Central (Albergue)';

    return (
        <header ref={headerRef} className="h-16 border-b border-gray-200 bg-white px-3 sm:px-6 flex items-center justify-between shrink-0 relative z-30">
            {/* Título de la Sección actual y botón menú en móviles */}
            <div className="flex items-center gap-2 sm:gap-4 min-w-0">
                {/* Botón Hamburguesa (< lg) */}
                <button
                    type="button"
                    onClick={onToggleMobileNav}
                    className="lg:hidden p-1.5 -ml-1 rounded-lg text-gray-600 hover:text-[#006c49] hover:bg-emerald-50 transition-colors cursor-pointer shrink-0"
                    title="Abrir menú de navegación"
                >
                    <Menu className="h-5 w-5" />
                </button>

                <h1 className="text-sm sm:text-base font-bold text-[#006c49] truncate">
                    Refugios Conectados
                </h1>
                <Link
                    to="/catalog"
                    className="hidden sm:inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-[#006c49] font-medium bg-gray-50 px-2.5 py-1 rounded border border-gray-200 shrink-0"
                >
                    <ExternalLink className="h-3.5 w-3.5" />
                    <span>Ver Catálogo</span>
                </Link>
            </div>

            {/* Elementos de la Derecha */}
            <div className="flex items-center gap-1.5 sm:gap-3">
                {/* Selector Interactivo de Sede (Combobox) */}
                <div className="relative">
                    <button
                        type="button"
                        onClick={() => {
                            setBranchOpen(!branchOpen);
                            setNotificationsOpen(false);
                            setProfileOpen(false);
                        }}
                        className="flex items-center gap-1 sm:gap-2 rounded-md border border-gray-200 bg-gray-50 px-2 sm:px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors"
                        title="Seleccionar Sede del Refugio"
                    >
                        <Building className="h-3.5 w-3.5 text-emerald-700 shrink-0" />
                        <span className="truncate max-w-[70px] xs:max-w-[110px] sm:max-w-[170px]">{currentShelterName}</span>
                        <ChevronDown className={`h-3.5 w-3.5 text-gray-400 transition-transform ${branchOpen ? 'rotate-180' : ''}`} />
                    </button>

                    {branchOpen && (
                        <div className="absolute right-0 mt-2 w-64 max-w-[90vw] rounded-xl bg-white shadow-lg ring-1 ring-black/5 border border-gray-200 py-1.5 z-50">
                            <div className="px-3 py-1.5 border-b border-gray-100">
                                <p className="text-[10px] font-bold uppercase tracking-wider text-gray-400">
                                    Sede Operativa Activa
                                </p>
                            </div>
                            <div className="max-h-60 overflow-y-auto p-1 space-y-0.5">
                                {shelters.map((s) => (
                                    <button
                                        key={s.id}
                                        type="button"
                                        onClick={() => handleSelectShelter(s.id)}
                                        className={`w-full flex items-center justify-between px-3 py-2 text-xs rounded-lg text-left transition-colors ${
                                            selectedShelterId === s.id
                                                ? 'bg-emerald-50 text-emerald-800 font-bold'
                                                : 'text-gray-700 hover:bg-gray-50'
                                        }`}
                                    >
                                        <span className="truncate">{s.name}</span>
                                        {selectedShelterId === s.id && <Check className="h-3.5 w-3.5 text-emerald-600 shrink-0 ml-2" />}
                                    </button>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                {/* IP Indicator */}
                <div className="hidden lg:flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100/60 px-2.5 py-1.5 rounded">
                    <Wifi className="h-3.5 w-3.5 text-[#10b981]" />
                    <span className="font-mono text-[11px]">LAN: Conectado</span>
                </div>

                {/* Botón de Notificaciones Interactivo */}
                <div className="relative">
                    <button
                        type="button"
                        onClick={() => {
                            setNotificationsOpen(!notificationsOpen);
                            setBranchOpen(false);
                            setProfileOpen(false);
                        }}
                        className="relative rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                        title="Notificaciones del Sistema"
                    >
                        <Bell className="h-5 w-5" />
                        {unreadCount > 0 && (
                            <span className="absolute top-1 right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold ring-2 ring-white">
                                {unreadCount}
                            </span>
                        )}
                    </button>

                    {notificationsOpen && (
                        <div className="absolute right-0 mt-2 w-[calc(100vw-1.5rem)] max-w-sm sm:w-96 rounded-xl bg-white shadow-xl ring-1 ring-black/5 border border-gray-200 z-50 overflow-hidden">
                            <div className="p-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/60">
                                <div className="flex items-center gap-2">
                                    <h4 className="text-xs font-bold text-gray-900">Notificaciones</h4>
                                    {unreadCount > 0 && (
                                        <Badge variant="warning" size="sm">
                                            {unreadCount} nuevas
                                        </Badge>
                                    )}
                                </div>
                                <div className="flex items-center gap-2">
                                    <button
                                        type="button"
                                        onClick={() => fetchDynamicNotifications()}
                                        title="Actualizar notificaciones"
                                        className="p-1 rounded text-gray-400 hover:text-emerald-700 hover:bg-gray-200/50 transition-colors"
                                    >
                                        <RotateCw className="h-3.5 w-3.5" />
                                    </button>
                                    {unreadCount > 0 && (
                                        <button
                                            type="button"
                                            onClick={handleMarkAllAsRead}
                                            className="text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 transition-colors"
                                        >
                                            Marcar leídas
                                        </button>
                                    )}
                                </div>
                            </div>

                            <div className="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                                {notifications.length > 0 ? (
                                    notifications.map((n) => (
                                        <div
                                            key={n.id}
                                            onClick={() => handleNotificationClick(n)}
                                            className={`p-3.5 hover:bg-gray-50 cursor-pointer transition-colors flex items-start gap-3 ${
                                                n.unread ? 'bg-emerald-50/30' : ''
                                            }`}
                                        >
                                            <div className="mt-0.5 shrink-0">
                                                {n.type === 'adoption' && (
                                                    <div className="h-7 w-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">
                                                        <FileText className="h-3.5 w-3.5" />
                                                    </div>
                                                )}
                                                {n.type === 'medical' && (
                                                    <div className="h-7 w-7 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center">
                                                        <Calendar className="h-3.5 w-3.5" />
                                                    </div>
                                                )}
                                                {n.type === 'donation' && (
                                                    <div className="h-7 w-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                                        <Heart className="h-3.5 w-3.5" />
                                                    </div>
                                                )}
                                                {n.type === 'rescue' && (
                                                    <div className="h-7 w-7 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center">
                                                        <CheckCircle2 className="h-3.5 w-3.5" />
                                                    </div>
                                                )}
                                            </div>

                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-center justify-between gap-1">
                                                    <p className={`text-xs truncate ${n.unread ? 'font-bold text-gray-900' : 'font-semibold text-gray-700'}`}>
                                                        {n.title}
                                                    </p>
                                                    <span className="text-[10px] text-gray-400 shrink-0">{n.time}</span>
                                                </div>
                                                <p className="text-[11px] text-gray-600 line-clamp-2 mt-0.5">
                                                    {n.description}
                                                </p>
                                            </div>

                                            {n.unread && (
                                                <span className="h-2 w-2 rounded-full bg-emerald-500 shrink-0 mt-1.5" />
                                            )}
                                        </div>
                                    ))
                                ) : (
                                    <div className="py-8 text-center text-xs text-gray-400">
                                        No hay notificaciones nuevas
                                    </div>
                                )}
                            </div>
                        </div>
                    )}
                </div>

                {/* Perfil de Usuario */}
                <div className="relative">
                    <button
                        onClick={() => {
                            setProfileOpen(!profileOpen);
                            setBranchOpen(false);
                            setNotificationsOpen(false);
                        }}
                        className="flex items-center gap-2.5 rounded-lg p-1.5 text-gray-700 hover:bg-gray-100 transition-colors"
                    >
                        <div className="flex h-8 w-8 items-center justify-center rounded-full bg-[#10b981]/20 text-[#006c49] font-bold text-xs border border-[#10b981]/30">
                            {user?.name ? user.name.charAt(0).toUpperCase() : <UserIcon className="h-4 w-4" />}
                        </div>
                        <div className="hidden sm:block text-left">
                            <p className="text-xs font-bold text-gray-900 leading-tight truncate max-w-[120px]">{user?.name || 'Usuario'}</p>
                            <p className="text-[10px] text-gray-500 font-medium leading-tight">{primaryRole}</p>
                        </div>
                        <ChevronDown className="h-3.5 w-3.5 text-gray-400" />
                    </button>

                    {profileOpen && (
                        <div className="absolute right-0 mt-2 w-52 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 border border-gray-200 z-50">
                            <div className="px-4 py-2.5 border-b border-gray-100 space-y-1">
                                <p className="text-xs font-bold text-gray-900 truncate">{user?.name || 'Usuario'}</p>
                                <p className="text-[11px] text-gray-500 truncate">{user?.email}</p>
                                <div className="pt-1">
                                    <Badge variant="success" size="sm">{primaryRole}</Badge>
                                </div>
                            </div>

                            <button
                                onClick={handleLogout}
                                className="flex w-full items-center gap-2 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors"
                            >
                                <LogOut className="h-4 w-4" />
                                <span>Cerrar Sesión</span>
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
};
