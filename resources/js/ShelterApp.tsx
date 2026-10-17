import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import { LoginPage } from './pages/LoginPage';
import { PetCatalogPage } from './pages/public/PetCatalogPage';
import { PublicDonationsPage } from './pages/public/PublicDonationsPage';
import { PublicMyApplicationsPage } from './pages/public/PublicMyApplicationsPage';
import { DashboardPage } from './pages/admin/DashboardPage';
import { ApplicationReviewPage } from './pages/admin/ApplicationReviewPage';
import { PetsAdminPage } from './pages/admin/PetsAdminPage';
import { RescuesAdminPage } from './pages/admin/RescuesAdminPage';
import { MedicalAdminPage } from './pages/admin/MedicalAdminPage';
import { AdoptionsAdminPage } from './pages/admin/AdoptionsAdminPage';
import { ContractsAdminPage } from './pages/admin/ContractsAdminPage';
import { FollowUpsAdminPage } from './pages/admin/FollowUpsAdminPage';
import { DonationsAdminPage } from './pages/admin/DonationsAdminPage';
import { PeopleAdminPage } from './pages/admin/PeopleAdminPage';
import { SheltersAdminPage } from './pages/admin/SheltersAdminPage';
import { UsersAdminPage } from './pages/admin/UsersAdminPage';
import { SettingsAdminPage } from './pages/admin/SettingsAdminPage';
import { Spinner } from './components/ui/Spinner';
import { ErrorBoundary } from './components/ui/ErrorBoundary';
import { AccessDeniedPage } from './components/ui/AccessDeniedPage';

// Guard para rutas protegidas de administración con verificación RBAC
interface ProtectedAdminRouteProps {
    children: React.ReactNode;
    allowedRoles?: string[];
    moduleName?: string;
}

const ProtectedAdminRoute: React.FC<ProtectedAdminRouteProps> = ({
    children,
    allowedRoles,
    moduleName,
}) => {
    const { isAuthenticated, isLoading, hasAnyRole } = useAuth();

    if (isLoading) {
        return (
            <div className="flex h-screen w-full items-center justify-center bg-[#f8f9ff]">
                <Spinner size="lg" />
            </div>
        );
    }

    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    if (allowedRoles && allowedRoles.length > 0 && !hasAnyRole(allowedRoles)) {
        return <AccessDeniedPage requiredRoles={allowedRoles} moduleName={moduleName} />;
    }

    return <>{children}</>;
};

export const ShelterApp: React.FC = () => {
    return (
        <AuthProvider>
            <BrowserRouter>
                <ErrorBoundary>
                    <Routes>
                        {/* Rutas Públicas */}
                        <Route path="/" element={<Navigate to="/catalog" replace />} />
                        <Route path="/catalog" element={<PetCatalogPage />} />
                        <Route path="/login" element={<LoginPage />} />

                        {/* Vistas directas públicas */}
                        <Route path="/public/donations" element={<PublicDonationsPage />} />
                        <Route path="/public/my-applications" element={<PublicMyApplicationsPage />} />

                        {/* Alias directos y atajos de navegación */}
                        <Route path="/pets" element={<Navigate to="/admin/pets" replace />} />
                        <Route path="/rescues" element={<Navigate to="/admin/rescues" replace />} />
                        <Route path="/medical" element={<Navigate to="/admin/medical" replace />} />
                        <Route path="/adoptions" element={<Navigate to="/admin/adoptions" replace />} />
                        <Route path="/contracts" element={<Navigate to="/admin/contracts" replace />} />
                        <Route path="/donations" element={<Navigate to="/admin/donations" replace />} />
                        <Route path="/people" element={<Navigate to="/admin/people" replace />} />
                        <Route path="/shelters" element={<Navigate to="/admin/shelters" replace />} />
                        <Route path="/users" element={<Navigate to="/admin/users" replace />} />
                        <Route path="/settings" element={<Navigate to="/admin/settings" replace />} />

                        {/* Rutas Administrativas Protegidas con Roles */}
                        <Route
                            path="/admin/dashboard"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Veterinario', 'Recepcionista', 'Voluntario']}
                                    moduleName="Dashboard Principal"
                                >
                                    <DashboardPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/pets"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Veterinario', 'Recepcionista', 'Voluntario']}
                                    moduleName="Catálogo de Mascotas"
                                >
                                    <PetsAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/rescues"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Veterinario', 'Voluntario']}
                                    moduleName="Rescates y Entradas"
                                >
                                    <RescuesAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/medical"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Veterinario', 'Recepcionista']}
                                    moduleName="Registros Médicos y Citas"
                                >
                                    <MedicalAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/adoptions"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Recepcionista']}
                                    moduleName="Solicitudes de Adopción"
                                >
                                    <AdoptionsAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/adoptions/eval/:id"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Recepcionista']}
                                    moduleName="Evaluación Detallada de Adopción"
                                >
                                    <ApplicationReviewPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/contracts"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Recepcionista']}
                                    moduleName="Contratos de Adopción"
                                >
                                    <ContractsAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/follow-ups"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Veterinario', 'Voluntario']}
                                    moduleName="Supervisión Post-Adopción"
                                >
                                    <FollowUpsAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/donations"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Recepcionista']}
                                    moduleName="Donaciones y Donantes"
                                >
                                    <DonationsAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/people"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador', 'Recepcionista']}
                                    moduleName="Padrón de Personas"
                                >
                                    <PeopleAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/shelters"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador']}
                                    moduleName="Gestión de Refugios y Sedes"
                                >
                                    <SheltersAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/users"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador']}
                                    moduleName="Control de Usuarios y Permisos"
                                >
                                    <UsersAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        <Route
                            path="/admin/settings"
                            element={
                                <ProtectedAdminRoute
                                    allowedRoles={['Administrador']}
                                    moduleName="Configuraciones del Sistema"
                                >
                                    <SettingsAdminPage />
                                </ProtectedAdminRoute>
                            }
                        />

                        {/* Ruta de reserva */}
                        <Route path="*" element={<Navigate to="/catalog" replace />} />
                    </Routes>
                </ErrorBoundary>
            </BrowserRouter>
        </AuthProvider>
    );
};
