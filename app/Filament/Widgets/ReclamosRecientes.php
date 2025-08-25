<?php

namespace App\Filament\Widgets;

use App\Models\Reclamo;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ReclamosRecientes extends BaseWidget
{
    protected static ?string $heading = 'Reclamos Recientes';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Reclamo::query()
                    ->with(['ciudadano', 'asignadoA'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('numero_ticket')
                    ->label('Ticket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ciudadano.nombre_completo')
                    ->label('Ciudadano')
                    ->limit(30),
                Tables\Columns\TextColumn::make('asunto')
                    ->label('Asunto')
                    ->limit(40),
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'danger' => 'nuevo',
                        'warning' => 'en_proceso',
                        'success' => 'resuelto',
                        'gray' => 'cerrado',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->since()
                    // ->dateTime()
                    // ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Reclamo $record): string => route('filament.admin.resources.reclamos.view', $record)),
            ])
            ->paginated(false);
    }
}
