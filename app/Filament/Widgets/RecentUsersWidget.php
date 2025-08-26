<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentUsersWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Usuarios Registrados Recientemente';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=FFFFFF&background=' . substr(md5($record->name), 0, 6))
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('Usuario')
                    ->description(fn($record) => $record->email)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Role')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->colors([
                        'danger' => 'admin',
                        'warning' => 'funcionario',
                        'info' => 'ciudadano',
                    ]),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Correo Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->since(),
            ])
            ->paginated(false)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.users.view', $record))
                    ->size('sm'),
            ]);
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->can('ver_estadisticas');
    }
}
