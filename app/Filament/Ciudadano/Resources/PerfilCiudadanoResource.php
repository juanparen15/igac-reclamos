<?php

namespace App\Filament\Ciudadano\Resources;

use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource\Pages;
use App\Models\Ciudadano;
use App\Models\Departamento;
use App\Models\Ciudad;
use App\Models\CampoDinamico;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PerfilCiudadanoResource extends Resource
{
    protected static ?string $model = Ciudadano::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $modelLabel = 'Mi Perfil';
    protected static ?string $pluralModelLabel = 'Mi Perfil';
    protected static ?string $navigationLabel = 'Mi Perfil';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Identificación')
                    ->description('Estos datos fueron registrados durante su registro y no pueden modificarse')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('user.tipo_documento')
                                    ->label('Tipo de Documento')
                                    ->content(fn($record) => $record?->user?->tipo_documento ?? 'No definido'),

                                Forms\Components\Placeholder::make('user.numero_documento')
                                    ->label('Número de Documento')
                                    ->content(fn($record) => $record?->user?->numero_documento ?? 'No definido'),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Información Personal')
                    ->description('Complete todos los campos requeridos para poder crear reclamos')
                    ->schema([
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
                            ->numeric()
                            ->rules(['regex:/^[3][0-9]{9}$/'])
                            ->validationMessages([
                                'regex' => 'Ingrese un número de celular válido colombiano (10 dígitos comenzando con 3)',
                            ])
                            ->helperText('Ejemplo: 3001234567'),
                        Forms\Components\Select::make('genero')
                            ->label('Género')
                            ->options([
                                'M' => 'Masculino',
                                'F' => 'Femenino',
                                'O' => 'Otro',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('departamento_id')
                                    ->label('Departamento de Residencia')
                                    ->options(Departamento::pluck('nombre', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn(Forms\Set $set) => $set('ciudad_id', null)),

                                Forms\Components\Select::make('ciudad_id')
                                    ->label('Ciudad de Residencia')
                                    ->options(
                                        fn(Get $get): array =>
                                        Ciudad::where('departamento_id', $get('departamento_id'))
                                            ->pluck('nombre', 'id')  // ✅ SIN filtro de estado
                                            ->toArray()
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->disabled(fn(Get $get): bool => !$get('departamento_id'))
                                    ->helperText('Seleccione primero un departamento'),
                            ]),
                        Forms\Components\FileUpload::make('foto_perfil')
                            ->label('Foto de Perfil')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->directory('perfiles')
                            ->disk('public')
                            ->maxSize(5120)
                            ->helperText('Tamaño máximo: 5MB. Formatos: JPG, PNG'),
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
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->default('Por completar'),
                Tables\Columns\TextColumn::make('documento_completo')
                    ->label('Documento'),
                Tables\Columns\TextColumn::make('numero_celular')
                    ->label('Celular')
                    ->default('Por completar'),
                Tables\Columns\IconColumn::make('perfil_completo')
                    ->label('Perfil Completo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar Perfil'),
            ])
            ->bulkActions([])
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerfilCiudadanos::route('/'),
            'edit' => Pages\EditPerfilCiudadano::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
