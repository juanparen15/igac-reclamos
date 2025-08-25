<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReclamoResource\Pages;
use App\Models\Reclamo;
use App\Models\TipoReclamo;
use App\Models\User;
use App\Models\CampoDinamico;
use App\Notifications\ReclamoActualizadoNotification;
use App\Notifications\ReclamoResueltoNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;

class ReclamoResource extends Resource
{
    protected static ?string $model = Reclamo::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gestión de Reclamos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Reclamo')
                    ->schema([
                        Forms\Components\TextInput::make('numero_ticket')
                            ->label('Número de Ticket')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('ciudadano_id')
                            ->label('Ciudadano')
                            ->relationship('ciudadano', 'numero_documento')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nombre_completo} ({$record->documento_completo})")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn($context) => $context === 'edit'),
                        Forms\Components\CheckboxList::make('tipos_reclamo_ids')
                            ->label('Tipos de Reclamo')
                            ->options(TipoReclamo::activo()->pluck('nombre', 'id'))
                            ->required()
                            ->columns(2),
                        Forms\Components\TextInput::make('asunto')
                            ->label('Asunto')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('mensaje')
                            ->label('Mensaje del Reclamo')
                            ->required()
                            ->rows(5),
                        Forms\Components\FileUpload::make('archivo_oficio')
                            ->label('Archivo de Oficio (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('reclamos/oficios')
                            ->downloadable()
                            ->maxSize(10240),
                        Forms\Components\FileUpload::make('archivos_adicionales')
                            ->label('Archivos Adicionales')
                            ->multiple()
                            ->directory('reclamos/adicionales')
                            ->downloadable()
                            ->maxSize(10240)
                            ->maxFiles(5),
                    ])
                    ->columns(2),

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

                Forms\Components\Section::make('Gestión Interna')
                    ->schema([
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'nuevo' => 'Nuevo',
                                'en_proceso' => 'En Proceso',
                                'resuelto' => 'Resuelto',
                                'cerrado' => 'Cerrado',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $get, $set) {
                                if (in_array($state, ['resuelto', 'cerrado']) && !$get('fecha_resolucion')) {
                                    $set('fecha_resolucion', now());
                                }
                            }),
                        Forms\Components\Select::make('asignado_a')
                            ->label('Asignado a')
                            ->options(User::role(['admin', 'funcionario'])->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\DateTimePicker::make('fecha_resolucion')
                            ->label('Fecha de Resolución')
                            ->visible(fn(callable $get) => in_array($get('estado'), ['resuelto', 'cerrado']))
                            ->required(fn(callable $get) => in_array($get('estado'), ['resuelto', 'cerrado'])),
                        Forms\Components\Textarea::make('notas_internas')
                            ->label('Notas Internas')
                            ->rows(3),
                    ])
                    ->columns(2)
                    ->visible(fn() => auth()->user()->can('gestionar_reclamos')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_ticket')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Número copiado'),
                Tables\Columns\TextColumn::make('ciudadano.nombre_completo')
                    ->label('Ciudadano')
                    ->searchable(['ciudadanos.primer_nombre', 'ciudadanos.primer_apellido'])
                    ->description(fn (Reclamo $record): string => $record->ciudadano->documento_completo),
                Tables\Columns\TextColumn::make('asunto')
                    ->label('Asunto')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'danger' => 'nuevo',
                        'warning' => 'en_proceso',
                        'success' => 'resuelto',
                        'gray' => 'cerrado',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'nuevo' => 'Nuevo',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'cerrado' => 'Cerrado',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('asignadoA.name')
                    ->label('Asignado a')
                    ->placeholder('Sin asignar')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_resolucion')
                    ->label('Fecha Resolución')
                    ->dateTime('d/m/Y')
                    ->placeholder('Pendiente'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'nuevo' => 'Nuevo',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'cerrado' => 'Cerrado',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('asignado_a')
                    ->label('Asignado a')
                    ->options(User::role(['admin', 'funcionario'])->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\Filter::make('sin_asignar')
                    ->label('Sin asignar')
                    ->query(fn (Builder $query): Builder => $query->whereNull('asignado_a')),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                // Acciones rápidas de cambio de estado
                Tables\Actions\Action::make('asignar')
                    ->label('Asignar')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (Reclamo $record): bool => !$record->asignado_a && auth()->user()->can('asignar_reclamos'))
                    ->form([
                        Forms\Components\Select::make('asignado_a')
                            ->label('Asignar a')
                            ->options(User::role(['admin', 'funcionario'])->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (Reclamo $record, array $data): void {
                        $record->update([
                            'asignado_a' => $data['asignado_a'],
                            'estado' => 'en_proceso',
                        ]);
                        
                        Notification::make()
                            ->title('Reclamo asignado')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('resolver')
                    ->label('Resolver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Reclamo $record): bool => 
                        $record->estado === 'en_proceso' && 
                        auth()->user()->can('resolver_reclamos')
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Resolver Reclamo')
                    ->modalDescription('¿Está seguro que desea marcar este reclamo como resuelto?')
                    ->modalSubmitActionLabel('Sí, resolver')
                    ->action(function (Reclamo $record): void {
                        $record->update([
                            'estado' => 'resuelto',
                            'fecha_resolucion' => now(),
                        ]);
                        
                        Notification::make()
                            ->title('Reclamo resuelto')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('imprimir_ticket')
                    ->label('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn(Reclamo $record) => in_array($record->estado, ['resuelto', 'cerrado']))
                    ->url(fn(Reclamo $record) => route('reclamo.ticket.imprimir', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('asignar_masivo')
                        ->label('Asignar en masa')
                        ->icon('heroicon-o-user-plus')
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'asignado_a' => $data['funcionario_id'],
                                    'estado' => $record->estado === 'nuevo' ? 'en_proceso' : $record->estado,
                                ]);
                            });
                            
                            Notification::make()
                                ->title('Reclamos asignados')
                                ->success()
                                ->send();
                        })
                        ->form([
                            Forms\Components\Select::make('funcionario_id')
                                ->label('Asignar a')
                                ->options(User::role(['admin', 'funcionario'])->pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                        ])
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('cambiar_estado')
                        ->label('Cambiar estado')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $updateData = ['estado' => $data['estado']];
                                
                                if (in_array($data['estado'], ['resuelto', 'cerrado'])) {
                                    $updateData['fecha_resolucion'] = now();
                                }
                                
                                $record->update($updateData);
                            });
                            
                            Notification::make()
                                ->title('Estados actualizados')
                                ->success()
                                ->send();
                        })
                        ->form([
                            Forms\Components\Select::make('estado')
                                ->label('Nuevo estado')
                                ->options([
                                    'en_proceso' => 'En Proceso',
                                    'resuelto' => 'Resuelto',
                                    'cerrado' => 'Cerrado',
                                ])
                                ->required(),
                        ])
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn(): bool => auth()->user()->hasRole('admin')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListReclamos::route('/'),
            'create' => Pages\CreateReclamo::route('/create'),
            'edit' => Pages\EditReclamo::route('/{record}/edit'),
            'view' => Pages\ViewReclamo::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('ciudadano')) {
            $query->whereHas('ciudadano', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        return $query;
    }
    
    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->hasRole('admin')) {
            return static::getModel()::where('estado', 'nuevo')->count();
        }
        
        return null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}