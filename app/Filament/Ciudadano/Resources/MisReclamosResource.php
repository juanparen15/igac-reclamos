<?php

namespace App\Filament\Ciudadano\Resources;

use App\Filament\Ciudadano\Resources\MisReclamosResource\Pages;
use App\Models\Reclamo;
use App\Models\TipoReclamo;
use App\Models\Ciudadano;
use App\Models\CampoDinamico;
use App\Notifications\NuevoReclamoNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class MisReclamosResource extends Resource
{
    protected static ?string $model = Reclamo::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Mi Reclamo';

    protected static ?string $pluralModelLabel = 'Mis Reclamos';

    protected static ?string $navigationLabel = 'Mis Reclamos';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Reclamo')
                    ->schema([
                        Forms\Components\CheckboxList::make('tipos_reclamo_ids')
                            ->label('Tipos de Reclamo')
                            ->helperText('Seleccione uno o más tipos de reclamo')
                            ->options(TipoReclamo::activo()->pluck('nombre', 'id'))
                            ->required()
                            ->columns(2),
                        Forms\Components\TextInput::make('asunto')
                            ->label('Asunto')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('mensaje')
                            ->label('Descripción del Reclamo')
                            ->helperText('Describa detalladamente su reclamo')
                            ->required()
                            ->rows(5),
                        Forms\Components\FileUpload::make('archivo_oficio')
                            ->label('Archivo de Oficio (PDF)')
                            ->helperText('Suba el documento oficial en formato PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('reclamos/oficios')
                            ->maxSize(10240),
                        Forms\Components\FileUpload::make('archivos_adicionales')
                            ->label('Documentos Adicionales')
                            ->helperText('Puede subir hasta 5 archivos adicionales (Cédula, documentos de propiedad, etc.)')
                            ->multiple()
                            ->directory('reclamos/adicionales')
                            ->maxSize(10240)
                            ->maxFiles(5),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Campos Adicionales')
                    ->schema(function () {
                        $campos = CampoDinamico::seccion('reclamo')->activo()->orderBy('orden')->get();
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
                    ->visible(fn() => CampoDinamico::seccion('reclamo')->activo()->exists()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_ticket')
                    ->label('Número de Ticket')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Número copiado'),
                Tables\Columns\TextColumn::make('asunto')
                    ->label('Asunto')
                    ->limit(50),
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'danger' => 'nuevo',
                        'warning' => 'en_proceso',
                        'success' => 'resuelto',
                        'gray' => 'cerrado',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'nuevo' => 'Nuevo',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'cerrado' => 'Cerrado',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_resolucion')
                    ->label('Fecha de Resolución')
                    ->dateTime()
                    ->placeholder('Pendiente'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'nuevo' => 'Nuevo',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'cerrado' => 'Cerrado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('imprimir_ticket')
                    ->label('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn(Reclamo $record) => in_array($record->estado, ['resuelto', 'cerrado']))
                    ->url(fn(Reclamo $record) => route('reclamo.ticket.imprimir', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMisReclamos::route('/'),
            'create' => Pages\CreateMisReclamos::route('/create'),
            'view' => Pages\ViewMisReclamos::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('ciudadano', function ($query) {
                $query->where('user_id', auth()->id());
            });
    }

    public static function canCreate(): bool
    {
        $ciudadano = auth()->user()->ciudadano;
        return $ciudadano && $ciudadano->perfil_completo;
    }

    // public static function canCreate(): bool
    // {
    //     $ciudadano = Ciudadano::where('user_id', auth()->id())->first();

    //     if (!$ciudadano || !$ciudadano->perfil_completo) {
    //         return false;
    //     }

    //     return true;
    // }

    public static function shouldRegisterNavigation(): bool
    {
        $ciudadano = Ciudadano::where('user_id', auth()->id())->first();

        // Solo mostrar el menú de reclamos si el perfil está completo
        return $ciudadano && $ciudadano->perfil_completo;
    }
}
