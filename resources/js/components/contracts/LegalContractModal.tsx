import React from 'react';
import { Modal } from '../ui/Modal';
import { Button } from '../ui/Button';
import { Contract, Adoption } from '../../types/models';
import { Printer, ShieldCheck, HeartHandshake, FileCheck2, Building2, User, PawPrint } from 'lucide-react';

export interface LegalContractModalProps {
    isOpen: boolean;
    onClose: () => void;
    contract?: Partial<Contract> | null;
    adoption?: Partial<Adoption> | null;
}

export const LegalContractModal: React.FC<LegalContractModalProps> = ({
    isOpen,
    onClose,
    contract,
    adoption: directAdoption,
}) => {
    if (!isOpen) return null;

    const adoption = contract?.adoption || directAdoption;
    const pet = adoption?.pet;
    const person = adoption?.person;
    const shelter = pet?.shelter;

    const contractDate = contract?.created_at
        ? new Date(contract.created_at).toLocaleDateString('es-GT', {
              day: '2-digit',
              month: 'long',
              year: 'numeric',
          })
        : new Date().toLocaleDateString('es-GT', {
              day: '2-digit',
              month: 'long',
              year: 'numeric',
          });

    const contractCode = contract?.id
        ? `CONTRATO-GT-${String(contract.id).padStart(5, '0')}`
        : `EXP-SOL-${adoption?.id || 'PENDIENTE'}`;

    const handlePrint = () => {
        window.print();
    };

    return (
        <Modal
            isOpen={isOpen}
            onClose={onClose}
            title="Contrato Oficial de Adopción Responsable"
            description="Compromiso legal de custodia, bienestar animal y seguimiento post-adopción."
            maxWidth="2xl"
        >
            <div className="space-y-6 text-gray-800">
                {/* Botón de impresión en pantalla */}
                <div className="flex justify-end gap-2 print:hidden">
                    <Button
                        type="button"
                        onClick={handlePrint}
                        className="bg-[#006c49] hover:bg-[#005238] flex items-center gap-2 text-xs font-bold shadow-xs"
                    >
                        <Printer className="h-4 w-4" />
                        <span>Imprimir / Guardar como PDF</span>
                    </Button>
                </div>

                {/* Hoja de Contrato Imprimible */}
                <div className="rounded-xl border border-gray-300 bg-white p-6 sm:p-8 space-y-6 text-xs leading-relaxed shadow-xs print:border-none print:p-0 print:shadow-none">
                    {/* Encabezado Institucional */}
                    <div className="border-b-2 border-[#006c49] pb-4 text-center space-y-1">
                        <div className="flex items-center justify-center gap-2 text-[#006c49] font-black tracking-widest text-sm uppercase">
                            <ShieldCheck className="h-5 w-5" />
                            <span>Red de Refugios y Protección Animal de Guatemala</span>
                        </div>
                        <h2 className="text-base font-extrabold text-gray-900 tracking-tight uppercase">
                            Contrato Privado de Adopción Responsable y Compromiso de Custodia
                        </h2>
                        <div className="flex items-center justify-between pt-2 text-[11px] text-gray-500 font-mono">
                            <span>Código: <strong>{contractCode}</strong></span>
                            <span>Fecha de Emisión: <strong>{contractDate}</strong></span>
                        </div>
                    </div>

                    {/* Partes Intervinientes */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div className="space-y-1">
                            <p className="font-bold text-[#006c49] uppercase tracking-wider flex items-center gap-1.5 text-[11px]">
                                <Building2 className="h-3.5 w-3.5" /> PARTE CEDENTE (EL REFUGIO)
                            </p>
                            <p className="font-semibold text-gray-900">{shelter?.name || 'Refugio Central Esperanza Animal'}</p>
                            <p className="text-gray-600">{shelter?.address || 'Ciudad de Guatemala, Guatemala'}</p>
                            <p className="text-gray-500">Tel: {shelter?.phone || '+502 2345-6789'} • {shelter?.email || 'contacto@refugio.org'}</p>
                        </div>

                        <div className="space-y-1">
                            <p className="font-bold text-[#006c49] uppercase tracking-wider flex items-center gap-1.5 text-[11px]">
                                <User className="h-3.5 w-3.5" /> PARTE ADOPTANTE (EL TUTOR)
                            </p>
                            <p className="font-semibold text-gray-900">{person?.name || 'Solicitante Aprobado'}</p>
                            <p className="text-gray-700">
                                <strong>{person?.document_type || 'DPI'}:</strong> {person?.document_number || 'N/A'}
                            </p>
                            <p className="text-gray-600">
                                Ocupación: {person?.occupation?.name || 'Empleado'} • Vivienda: {person?.residence?.name || 'Casa particular'}
                            </p>
                            <p className="text-gray-500">Email: {person?.email || 'correo@ejemplo.com'}</p>
                        </div>
                    </div>

                    {/* Datos de la Mascota */}
                    <div className="border border-emerald-200 bg-emerald-50/40 p-4 rounded-lg space-y-2">
                        <p className="font-bold text-emerald-900 uppercase tracking-wider flex items-center gap-1.5 text-[11px]">
                            <PawPrint className="h-3.5 w-3.5 text-emerald-700" /> IDENTIFICACIÓN DEL EJEMPLAR ADOPTADO
                        </p>
                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                            <div>
                                <span className="text-gray-500">Nombre:</span>{' '}
                                <strong className="text-gray-900">{pet?.name || 'Mascota'}</strong>
                            </div>
                            <div>
                                <span className="text-gray-500">Especie:</span>{' '}
                                <strong className="text-gray-900">{pet?.species?.name || 'Canino'}</strong>
                            </div>
                            <div>
                                <span className="text-gray-500">Raza:</span>{' '}
                                <strong className="text-gray-900">{pet?.breed?.name || 'Mestizo'}</strong>
                            </div>
                            <div>
                                <span className="text-gray-500">Sexo:</span>{' '}
                                <strong className="text-gray-900">{pet?.sex === 'M' ? 'Macho' : 'Hembra'}</strong>
                            </div>
                            <div>
                                <span className="text-gray-500">Edad Estimada:</span>{' '}
                                <strong className="text-gray-900">{pet?.estimated_age ? `${pet.estimated_age} años` : 'Joven'}</strong>
                            </div>
                            <div>
                                <span className="text-gray-500">Color:</span>{' '}
                                <strong className="text-gray-900">{pet?.color?.name || 'Distintivo'}</strong>
                            </div>
                            <div>
                                <span className="text-gray-500">Peso:</span>{' '}
                                <strong className="text-gray-900">{pet?.weight_kg ? `${pet.weight_kg} kg` : 'Mediano'}</strong>
                            </div>
                            <div>
                                <span className="text-gray-500">Identificador:</span>{' '}
                                <strong className="text-gray-900">ID #{pet?.id || '—'}</strong>
                            </div>
                        </div>
                    </div>

                    {/* Cláusulas Formales de Tenencia Responsable */}
                    <div className="space-y-3 text-justify">
                        <h4 className="font-bold text-gray-900 uppercase tracking-wider text-[11px] border-b border-gray-200 pb-1">
                            CLÁUSULAS DEL COMPROMISO DE TENENCIA RESPONSABLE
                        </h4>

                        <p>
                            <strong>PRIMERA (NATURALEZA DE LA ADOPCIÓN):</strong> La entrega del animal identificado se realiza con fines altruistas y de bienestar animal, no constituyendo una transacción mercantil ni enajenación de dominio con fines de lucro.
                        </p>
                        <p>
                            <strong>SEGUNDA (OBLIGACIONES DE CUIDADO Y SALUD):</strong> EL ADOPTANTE se compromete a suministrar alimentación nutritiva, agua limpia constante, espacio ventilado y resguardado de la intemperie, así como atención médico-veterinaria periódica (vacunas de refuerzo y desparasitaciones semestrales).
                        </p>
                        <p>
                            <strong>TERCERA (PROHIBICIÓN DE MALTRATO O AISLAMIENTO):</strong> Queda terminantemente prohibido mantener al animal permanentemente encadenado, confinado en azoteas, jaulas inadecuadas o expuesto a violencia física o psicológica.
                        </p>
                        <p>
                            <strong>CUARTA (NO REPRODUCCIÓN NI VENTA):</strong> El animal adoptado no podrá ser utilizado con fines de reproducción, peleas, guardia agresiva, ni podrá ser vendido o cedido a terceros sin previa autorización escrita de EL REFUGIO.
                        </p>
                        <p>
                            <strong>QUINTA (SUPERVISIÓN POST-ADOPCIÓN):</strong> EL ADOPTANTE autoriza voluntariamente al personal de EL REFUGIO a realizar visitas domiciliarias de seguimiento y llamadas de control durante el primer año para corroborar la adaptación del animal.
                        </p>
                        <p>
                            <strong>SEXTA (RESTITUCIÓN VOLUNTARIA):</strong> En caso de que por fuerza mayor EL ADOPTANTE no pueda continuar velando por el bienestar del animal, se compromete a restituirlo de manera inmediata y exclusiva a EL REFUGIO.
                        </p>
                    </div>

                    {/* Bloque de Firmas */}
                    <div className="pt-8 border-t border-gray-300 grid grid-cols-1 sm:grid-cols-2 gap-8 text-center">
                        <div className="space-y-1">
                            <div className="h-14 border-b border-gray-400 mx-auto w-3/4 flex items-end justify-center pb-1">
                                <span className="text-[11px] text-gray-400 italic">Firma del Adoptante</span>
                            </div>
                            <p className="font-bold text-gray-900">{person?.name || 'Adoptante Responsable'}</p>
                            <p className="text-[11px] text-gray-500 font-mono">DPI / CUI: {person?.document_number || '______________'}</p>
                            <p className="text-[10px] text-gray-400">Adoptante Tutor</p>
                        </div>

                        <div className="space-y-1">
                            <div className="h-14 border-b border-gray-400 mx-auto w-3/4 flex items-end justify-center pb-1">
                                <span className="text-[11px] text-emerald-800 font-semibold">DIRECTOR DEL REFUGIO</span>
                            </div>
                            <p className="font-bold text-gray-900">{shelter?.name || 'Dirección de Adopciones'}</p>
                            <p className="text-[11px] text-gray-500 font-mono">SELLO Y CONSTANCIA LEGAL</p>
                            <p className="text-[10px] text-gray-400">Por El Refugio Cedente</p>
                        </div>
                    </div>
                </div>

                <div className="flex justify-end print:hidden">
                    <Button variant="outline" onClick={onClose}>
                        Cerrar Visor
                    </Button>
                </div>
            </div>
        </Modal>
    );
};

