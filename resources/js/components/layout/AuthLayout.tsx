import React from 'react';
import { ShieldCheck, Wifi } from 'lucide-react';

interface AuthLayoutProps {
    children: React.ReactNode;
}

export const AuthLayout: React.FC<AuthLayoutProps> = ({ children }) => {
    return (
        <div className="flex min-h-screen w-full bg-[#f8f9ff]">
            {/* Panel Izquierdo Hero (Exacto a la Imagen 2) */}
            <div className="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-gradient-to-br from-[#006c49] via-[#047857] to-[#064e3b] p-12 text-white lg:flex">
                {/* Imagen de fondo sutil con overlay */}
                <div 
                    className="absolute inset-0 bg-cover bg-center opacity-30 mix-blend-overlay"
                    style={{
                        backgroundImage: `url('https://images.unsplash.com/photo-1548767797-d8c844163c4c?auto=format&fit=crop&q=80&w=1200')`
                    }}
                />
                
                <div className="relative z-10 space-y-6">
                    <div className="inline-flex items-center gap-2 rounded-full bg-[#10b981]/30 backdrop-blur-md px-3.5 py-1 text-xs font-semibold text-white border border-[#10b981]/40">
                        <Wifi className="h-3.5 w-3.5" />
                        <span>Servidor LAN: 192.168.1.42</span>
                    </div>

                    <div className="space-y-2">
                        <div className="flex items-center gap-3">
                            <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 backdrop-blur-md">
                                <ShieldCheck className="h-6 w-6 text-white" />
                            </div>
                            <h1 className="text-3xl font-extrabold tracking-tight">
                                Sistema de Gestión de Refugios
                            </h1>
                        </div>
                        <p className="text-sm text-emerald-100 max-w-md">
                            Plataforma centralizada de operaciones veterinarias, registro de fauna, control de adopciones y gestión de donaciones.
                        </p>
                    </div>
                </div>

                <div className="relative z-10 border-t border-white/20 pt-6">
                    <p className="text-sm font-semibold tracking-wide text-white">
                        Administración centralizada v2.4.0-stable
                    </p>
                    <p className="text-xs text-emerald-200 mt-0.5">
                        Infraestructura Local Segura • Base de Datos Conectada
                    </p>
                </div>
            </div>

            {/* Panel Derecho - Formulario de Login */}
            <div className="flex w-full flex-col items-center justify-center p-6 lg:w-1/2 sm:p-12">
                <div className="w-full max-w-md">
                    {children}
                </div>
            </div>
        </div>
    );
};

