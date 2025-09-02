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
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

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
                    ->description('Estos datos fueron registrados durante su registro')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('tipo_documento')
                                    ->label('Tipo de Documento')
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('numero_documento')
                                    ->label('Número de Documento')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Información Personal')
                    ->description('Complete todos los campos para poder crear reclamos')
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
                            ->maxLength(20),
                        Forms\Components\Select::make('genero')
                            ->label('Género')
                            ->options([
                                'M' => 'Masculino',
                                'F' => 'Femenino',
                                'O' => 'Otro',
                            ])
                            ->required()
                            ->native(false),
                        // Forms\Components\Textarea::make('direccion_notificacion')
                        //     ->label('Dirección de Notificación')
                        //     ->required()
                        //     ->rows(2)
                        //     ->maxLength(255),
                        // Forms\Components\DatePicker::make('fecha_nacimiento')
                        //     ->label('Fecha de Nacimiento')
                        //     ->required()
                        //     ->maxDate(now()->subYears(18))
                        //     ->displayFormat('d/m/Y'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('departamento_id')
                                    ->label('Departamento de Nacimiento')
                                    // ->relationship(name: 'departamento', titleAttribute: 'nombre')
                                    ->options(Departamento::pluck('nombre', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    // ->afterStateUpdated(function (Set $set) {
                                    //     $set('ciudad_id', null);
                                    // })
                                    ->afterStateUpdated(fn(Forms\Set $set) => $set('ciudad_id', null)),
                                Forms\Components\Select::make('ciudad_id')
                                    ->label('Ciudad de Nacimiento')
                                    // ->options(fn(Get $get): Collection => Ciudad::query()
                                    //     ->where('departamento_id', $get('departamento_id'))
                                    //     ->pluck('nombre', 'id'))
                                    ->options(
                                        fn(Get $get): array =>
                                        Ciudad::where('departamento_id', $get('departamento_id'))
                                            ->where('estado', true)
                                            ->pluck('nombre', 'id')
                                            ->toArray()
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                            ]),
                        // Forms\Components\TextInput::make('condicion_especial')
                        //     ->label('Condición Especial')
                        //     ->helperText('Si tiene alguna condición especial, por favor especifique')
                        //     ->maxLength(100),
                        // Forms\Components\FileUpload::make('foto_perfil')
                        //     ->label('Foto de Perfil')
                        //     ->image()
                        //     ->imageEditor()
                        //     ->imageEditorAspectRatios([
                        //         '1:1',
                        //     ])
                        //     ->directory('perfiles')
                        //     ->disk('public')
                        //     ->maxSize(5120)
                        //     ->helperText('Tamaño máximo: 5MB. Formatos: JPG, PNG'),
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
                    ->label('Completar Perfil'),
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
        return false; // No permitir crear nuevos perfiles
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
