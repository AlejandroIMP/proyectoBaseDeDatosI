import React, { InputHTMLAttributes, forwardRef } from 'react';
import { cn } from '../../lib/utils';

export interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    error?: string;
    icon?: React.ReactNode;
}

export const Input = forwardRef<HTMLInputElement, InputProps>(
    ({ className, label, error, icon, id, ...props }, ref) => {
        const inputId = id || (label ? label.toLowerCase().replace(/\s+/g, '-') : undefined);

        return (
            <div className="w-full">
                {label && (
                    <label htmlFor={inputId} className="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        {label} {props.required && <span className="text-red-500">*</span>}
                    </label>
                )}
                <div className="relative">
                    <input
                        id={inputId}
                        ref={ref}
                        className={cn(
                            'w-full rounded border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981] transition-colors',
                            icon ? 'pr-10' : '',
                            error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '',
                            className
                        )}
                        {...props}
                    />
                    {icon && (
                        <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            {icon}
                        </div>
                    )}
                </div>
                {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
            </div>
        );
    }
);

Input.displayName = 'Input';

