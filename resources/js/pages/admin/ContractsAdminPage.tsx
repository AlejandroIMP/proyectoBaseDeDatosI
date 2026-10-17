import React, { useState, useEffect } from 'react';
import { AppLayout } from '../../components/layout/AppLayout';
import { ResourceCrudView, FieldConfig } from '../../components/crud/ResourceCrudView';
import { Contract } from '../../types/models';
import { Column } from '../../components/ui/Table';
import { FileText, Printer, ShieldCheck } from 'lucide-react';
import { api } from '../../api/client';
import { LegalContractModal } from '../../components/contracts/LegalContractModal';

export const ContractsAdminPage: React.FC = () => {
    const [adoptionOptions, setAdoptionOptions] = useState([
        { value: 1, label: 'Adopción #1: Max (Adoptante: Carlos Ruiz)' },
        { value: 2, label: 'Adopción #2: Luna (Adoptante: María Fernanda Gómez)' },
        { value: 3, label: 'Adopción #3: Toby (Adoptante: Jorge Mendoza)' },
    ]);

    const [selectedContractForPreview, setSelectedContractForPreview] = useState<Contract | null>(null);

    useEffect(() => {
        const loadAdoptions = async () => {
            try {
                const res = await api.get('adoptions', { per_page: 50 });
                if (res) {
                    const list = Array.isArray(res) ? res : res.data;
                    if (Array.isArray(list) && list.length > 0) {
                        setAdoptionOptions(
                            list.map((a: any) => ({
                                value: a.id,
                                label: `Adopción #${a.id}: ${a.pet?.name || 'Mascota'} (Adoptante: ${a.person?.name || 'Persona'})`,
                            }))
                        );
                    }
                }
            } catch (e) {
                // Mantener fallbacks
            }
        };

        loadAdoptions();
    }, []);

    const columns: Column<Contract>[] = [
        { header: 'ID', accessorKey: 'id', className: 'w-16 font-mono text-xs' },
        {
            header: 'Contrato',
            cell: (c) => (
                <div className="flex items-center gap-2">
                    <FileText className="h-4 w-4 text-[#006c49]" />
                    <span className="font-bold text-gray-900">{c.name}</span>
                </div>
            ),
        },
        {
            header: 'Adopción Vinculada',
            cell: (c) => (
                <span className="text-xs font-mono font-semibold text-gray-700">
                    #{c.adoption_id} {c.adoption?.pet ? `(${c.adoption.pet.name})` : ''}
                </span>
            ),
        },
        {
            header: 'Adoptante',
            cell: (c) => (
                <span className="text-xs text-gray-700 font-medium">
                    {c.adoption?.person?.name || '—'}
                </span>
            ),
        },
        {
            header: 'Documento Legal',
            cell: (c) => (
                <button
                    type="button"
                    onClick={() => setSelectedContractForPreview(c)}
                    className="inline-flex items-center gap-1.5 rounded bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800 hover:bg-emerald-100 border border-emerald-300 transition-colors cursor-pointer"
                >
                    <Printer className="h-3.5 w-3.5" />
                    <span>Ver / Imprimir Contrato</span>
                </button>
            ),
        },
    ];

    const fields: FieldConfig[] = [
        {
            name: 'adoption_id',
            label: 'Adopción Aprobada Vinculada',
            type: 'select',
            required: true,
            options: adoptionOptions,
        },
        { name: 'name', label: 'Título del Contrato', required: true, placeholder: 'Contrato de Adopción Responsable #...' },
        { name: 'document_path', label: 'Ruta o Referencia de Archivo', placeholder: 'contracts/2026/contrato_001.pdf' },
    ];

    const handleTransformPayload = (data: any) => ({
        ...data,
        adoption_id: data.adoption_id ? parseInt(String(data.adoption_id), 10) : undefined,
    });

    return (
        <AppLayout>
            <div className="space-y-6">
                <ResourceCrudView<Contract>
                    title="Contratos Legales de Adopción"
                    description="Generación, emisión formal e impresión de compromisos de tenencia responsable."
                    endpoint="contracts"
                    columns={columns}
                    fields={fields}
                    onTransformPayload={handleTransformPayload}
                />

                {/* Modal de Vista Previa e Impresión del Contrato */}
                <LegalContractModal
                    isOpen={!!selectedContractForPreview}
                    onClose={() => setSelectedContractForPreview(null)}
                    contract={selectedContractForPreview}
                />
            </div>
        </AppLayout>
    );
};
