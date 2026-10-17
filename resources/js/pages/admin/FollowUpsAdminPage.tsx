import React, { useState, useEffect } from 'react';
import { AppLayout } from '../../components/layout/AppLayout';
import { ResourceCrudView, FieldConfig } from '../../components/crud/ResourceCrudView';
import { AdoptionFollowUp } from '../../types/models';
import { Column } from '../../components/ui/Table';
import { api } from '../../api/client';

export const FollowUpsAdminPage: React.FC = () => {
    const [adoptionOptions, setAdoptionOptions] = useState([
        { value: 1, label: 'Adopción #1: Max (Adoptante: Carlos Ruiz)' },
        { value: 2, label: 'Adopción #2: Luna (Adoptante: María Fernanda Gómez)' },
        { value: 3, label: 'Adopción #3: Toby (Adoptante: Jorge Mendoza)' },
    ]);

    useEffect(() => {
        const loadAdoptions = async () => {
            try {
                const res = await api.get('adoptions', { per_page: 50 });
                if (res) {
                    const list = Array.isArray(res) ? res : res.data;
                    if (Array.isArray(list) && list.length > 0) {
                        setAdoptionOptions(list.map((a: any) => ({
                            value: a.id,
                            label: `Adopción #${a.id}: ${a.pet?.name || 'Mascota'} (Adoptante: ${a.person?.name || 'Persona'})`,
                        })));
                    }
                }
            } catch (e) {
                // Mantener fallbacks
            }
        };

        loadAdoptions();
    }, []);

    const columns: Column<AdoptionFollowUp>[] = [
        { header: 'ID', accessorKey: 'id', className: 'w-16 font-mono text-xs' },
        {
            header: 'Adopción',
            cell: (f) => (
                <span className="font-semibold text-gray-900">
                    Adopción #{f.adoption_id} {f.adoption?.pet ? `(${f.adoption.pet.name})` : ''}
                </span>
            ),
        },
        { header: 'Fecha de Seguimiento', accessorKey: 'followed_at', className: 'text-xs text-gray-700' },
        {
            header: 'Notas de Bienestar y Hogar',
            accessorKey: 'notes',
            className: 'text-xs text-gray-600 max-w-sm truncate',
        },
    ];

    const fields: FieldConfig[] = [
        {
            name: 'adoption_id',
            label: 'Adopción a Supervisar',
            type: 'select',
            required: true,
            options: adoptionOptions,
        },
        { name: 'followed_at', label: 'Fecha de la Visita o Llamada', type: 'date', required: true },
        { name: 'notes', label: 'Observaciones de Adaptación y Salud', type: 'textarea', required: true, placeholder: 'La mascota se muestra sociable, bien alimentada, con espacio adecuado...' },
    ];

    const handleTransformPayload = (data: any) => ({
        ...data,
        adoption_id: data.adoption_id ? parseInt(String(data.adoption_id), 10) : undefined,
    });

    return (
        <AppLayout>
            <ResourceCrudView<AdoptionFollowUp>
                title="Supervisión y Post-Adopción"
                description="Control de visitas domiciliares y seguimiento del bienestar animal en sus nuevos hogares."
                endpoint="adoption-follow-ups"
                columns={columns}
                fields={fields}
                onTransformPayload={handleTransformPayload}
            />
        </AppLayout>
    );
};


