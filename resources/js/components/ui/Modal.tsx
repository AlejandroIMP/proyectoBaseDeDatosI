import React, { useEffect } from 'react';
import { X } from 'lucide-react';
import { cn } from '../../lib/utils';

export interface ModalProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    description?: string;
    children: React.ReactNode;
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
}

export const Modal: React.FC<ModalProps> = ({
    isOpen,
    onClose,
    title,
    description,
    children,
    maxWidth = 'lg',
}) => {
    useEffect(() => {
        const handleKeyDown = (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isOpen) onClose();
        };
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [isOpen, onClose]);

    if (!isOpen) return null;

    const maxW = {
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg',
        xl: 'max-w-xl',
        '2xl': 'max-w-2xl',
    }[maxWidth];

    return (
        <div className="fixed inset-0 z-50 overflow-y-auto">
            {/* Backdrop */}
            <div
                className="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity"
                onClick={onClose}
            />

            <div className="flex min-h-full items-center justify-center p-2.5 sm:p-4 text-center">
                <div
                    className={cn(
                        'relative transform rounded-xl bg-white text-left shadow-2xl transition-all w-full my-auto sm:my-8 border border-gray-200 max-h-[92vh] flex flex-col overflow-hidden',
                        maxW
                    )}
                >
                    <div className="flex items-center justify-between border-b border-gray-100 px-4 sm:px-6 py-3.5 sm:py-4 bg-gray-50/70 shrink-0">
                        <div className="min-w-0 pr-2">
                            <h3 className="text-sm sm:text-base font-semibold text-gray-900 truncate">{title}</h3>
                            {description && <p className="text-xs text-gray-500 mt-0.5 truncate">{description}</p>}
                        </div>
                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors shrink-0 cursor-pointer"
                            title="Cerrar ventana"
                        >
                            <X className="h-5 w-5" />
                        </button>
                    </div>
                    <div className="p-4 sm:p-6 overflow-y-auto flex-1">{children}</div>
                </div>
            </div>
        </div>
    );
};

