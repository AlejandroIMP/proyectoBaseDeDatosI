import React, { SelectHTMLAttributes, forwardRef } from 'react';
import { cn } from '../../lib/utils';

export interface SelectOption {
    value: string | number;
    label: string;
}

export interface SelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
    label?: string;
    error?: string;
    options: SelectOption[];
    placeholder?: string;
}

export const Select = forwardRef<HTMLSelectElement, SelectProps>(
    ({ className, label, error, options, placeholder, id, ...props }, ref) => {
        const selectId = id || (label ? label.toLowerCase().replace(/\s+/g, '-') : undefined);

        return (
            <div className="w-full">
                {label && (
                    <label htmlFor={selectId} className="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        {label} {props.required && <span className="text-red-500">*</span>}
                    </label>
                )}
                <select
                    id={selectId}
                    ref={ref}
                    className={cn(
                        'w-full rounded border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-[#10b981] focus:outline-none focus:ring-1 focus:ring-[#10b981] transition-colors',
                        error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '',
                        className
                    )}
                    {...props}
                >
                    {placeholder && (
                        <option value="">{placeholder}</option>
                    )}
                    {options.map((opt) => (
                        <option key={opt.value} value={opt.value}>
                            {opt.label}
                        </option>
                    ))}
                </select>
                {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
            </div>
        );
    }
);

Select.displayName = 'Select';

