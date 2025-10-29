<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CiudadanoResource\Pages;
use App\Models\Ciudadano;
use App\Models\Departamento;
use App\Models\Ciudad;
use App\Models\CampoDinamico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CiudadanoResource extends Resource
{
    protected static ?string $model = Ciudadano::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
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
                            ->required(),
                        Forms\Components\TextInput::make('numero_documento')
                            ->label('Número de Documento')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('primer_nombre')
                                    ->label('Primer Nombre')
                                    ->required(),
                                Forms\Components\TextInput::make('segundo_nombre')
                                    ->label('Segundo Nombre'),
                                Forms\Components\TextInput::make('primer_apellido')
                                    ->label('Primer Apellido')
                                    ->required(),
                                Forms\Components\TextInput::make('segundo_apellido')
                                    ->label('Segundo Apellido'),
                            ]),
                        Forms\Components\TextInput::make('numero_celular')
                            ->label('Número de Celular')
                            ->tel()
                            ->required(),
                        Forms\Components\Textarea::make('direccion_notificacion')
                            ->label('Dirección de Notificación')
                            ->required()
                            ->rows(2),
                        Forms\Components\DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->required()
                            ->maxDate(now()),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('departamento_id')
                                    ->label('Departamento de Nacimiento')
                                    ->searchable()
                                    ->options(fn() => Departamento::pluck('nombre', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn(callable $set) => $set('ciudad_id', null)),
                                Forms\Components\Select::make('ciudad_id')
                                    ->label('Ciudad de Nacimiento')
                                    ->searchable()
                                    ->options(function (callable $get) {
                                        $departamento = $get('departamento_id');
                                        if (!$departamento) {
                                            return [];
                                        }
                                        return Ciudad::where('departamento_id', $departamento)
                                            ->pluck('nombre', 'nombre');
                                    })
                                    ->required(),
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
                            ->label('Condición Especial'),
                        Forms\Components\FileUpload::make('foto_perfil')
                            ->label('Foto de Perfil')
                            ->image()
                            ->directory('perfiles')
                            ->maxSize(5120),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto_perfil')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->searchable(['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido']),
                Tables\Columns\TextColumn::make('tipo_documento')
                    ->label('Tipo Doc.')
                    ->badge(),
                Tables\Columns\TextColumn::make('numero_documento')
                    ->label('Número Documento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_celular')
                    ->label('Celular'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('perfil_completo')
                    ->label('Perfil Completo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('reclamos_count')
                    ->label('Reclamos')
                    ->counts('reclamos')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('perfil_completo')
                    ->label('Estado del Perfil')
                    ->options([
                        true => 'Completo',
                        false => 'Incompleto',
                    ]),
                Tables\Filters\SelectFilter::make('genero')
                    ->label('Género')
                    ->options([
                        'M' => 'Masculino',
                        'F' => 'Femenino',
                        'O' => 'Otro',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCiudadanos::route('/'),
            'create' => Pages\CreateCiudadano::route('/create'),
            'edit' => Pages\EditCiudadano::route('/{record}/edit'),
            'view' => Pages\ViewCiudadano::route('/{record}'),
        ];
    }

    // public static function canViewAny(): bool
    // {
    //     return Auth::user()->can('gestionar_ciudadanos') ||
    //         Auth::user()->can('ver_ciudadanos') ||
    //         Auth::user()->can('crear_ciudadanos') ||
    //         Auth::user()->can('editar_ciudadanos') ||
    //         Auth::user()->can('eliminar_ciudadanos') ||
    //         Auth::user()->hasRole('admin');
    // }
}
