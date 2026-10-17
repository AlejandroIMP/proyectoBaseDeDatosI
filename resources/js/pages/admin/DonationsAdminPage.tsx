import React, { useState, useEffect } from 'react';
import { AppLayout } from '../../components/layout/AppLayout';
import { ResourceCrudView, FieldConfig } from '../../components/crud/ResourceCrudView';
import { Donation, Donor } from '../../types/models';
import { Column } from '../../components/ui/Table';
import { Badge } from '../../components/ui/Badge';
import { api } from '../../api/client';

export const DonationsAdminPage: React.FC = () => {
    const [activeTab, setActiveTab] = useState<'donations' | 'donors'>('donations');

    const [donorOptions, setDonorOptions] = useState([
        { value: 1, label: 'Fundación Amigo Fiel' },
        { value: 2, label: 'Veterinaria San Roque' },
        { value: 3, label: 'Carlos Ruiz (Particular)' },
    ]);

    const [petOptions, setPetOptions] = useState([
        { value: 1, label: 'Max (Perro)' },
        { value: 2, label: 'Luna (Gato)' },
        { value: 3, label: 'Toby (Perro)' },
    ]);

    const [personOptions, setPersonOptions] = useState([
        { value: 1, label: 'Carlos Ruiz' },
        { value: 2, label: 'María Fernanda Gómez' },
        { value: 3, label: 'Jorge Mendoza' },
    ]);

    useEffect(() => {
        const loadCatalogs = async () => {
            try {
                const [dRes, pRes, peRes] = await Promise.allSettled([
                    api.get('donors', { per_page: 50 }),
                    api.get('pets', { per_page: 50 }),
                    api.get('people', { per_page: 50 }),
                ]);

                if (dRes.status === 'fulfilled' && dRes.value) {
                    const list = Array.isArray(dRes.value) ? dRes.value : dRes.value.data;
                    if (Array.isArray(list) && list.length > 0) {
                        setDonorOptions(list.map((d: any) => ({ value: d.id, label: d.name })));
                    }
                }

                if (pRes.status === 'fulfilled' && pRes.value) {
                    const list = Array.isArray(pRes.value) ? pRes.value : pRes.value.data;
                    if (Array.isArray(list) && list.length > 0) {
                        setPetOptions(list.map((p: any) => ({ value: p.id, label: `${p.name} (${p.species?.name || 'Mascota'})` })));
                    }
                }

                if (peRes.status === 'fulfilled' && peRes.value) {
                    const list = Array.isArray(peRes.value) ? peRes.value : peRes.value.data;
                    if (Array.isArray(list) && list.length > 0) {
                        setPersonOptions(list.map((pe: any) => ({ value: pe.id, label: pe.name })));
                    }
                }
            } catch (e) {
                // Mantener fallbacks
            }
        };

        loadCatalogs();
    }, []);

    // Donaciones
    const donationColumns: Column<Donation>[] = [
        { header: 'ID', accessorKey: 'id', className: 'w-16 font-mono text-xs' },
        {
            header: 'Donante',
            cell: (d) => <span className="font-bold text-gray-900">{d.donor?.name || `Donante #${d.donor_id}`}</span>,
        },
        {
            header: 'Monto Aportado',
            cell: (d) => <span className="font-mono font-bold text-emerald-700 text-sm">Q {Number(d.amount).toFixed(2)}</span>,
        },
        {
            header: 'Método / Tipo',
            cell: (d) => {
                const types: Record<string, string> = { cash: 'Efectivo', transfer: 'Transferencia', in_kind: 'Especie' };
                return <Badge variant="info">{types[d.contribution_type] || d.contribution_type}</Badge>;
            },
        },
        {
            header: 'Mascota Beneficiada',
            cell: (d) => <span className="text-xs text-gray-500">{d.pet ? d.pet.name : 'Fondo General'}</span>,
        },
    ];

    const donationFields: FieldConfig[] = [
        {
            name: 'donor_id',
            label: 'Donante',
            type: 'select',
            required: true,
            options: donorOptions,
        },
        { name: 'amount', label: 'Monto de la Aportación en Quetzales (Q)', type: 'number', step: '0.01', min: 0, required: true },
        {
            name: 'contribution_type',
            label: 'Tipo de Contribución',
            type: 'select',
            required: true,
            options: [
                { value: 'cash', label: 'Efectivo' },
                { value: 'transfer', label: 'Transferencia Bancaria' },
                { value: 'in_kind', label: 'En Especie (Alimento/Insumos)' },
            ],
        },
        {
            name: 'pet_id',
            label: 'Mascota Destino (Opcional - dejar vacío para Fondo General)',
            type: 'select',
            options: petOptions,
        },
    ];

    // Donantes
    const donorColumns: Column<Donor>[] = [
        { header: 'ID', accessorKey: 'id', className: 'w-16 font-mono text-xs' },
        { header: 'Nombre del Donante / Organización', accessorKey: 'name', className: 'font-bold text-gray-900' },
        {
            header: 'Persona Vinculada',
            cell: (d) => <span className="text-xs text-gray-700">{d.person?.name || `Persona #${d.person_id}`}</span>,
        },
    ];

    const donorFields: FieldConfig[] = [
        { name: 'name', label: 'Nombre o Razón Social del Donante', required: true, placeholder: 'Ej: Fundación Amigo Fiel' },
        {
            name: 'person_id',
            label: 'Persona Representante',
            type: 'select',
            required: true,
            options: personOptions,
        },
    ];

    const handleDonationTransform = (data: any) => ({
        ...data,
        donor_id: data.donor_id ? parseInt(String(data.donor_id), 10) : undefined,
        amount: data.amount !== '' && data.amount !== null ? parseFloat(String(data.amount)) : 0,
        pet_id: data.pet_id ? parseInt(String(data.pet_id), 10) : null,
    });

    const handleDonorTransform = (data: any) => ({
        ...data,
        person_id: data.person_id ? parseInt(String(data.person_id), 10) : undefined,
    });

    return (
        <AppLayout>
            <div className="space-y-6">
                <div>
                    <h2 className="text-2xl font-black tracking-tight text-gray-900">
                        Gestión Financiera y Donaciones
                    </h2>
                    <p className="text-xs text-gray-500 mt-1">
                        Control de aportes económicos, donaciones en especie y catálogo de benefactores.
                    </p>
                </div>

                <div className="flex border-b border-gray-200">
                    <button
                        type="button"
                        onClick={() => setActiveTab('donations')}
                        className={`py-3 px-5 text-xs font-bold transition-colors border-b-2 ${
                            activeTab === 'donations'
                                ? 'border-[#006c49] text-[#006c49]'
                                : 'border-transparent text-gray-500 hover:text-gray-700'
                        }`}
                    >
                        Registro de Donaciones
                    </button>
                    <button
                        type="button"
                        onClick={() => setActiveTab('donors')}
                        className={`py-3 px-5 text-xs font-bold transition-colors border-b-2 ${
                            activeTab === 'donors'
                                ? 'border-[#006c49] text-[#006c49]'
                                : 'border-transparent text-gray-500 hover:text-gray-700'
                        }`}
                    >
                        Catálogo de Donantes
                    </button>
                </div>

                {activeTab === 'donations' && (
                    <ResourceCrudView<Donation>
                        title="Donaciones Recibidas"
                        description="Ingresos económicos y en especie para el sustento de los animales."
                        endpoint="donations"
                        columns={donationColumns}
                        fields={donationFields}
                        onTransformPayload={handleDonationTransform}
                    />
                )}

                {activeTab === 'donors' && (
                    <ResourceCrudView<Donor>
                        title="Padrón de Donantes y Padrinos"
                        description="Benefactores particulares y corporativos registrados."
                        endpoint="donors"
                        columns={donorColumns}
                        fields={donorFields}
                        onTransformPayload={handleDonorTransform}
                    />
                )}
            </div>
        </AppLayout>
    );
};


