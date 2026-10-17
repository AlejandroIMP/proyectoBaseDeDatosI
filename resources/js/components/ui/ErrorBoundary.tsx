import React, { Component, ErrorInfo, ReactNode } from 'react';
import { AlertTriangle, RefreshCw, Home } from 'lucide-react';
import { Button } from './Button';

interface Props {
    children: ReactNode;
}

interface State {
    hasError: boolean;
    error: Error | null;
}

export class ErrorBoundary extends Component<Props, State> {
    public state: State = {
        hasError: false,
        error: null,
    };

    public static getDerivedStateFromError(error: Error): State {
        return { hasError: true, error };
    }

    public componentDidCatch(error: Error, errorInfo: ErrorInfo) {
        console.error('ErrorBoundary caught an error:', error, errorInfo);
    }

    public handleReset = () => {
        this.setState({ hasError: false, error: null });
        window.location.href = '/catalog';
    };

    public handleReload = () => {
        window.location.reload();
    };

    public render() {
        if (this.state.hasError) {
            return (
                <div className="min-h-screen bg-[#f8f9ff] flex items-center justify-center p-6">
                    <div className="max-w-md w-full rounded-2xl border border-gray-200 bg-white p-8 shadow-sm text-center space-y-5">
                        <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-50 text-amber-600 border border-amber-200">
                            <AlertTriangle className="h-7 w-7" />
                        </div>

                        <div className="space-y-2">
                            <h2 className="text-xl font-black text-gray-900 tracking-tight">
                                Algo no salió como esperábamos
                            </h2>
                            <p className="text-xs text-gray-600 leading-relaxed">
                                Se ha presentado una interrupción momentánea en la interfaz. Puedes recargar la pantalla o volver al catálogo principal.
                            </p>
                        </div>

                        {this.state.error && (
                            <div className="rounded-lg bg-gray-50 p-3 text-left border border-gray-100 overflow-x-auto">
                                <p className="font-mono text-[11px] text-gray-600 truncate">
                                    {this.state.error.message}
                                </p>
                            </div>
                        )}

                        <div className="flex items-center justify-center gap-3 pt-2">
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={this.handleReload}
                                className="flex items-center gap-1.5"
                            >
                                <RefreshCw className="h-3.5 w-3.5" />
                                <span>Recargar</span>
                            </Button>
                            <Button
                                variant="primary"
                                size="sm"
                                onClick={this.handleReset}
                                className="flex items-center gap-1.5 bg-[#006c49] hover:bg-[#005238]"
                            >
                                <Home className="h-3.5 w-3.5" />
                                <span>Volver al Inicio</span>
                            </Button>
                        </div>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}

