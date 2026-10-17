import React from 'react';
import { AppLayout } from '../../components/layout/AppLayout';
import { ResourceCrudView, FieldConfig } from '../../components/crud/ResourceCrudView';
import { Shelter } from '../../types/models';
import { Column } from '../../components/ui/Table';
import { Building2, Phone, Mail, MapPin } from 'lucide-react';

export const SheltersAdminPage: React.FC = () => {
    const columns: Column<Shelter>[] = [
        { header: 'ID', accessorKey: 'id', className: 'w-16 font-mono text-xs' },
        {
            header: 'Sede / Refugio',
            cell: (s) => (
                <div className="flex items-center gap-2">
                    <Building2 className="h-4 w-4 text-[#006c49]" />
                    <span className="font-bold text-gray-900">{s.name}</span>
                </div>
            ),
        },
        {
            header: 'Dirección',
            cell: (s) => (
                <div className="flex items-center gap-1.5 text-xs text-gray-700">
                    <MapPin className="h-3.5 w-3.5 text-gray-400" />
                    <span>{s.address}</span>
                </div>
            ),
        },
        {
            header: 'Contacto',
            cell: (s) => (
                <div className="space-y-0.5 text-xs text-gray-600">
                    {s.phone && (
                        <p className="flex items-center gap-1">
                            <Phone className="h-3 w-3 text-gray-400" /> {s.phone}
                        </p>
                    )}
                    {s.email && (
                        <p className="flex items-center gap-1">
                            <Mail className="h-3 w-3 text-gray-400" /> {s.email}
                        </p>
                    )}
                </div>
            ),
        },
    ];

    const fields: FieldConfig[] = [
        { name: 'name', label: 'Nombre del Refugio o Sede', required: true, placeholder: 'Ej: Albergue Central, Refugio Norte' },
        { name: 'address', label: 'Dirección Física Completa', required: true, placeholder: 'Zona, Municipio, Departamento' },
        { name: 'phone', label: 'Teléfono de Contacto', placeholder: '+502 2345-6789' },
        { name: 'email', label: 'Correo Electrónico Oficial', type: 'email', placeholder: 'contacto@refugio.org' },
    ];

    return (
        <AppLayout>
            <ResourceCrudView<Shelter>
                title="Red de Refugios y Sedes"
                description="Administración de locales, albergues temporales y centros de adopción vinculados a la red."
                endpoint="shelters"
                columns={columns}
                fields={fields}
            />
        </AppLayout>
    );
};

