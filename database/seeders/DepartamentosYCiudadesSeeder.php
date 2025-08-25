<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\Ciudad;

class DepartamentosYCiudadesSeeder extends Seeder
{
    public function run()
    {
        $departamentos = [
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

        foreach ($departamentos as $depto) {
            Departamento::create($depto);
        }

        // Agregar algunas ciudades principales
        $ciudades = [
            // Antioquia
            ['codigo' => '05001', 'nombre' => 'Medellín', 'departamento' => 'Antioquia'],
            ['codigo' => '05088', 'nombre' => 'Bello', 'departamento' => 'Antioquia'],
            ['codigo' => '05360', 'nombre' => 'Itagüí', 'departamento' => 'Antioquia'],
            
            // Atlántico
            ['codigo' => '08001', 'nombre' => 'Barranquilla', 'departamento' => 'Atlántico'],
            ['codigo' => '08078', 'nombre' => 'Baranoa', 'departamento' => 'Atlántico'],
            
            // Bogotá
            ['codigo' => '11001', 'nombre' => 'Bogotá D.C.', 'departamento' => 'Bogotá D.C.'],
            
            // Bolívar
            ['codigo' => '13001', 'nombre' => 'Cartagena', 'departamento' => 'Bolívar'],
            ['codigo' => '13430', 'nombre' => 'Magangué', 'departamento' => 'Bolívar'],
            
            // Valle del Cauca
            ['codigo' => '76001', 'nombre' => 'Cali', 'departamento' => 'Valle del Cauca'],
            ['codigo' => '76111', 'nombre' => 'Buenaventura', 'departamento' => 'Valle del Cauca'],
            ['codigo' => '76520', 'nombre' => 'Palmira', 'departamento' => 'Valle del Cauca'],
        ];

        foreach ($ciudades as $ciudad) {
            Ciudad::create($ciudad);
        }
    }
}