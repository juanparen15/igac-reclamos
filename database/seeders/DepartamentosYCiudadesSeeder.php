<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\Ciudad;

class DepartamentosYCiudadesSeeder extends Seeder
{
    public function run(): void
    {
        $departamentosData = [
            ['codigo' => '05', 'nombre' => 'Antioquia'],
            ['codigo' => '08', 'nombre' => 'Atlántico'],
            ['codigo' => '11', 'nombre' => 'Bogotá D.C.'],
            ['codigo' => '13', 'nombre' => 'Bolívar'],
            ['codigo' => '15', 'nombre' => 'Boyacá'],
            ['codigo' => '17', 'nombre' => 'Caldas'],
            ['codigo' => '18', 'nombre' => 'Caquetá'],
            ['codigo' => '19', 'nombre' => 'Cauca'],
            ['codigo' => '20', 'nombre' => 'Cesar'],
            ['codigo' => '23', 'nombre' => 'Córdoba'],
            ['codigo' => '25', 'nombre' => 'Cundinamarca'],
            ['codigo' => '27', 'nombre' => 'Chocó'],
            ['codigo' => '41', 'nombre' => 'Huila'],
            ['codigo' => '44', 'nombre' => 'La Guajira'],
            ['codigo' => '47', 'nombre' => 'Magdalena'],
            ['codigo' => '50', 'nombre' => 'Meta'],
            ['codigo' => '52', 'nombre' => 'Nariño'],
            ['codigo' => '54', 'nombre' => 'Norte de Santander'],
            ['codigo' => '63', 'nombre' => 'Quindío'],
            ['codigo' => '66', 'nombre' => 'Risaralda'],
            ['codigo' => '68', 'nombre' => 'Santander'],
            ['codigo' => '70', 'nombre' => 'Sucre'],
            ['codigo' => '73', 'nombre' => 'Tolima'],
            ['codigo' => '76', 'nombre' => 'Valle del Cauca'],
            ['codigo' => '81', 'nombre' => 'Arauca'],
            ['codigo' => '85', 'nombre' => 'Casanare'],
            ['codigo' => '86', 'nombre' => 'Putumayo'],
            ['codigo' => '88', 'nombre' => 'San Andrés y Providencia'],
            ['codigo' => '91', 'nombre' => 'Amazonas'],
            ['codigo' => '94', 'nombre' => 'Guainía'],
            ['codigo' => '95', 'nombre' => 'Guaviare'],
            ['codigo' => '97', 'nombre' => 'Vaupés'],
            ['codigo' => '99', 'nombre' => 'Vichada'],
        ];

        // Crear departamentos
        foreach ($departamentosData as $dpto) {
            Departamento::create($dpto);
        }

        // ✅ CORREGIR: Ciudades con departamento_id
        $ciudadesData = [
            // Antioquia (id: 1)
            ['codigo' => '05001', 'nombre' => 'Medellín', 'departamento_id' => 1],
            ['codigo' => '05002', 'nombre' => 'Abejorral', 'departamento_id' => 1],
            ['codigo' => '05004', 'nombre' => 'Abriaquí', 'departamento_id' => 1],
            ['codigo' => '05021', 'nombre' => 'Alejandría', 'departamento_id' => 1],

            // Atlántico (id: 2)
            ['codigo' => '08001', 'nombre' => 'Barranquilla', 'departamento_id' => 2],
            ['codigo' => '08078', 'nombre' => 'Baranoa', 'departamento_id' => 2],

            // Bogotá (id: 3)
            ['codigo' => '11001', 'nombre' => 'Bogotá D.C.', 'departamento_id' => 3],

            // Bolívar (id: 4)
            ['codigo' => '13001', 'nombre' => 'Cartagena', 'departamento_id' => 4],
            ['codigo' => '13006', 'nombre' => 'Achí', 'departamento_id' => 4],

            // Boyacá (id: 5)
            ['codigo' => '15001', 'nombre' => 'Tunja', 'departamento_id' => 5],
            ['codigo' => '15022', 'nombre' => 'Almeida', 'departamento_id' => 5],
            ['codigo' => '15090', 'nombre' => 'Berbeo', 'departamento_id' => 5],

            // Caldas (id: 6)
            ['codigo' => '17001', 'nombre' => 'Manizales', 'departamento_id' => 6],
            ['codigo' => '17013', 'nombre' => 'Aguadas', 'departamento_id' => 6],

            // Caquetá (id: 7)
            ['codigo' => '18001', 'nombre' => 'Florencia', 'departamento_id' => 7],
            ['codigo' => '18029', 'nombre' => 'Albania', 'departamento_id' => 7],

            // Cauca (id: 8)
            ['codigo' => '19001', 'nombre' => 'Popayán', 'departamento_id' => 8],
            ['codigo' => '19022', 'nombre' => 'Almaguer', 'departamento_id' => 8],

            // Cesar (id: 9)
            ['codigo' => '20001', 'nombre' => 'Valledupar', 'departamento_id' => 9],
            ['codigo' => '20011', 'nombre' => 'Aguachica', 'departamento_id' => 9],

            // Córdoba (id: 10)
            ['codigo' => '23001', 'nombre' => 'Montería', 'departamento_id' => 10],
            ['codigo' => '23068', 'nombre' => 'Ayapel', 'departamento_id' => 10],

            // Cundinamarca (id: 11)
            ['codigo' => '25001', 'nombre' => 'Agua de Dios', 'departamento_id' => 11],
            ['codigo' => '25019', 'nombre' => 'Albán', 'departamento_id' => 11],
            ['codigo' => '25035', 'nombre' => 'Anapoima', 'departamento_id' => 11],

            // Chocó (id: 12)
            ['codigo' => '27001', 'nombre' => 'Quibdó', 'departamento_id' => 12],
            ['codigo' => '27006', 'nombre' => 'Acandí', 'departamento_id' => 12],

            // Huila (id: 13)
            ['codigo' => '41001', 'nombre' => 'Neiva', 'departamento_id' => 13],
            ['codigo' => '41006', 'nombre' => 'Acevedo', 'departamento_id' => 13],

            // La Guajira (id: 14)
            ['codigo' => '44001', 'nombre' => 'Riohacha', 'departamento_id' => 14],
            ['codigo' => '44035', 'nombre' => 'Albania', 'departamento_id' => 14],

            // Magdalena (id: 15)
            ['codigo' => '47001', 'nombre' => 'Santa Marta', 'departamento_id' => 15],
            ['codigo' => '47030', 'nombre' => 'Algarrobo', 'departamento_id' => 15],

            // Meta (id: 16)
            ['codigo' => '50001', 'nombre' => 'Villavicencio', 'departamento_id' => 16],
            ['codigo' => '50006', 'nombre' => 'Acacías', 'departamento_id' => 16],

            // Nariño (id: 17)
            ['codigo' => '52001', 'nombre' => 'Pasto', 'departamento_id' => 17],
            ['codigo' => '52019', 'nombre' => 'Albán', 'departamento_id' => 17],

            // Norte de Santander (id: 18)
            ['codigo' => '54001', 'nombre' => 'Cúcuta', 'departamento_id' => 18],
            ['codigo' => '54003', 'nombre' => 'Ábrego', 'departamento_id' => 18],

            // Quindío (id: 19)
            ['codigo' => '63001', 'nombre' => 'Armenia', 'departamento_id' => 19],
            ['codigo' => '63111', 'nombre' => 'Buenavista', 'departamento_id' => 19],

            // Risaralda (id: 20)
            ['codigo' => '66001', 'nombre' => 'Pereira', 'departamento_id' => 20],
            ['codigo' => '66045', 'nombre' => 'Apía', 'departamento_id' => 20],

            // Santander (id: 21)
            ['codigo' => '68001', 'nombre' => 'Bucaramanga', 'departamento_id' => 21],
            ['codigo' => '68013', 'nombre' => 'Aguada', 'departamento_id' => 21],

            // Sucre (id: 22)
            ['codigo' => '70001', 'nombre' => 'Sincelejo', 'departamento_id' => 22],
            ['codigo' => '70110', 'nombre' => 'Buenavista', 'departamento_id' => 22],

            // Tolima (id: 23)
            ['codigo' => '73001', 'nombre' => 'Ibagué', 'departamento_id' => 23],
            ['codigo' => '73024', 'nombre' => 'Alpujarra', 'departamento_id' => 23],

            // Valle del Cauca (id: 24)
            ['codigo' => '76001', 'nombre' => 'Cali', 'departamento_id' => 24],
            ['codigo' => '76020', 'nombre' => 'Alcalá', 'departamento_id' => 24],

            // Arauca (id: 25)
            ['codigo' => '81001', 'nombre' => 'Arauca', 'departamento_id' => 25],
            ['codigo' => '81065', 'nombre' => 'Arauquita', 'departamento_id' => 25],

            // Casanare (id: 26)
            ['codigo' => '85001', 'nombre' => 'Yopal', 'departamento_id' => 26],
            ['codigo' => '85010', 'nombre' => 'Aguazul', 'departamento_id' => 26],

            // Putumayo (id: 27)
            ['codigo' => '86001', 'nombre' => 'Mocoa', 'departamento_id' => 27],
            ['codigo' => '86219', 'nombre' => 'Colón', 'departamento_id' => 27],

            // San Andrés (id: 28)
            ['codigo' => '88001', 'nombre' => 'San Andrés', 'departamento_id' => 28],
            ['codigo' => '88564', 'nombre' => 'Providencia', 'departamento_id' => 28],

            // Amazonas (id: 29)
            ['codigo' => '91001', 'nombre' => 'Leticia', 'departamento_id' => 29],
            ['codigo' => '91263', 'nombre' => 'El Encanto', 'departamento_id' => 29],

            // Guainía (id: 30)
            ['codigo' => '94001', 'nombre' => 'Inírida', 'departamento_id' => 30],
            ['codigo' => '94885', 'nombre' => 'Barranco Minas', 'departamento_id' => 30],

            // Guaviare (id: 31)
            ['codigo' => '95001', 'nombre' => 'San José del Guaviare', 'departamento_id' => 31],
            ['codigo' => '95015', 'nombre' => 'Calamar', 'departamento_id' => 31],

            // Vaupés (id: 32)
            ['codigo' => '97001', 'nombre' => 'Mitú', 'departamento_id' => 32],
            ['codigo' => '97161', 'nombre' => 'Carurú', 'departamento_id' => 32],

            // Vichada (id: 33)
            ['codigo' => '99001', 'nombre' => 'Puerto Carreño', 'departamento_id' => 33],
            ['codigo' => '99524', 'nombre' => 'La Primavera', 'departamento_id' => 33],
        ];

        // Crear ciudades
        foreach ($ciudadesData as $ciudad) {
            Ciudad::create($ciudad);
        }

        $this->command->info('✅ Departamentos y ciudades creados exitosamente');
    }
}
