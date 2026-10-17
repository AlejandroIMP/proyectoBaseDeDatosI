import React, { useState, useEffect } from 'react';
import { AppLayout } from '../../components/layout/AppLayout';
import { ResourceCrudView, FieldConfig } from '../../components/crud/ResourceCrudView';
import { Rescue } from '../../types/models';
import { Column } from '../../components/ui/Table';
import { api } from '../../api/client';

export const RescuesAdminPage: React.FC = () => {
    const [petOptions, setPetOptions] = useState([
        { value: 1, label: 'Max (Perro / Mestizo)' },
        { value: 2, label: 'Luna (Gato / Común)' },
        { value: 3, label: 'Toby (Perro / Golden)' },
        { value: 4, label: 'Rocky (Perro / Terrier)' },
    ]);

    const [statusOptions, setStatusOptions] = useState([
        { value: 1, label: 'Disponible' },
        { value: 2, label: 'En tratamiento' },
        { value: 3, label: 'En cuarentena' },
        { value: 4, label: 'Reservado' },
        { value: 5, label: 'Adoptado' },
    ]);

    useEffect(() => {
        const loadOptions = async () => {
            try {
                const [pRes, sRes] = await Promise.allSettled([
                    api.get('pets', { per_page: 50 }),
                    api.get('pet-statuses'),
                ]);

                if (pRes.status === 'fulfilled' && pRes.value) {
                    const list = Array.isArray(pRes.value) ? pRes.value : pRes.value.data;
                    if (Array.isArray(list) && list.length > 0) {
                        setPetOptions(list.map((p: any) => ({
                            value: p.id,
                            label: `${p.name} (${p.species?.name || 'Mascota'} - ${p.breed?.name || 'Raza'})`,
                        })));
                    }
                }

                if (sRes.status === 'fulfilled' && sRes.value) {
                    const list = Array.isArray(sRes.value) ? sRes.value : sRes.value.data;
                    if (Array.isArray(list) && list.length > 0) {
                        setStatusOptions(list.map((s: any) => ({
                            value: s.id,
                            label: s.name,
                        })));
                    }
                }
            } catch (e) {
                // Mantener fallbacks
            }
        };

        loadOptions();
    }, []);

    const columns: Column<Rescue>[] = [
        { header: 'ID', accessorKey: 'id', className: 'w-16 font-mono text-xs' },
        {
            header: 'Mascota',
            cell: (r) => (
                <span className="font-bold text-gray-900">
                    {r.pet?.name || `Mascota #${r.pet_id}`}
                </span>
            ),
        },
        {
            header: 'Fecha de Rescate',
            cell: (r) => <span className="text-xs text-gray-700">{r.rescued_at}</span>,
        },
        {
            header: 'Estado de Ingreso',
            cell: (r) => (
                <span className="text-xs font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded">
                    {r.pet_status?.name || `Estado #${r.pet_status_id}`}
                </span>
            ),
        },
        {
            header: 'Notas de Rescate',
            cell: (r) => (
                <span className="text-xs text-gray-500 max-w-xs truncate block">
                    {r.notes || '—'}
                </span>
            ),
        },
    ];

    const fields: FieldConfig[] = [
        {
            name: 'pet_id',
            label: 'Mascota Rescatada',
            type: 'select',
            required: true,
            options: petOptions,
        },
        {
            name: 'pet_status_id',
            label: 'Estado Clínico Inicial',
            type: 'select',
            required: true,
            options: statusOptions,
        },
        { name: 'rescued_at', label: 'Fecha de Rescate', type: 'date', required: true },
        { name: 'notes', label: 'Detalles del rescate y ubicación encontrada', type: 'textarea', placeholder: 'Condiciones de salud, ubicación geográfica, reporte ciudadano...' },
    ];

    const handleTransformPayload = (data: any) => ({
        ...data,
        pet_id: data.pet_id ? parseInt(String(data.pet_id), 10) : undefined,
        pet_status_id: data.pet_status_id ? parseInt(String(data.pet_status_id), 10) : undefined,
    });

    return (
        <AppLayout>
            <ResourceCrudView<Rescue>
                title="Rescates y Operaciones de Ingreso"
                description="Control de animales rescatados de la vía pública y casos de emergencia."
                endpoint="rescues"
                columns={columns}
                fields={fields}
                onTransformPayload={handleTransformPayload}
                filterShelter={true}
            />
        </AppLayout>
    );
};


