<?php

namespace App\Filament\Ciudadano\Pages;

use App\Models\Ciudadano;
use App\Models\Departamento;
use App\Models\Ciudad;
use App\Models\CampoDinamico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Filament\Forms\Components\Select;

class MiPerfil extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.ciudadano.pages.mi-perfil';

    protected static ?string $title = 'Mi Perfil';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        // Separar el nombre completo en partes
        $nombreParts = preg_split('/\s+/', trim($user->name));

        // Inicializar variables
        $primerNombre = '';
        $segundoNombre = '';
        $primerApellido = '';
        $segundoApellido = '';

        // Asignar según la cantidad de partes encontradas
        if (count($nombreParts) === 1) {
            $primerNombre = $nombreParts[0];
        } elseif (count($nombreParts) === 2) {
            $primerNombre = $nombreParts[0];
            $primerApellido = $nombreParts[1];
        } elseif (count($nombreParts) === 3) {
            $primerNombre = $nombreParts[0];
            $segundoNombre = $nombreParts[1];
            $primerApellido = $nombreParts[2];
        } else {
            // 4 o más partes → asumimos los dos primeros como nombres y los dos últimos como apellidos
            $primerNombre = $nombreParts[0];
            $segundoNombre = $nombreParts[1];
            $primerApellido = $nombreParts[2];
            $segundoApellido = $nombreParts[3];
        }

        // Si no tiene perfil de ciudadano, lo creamos con los datos iniciales
        if (!$user->ciudadano) {
            $user->ciudadano()->create([
                'primer_nombre'     => $primerNombre,
                'segundo_nombre'    => $segundoNombre,
                'primer_apellido'   => $primerApellido ?: 'Pendiente',
                'segundo_apellido'  => $segundoApellido,
                'perfil_completo'   => false,
            ]);
            $user->refresh();
        }

        $ciudadano = $user->ciudadano;

        // Llenar formulario
        $this->form->fill([
            // Datos que vienen de users
            'tipo_documento'       => $user->tipo_documento,
            'numero_documento'     => $user->numero_documento,

            // Datos que vienen de ciudadano
            'primer_nombre'        => $ciudadano->primer_nombre,
            'segundo_nombre'       => $ciudadano->segundo_nombre,
            'primer_apellido'      => $ciudadano->primer_apellido,
            'segundo_apellido'     => $ciudadano->segundo_apellido,
            'numero_celular'       => $ciudadano->numero_celular,
            'direccion_notificacion' => $ciudadano->direccion_notificacion,
            'fecha_nacimiento'     => $ciudadano->fecha_nacimiento,
            'departamento_id'      => $ciudadano->departamento_id,
            'ciudad_id'            => $ciudadano->ciudad_id,
            'genero'               => $ciudadano->genero,
            'condicion_especial'   => $ciudadano->condicion_especial,
            'foto_perfil'          => $ciudadano->foto_perfil,
            'campos_adicionales'   => $ciudadano->campos_adicionales ?? [],
        ]);
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->description('Complete todos los campos requeridos para poder crear reclamos')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\FileUpload::make('foto_perfil')
                                    ->label('Foto de Perfil')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ])
                                    ->directory('perfiles')
                                    ->maxSize(5120)
                                    ->helperText('Tamaño máximo: 5MB. Formatos: JPG, PNG'),

                                // Forms\Components\FileUpload::make('foto_perfil')
                                //     ->label('Foto de Perfil')
                                //     ->image()
                                //     ->avatar()
                                //     ->directory('perfiles')
                                //     ->maxSize(5120)
                                //     ->columnSpan(1),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('tipo_documento')
                                            ->label('Tipo de Documento')
                                            ->options([
                                                'CC' => 'Cédula de Ciudadanía',
                                                'CE' => 'Cédula de Extranjería',
                                                'TI' => 'Tarjeta de Identidad',
                                                'PAS' => 'Pasaporte',
                                                'NIT' => 'NIT',
                                            ])
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->helperText('Definido en el registro'),
                                        Forms\Components\TextInput::make('numero_documento')
                                            ->label('Número de Documento')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->helperText('Definido en el registro')
                                        // ->unique(ignoreRecord: true),
                                    ])
                                    ->columnSpan(2),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('primer_nombre')
                                    ->label('Primer Nombre')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('segundo_nombre')
                                    ->label('Segundo Nombre')
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('primer_apellido')
                                    ->label('Primer Apellido')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('segundo_apellido')
                                    ->label('Segundo Apellido')
                                    ->maxLength(50),
                            ]),
                        Forms\Components\TextInput::make('numero_celular')
                            ->label('Número de Celular')
                            ->tel()
                            ->required()
                            ->prefixIcon('heroicon-o-phone')
                            ->prefix('+57')
                            ->minLength(10)
                            ->maxLength(10)
                            ->rules([
                                'regex:/^[3][0-9]{9}$/' // Formato colombiano
                            ])
                            ->validationMessages([
                                'regex' => 'Ingrese un número de celular válido (10 dígitos)',
                            ]),
                        Forms\Components\Textarea::make('direccion_notificacion')
                            ->label('Dirección de Notificación')
                            ->required()
                            ->rows(2),
                        Forms\Components\DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->required()
                            ->maxDate(now()->subYears(18))
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Section::make('Lugar de Nacimiento')
                                    ->schema([
                                        Select::make('departamento_id')
                                            ->label('Departamento de Nacimiento')
                                            ->relationship(name: 'departamento', titleAttribute: 'nombre')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->required(),
                                        Select::make('ciudad_id')
                                            ->label('Ciudad de Nacimiento')
                                            ->options(fn(Get $get): Collection => Ciudad::query()
                                                ->where('departamento_id', $get('departamento_id'))
                                            ->pluck('nombre', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->required(),
                                    ]),
                            ]),
                        Forms\Components\Select::make('genero')
                            ->label('Género')
                            ->options([
                                'M' => 'Masculino',
                                'F' => 'Femenino',
                                'O' => 'Otro',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('condicion_especial')
                            ->label('Condición Especial')
                            ->helperText('Indique si tiene alguna condición especial o discapacidad'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Campos Adicionales')
                    ->schema(function () {
                        $campos = CampoDinamico::seccion('perfil')->activo()->orderBy('orden')->get();
                        $components = [];

                        foreach ($campos as $campo) {
                            $component = match ($campo->tipo) {
                                'text' => Forms\Components\TextInput::make("campos_adicionales.{$campo->nombre}")
                                    ->label($campo->etiqueta),
                                'textarea' => Forms\Components\Textarea::make("campos_adicionales.{$campo->nombre}")
                                    ->label($campo->etiqueta),
                                'select' => Forms\Components\Select::make("campos_adicionales.{$campo->nombre}")
                                    ->label($campo->etiqueta)
                                    ->options($campo->opciones ?? []),
                                'date' => Forms\Components\DatePicker::make("campos_adicionales.{$campo->nombre}")
                                    ->label($campo->etiqueta),
                                'checkbox' => Forms\Components\Checkbox::make("campos_adicionales.{$campo->nombre}")
                                    ->label($campo->etiqueta),
                                default => Forms\Components\TextInput::make("campos_adicionales.{$campo->nombre}")
                                    ->label($campo->etiqueta),
                            };

                            if ($campo->requerido) {
                                $component->required();
                            }

                            $components[] = $component;
                        }

                        return $components;
                    })
                    ->columns(2)
                    ->collapsed()
                    ->visible(fn() => CampoDinamico::seccion('perfil')->activo()->exists()),

                // Forms\Components\Section::make('Estado del Perfil')
                //     ->schema([
                //         Forms\Components\Placeholder::make('estado_perfil')
                //             ->label('')
                //             ->content(function ($record) {
                //                 if (!$record->perfil_completo) {
                //                     return new \Illuminate\Support\HtmlString(
                //                         '<div class="flex items-center gap-2 p-4 bg-warning-50 dark:bg-warning-900/20 rounded-lg">
                //                             <svg class="w-5 h-5 text-warning-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                //                                 <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                //                             </svg>
                //                             <div>
                //                                 <p class="font-semibold text-warning-700">Perfil Incompleto</p>
                //                                 <p class="text-sm text-warning-600">Complete todos los campos requeridos para poder crear reclamos</p>
                //                             </div>
                //                         </div>'
                //                     );
                //                 }

                //                 return new \Illuminate\Support\HtmlString(
                //                     '<div class="flex items-center gap-2 p-4 bg-success-50 dark:bg-success-900/20 rounded-lg">
                //                         <svg class="w-5 h-5 text-success-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                //                             <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                //                         </svg>
                //                         <div>
                //                             <p class="font-semibold text-success-700">Perfil Completo</p>
                //                             <p class="text-sm text-success-600">Ya puede crear reclamos en el sistema</p>
                //                         </div>
                //                     </div>'
                //                 );
                //             }),
                //     ])
                //     ->columnSpan('full'),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar Perfil')
                ->submit('save'),
        ];
    }

    // public function save(): void
    // {
    //     $data = $this->form->getState();

    //     $ciudadano = auth()->user()->ciudadano;

    //     // Actualizar los datos del ciudadano
    //     $ciudadano->update([
    //         'primer_nombre' => $data['primer_nombre'],
    //         'segundo_nombre' => $data['segundo_nombre'],
    //         'primer_apellido' => $data['primer_apellido'],
    //         'segundo_apellido' => $data['segundo_apellido'],
    //         'numero_celular' => $data['numero_celular'],
    //         'direccion_notificacion' => $data['direccion_notificacion'],
    //         'fecha_nacimiento' => $data['fecha_nacimiento'],
    //         'departamento_id' => $data['departamento_id'],
    //         'ciudad_id' => $data['ciudad_id'],
    //         'genero' => $data['genero'],
    //         'condicion_especial' => $data['condicion_especial'],
    //         'foto_perfil' => $data['foto_perfil'],
    //         'campos_adicionales' => $data['campos_adicionales'] ?? [],
    //     ]);

    //     // Verificar si el perfil está completo
    //     $ciudadano->verificarPerfilCompleto();

    //     Notification::make()
    //         ->success()
    //         ->title('Perfil actualizado')
    //         ->body('Su perfil ha sido actualizado exitosamente.')
    //         ->send();

    //     if ($ciudadano->perfil_completo) {
    //         Notification::make()
    //             ->success()
    //             ->title('¡Perfil completo!')
    //             ->body('Ahora puede crear reclamos en el sistema.')
    //             ->persistent()
    //             ->send();
    //     }
    // }
    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $ciudadano = auth()->user()->ciudadano;
            $ciudadano->update([
                'primer_nombre' => $data['primer_nombre'],
                'segundo_nombre' => $data['segundo_nombre'],
                'primer_apellido' => $data['primer_apellido'],
                'segundo_apellido' => $data['segundo_apellido'],
                'numero_celular' => $data['numero_celular'],
                'direccion_notificacion' => $data['direccion_notificacion'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'departamento_id' => $data['departamento_id'],
                'ciudad_id' => $data['ciudad_id'],
                'genero' => $data['genero'],
                'condicion_especial' => $data['condicion_especial'],
                'foto_perfil' => $data['foto_perfil'],
                'campos_adicionales' => $data['campos_adicionales'] ?? [],
            ]);

            $ciudadano->verificarPerfilCompleto();

            Notification::make()
                ->success()
                ->title('Perfil actualizado')
                ->body('Su perfil ha sido actualizado exitosamente.')
                ->send();

            if ($ciudadano->perfil_completo) {
                Notification::make()
                    ->success()
                    ->title('¡Perfil completo!')
                    ->body('Ahora puede crear reclamos en el sistema.')
                    ->persistent()
                    ->send();
            }

            DB::listen(function ($query) {
                logger()->info($query->sql, $query->bindings);
            });
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Error al guardar')
                ->body($e->getMessage())
                ->send();
        }
    }
}
