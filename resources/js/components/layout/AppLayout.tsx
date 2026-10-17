import React, { useState } from 'react';
import { Sidebar } from './Sidebar';
import { Header } from './Header';

interface AppLayoutProps {
    children: React.ReactNode;
}

export const AppLayout: React.FC<AppLayoutProps> = ({ children }) => {
    const [mobileNavOpen, setMobileNavOpen] = useState(false);

    return (
        <div className="flex h-screen bg-[#f8f9ff] overflow-hidden relative">
            {/* Sidebar responsivo (estático en desktop >= lg, drawer flotante con backdrop en < lg) */}
            <Sidebar
                isMobileOpen={mobileNavOpen}
                onCloseMobile={() => setMobileNavOpen(false)}
            />

            {/* Área principal con Header y contenido fluido */}
            <div className="flex flex-1 flex-col overflow-hidden min-w-0">
                <Header onToggleMobileNav={() => setMobileNavOpen(!mobileNavOpen)} />
                <main className="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                    <div className="mx-auto max-w-7xl">
                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
};

