<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;


class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Roles y Permisos';

    protected static ?string $modelLabel = 'Role';

    protected static ?string $pluralModelLabel = 'Roles';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->heading('Información del Rol')
                    ->description('Configure el nombre y los permisos del rol')
                    ->schema([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre del Rol')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-o-shield-check')
                                    ->placeholder('Ej: supervisor, auditor')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Section::make('Permisos')
                            ->description('Seleccione los permisos que tendrá este rol')
                            ->schema([
                                Forms\Components\CheckboxList::make('permissions')
                                    ->relationship('permissions', 'name')
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->bulkToggleable()
                                    ->searchable()
                                    ->descriptions(function () {
                                        return Permission::all()->pluck('description', 'id')->toArray();
                                    })
                                    ->helperText('Los permisos definen qué acciones puede realizar un usuario con este rol'),
                            ])
                            ->collapsible(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label('Rol')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->searchable()
                            ->color(fn(string $state): string => match ($state) {
                                'admin' => 'danger',
                                'funcionario' => 'warning',
                                'ciudadano' => 'info',
                                default => 'gray'
                            })
                            ->icon(fn(string $state): string => match ($state) {
                                'admin' => 'heroicon-m-shield-exclamation',
                                'funcionario' => 'heroicon-m-briefcase',
                                'ciudadano' => 'heroicon-m-user',
                                default => 'heroicon-m-shield-check'
                            }),
                        Tables\Columns\TextColumn::make('permissions_count')
                            ->label('Permisos')
                            ->counts('permissions')
                            ->formatStateUsing(fn($state) => "{$state} permisos asignados")
                            ->color('gray')
                            ->icon('heroicon-m-key')
                            ->size('sm'),
                    ]),
                    Tables\Columns\TextColumn::make('users_count')
                        ->label('Usuarios')
                        ->counts('users')
                        ->formatStateUsing(fn($state) => $state)
                        ->badge()
                        ->color('success')
                        ->size('xl')
                        ->alignEnd()
                        ->extraAttributes(['class' => 'font-bold']),
                ]),
                Tables\Columns\TagsColumn::make('permissions.name')
                    ->label('Permisos')
                    ->limit(3)
                    ->separator(','),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                Tables\Filters\Filter::make('has_users')
                    ->label('Con usuarios')
                    ->query(fn(Builder $query): Builder => $query->has('users')),
                Tables\Filters\Filter::make('system_roles')
                    ->label('Roles del sistema')
                    ->query(fn(Builder $query): Builder => $query->whereIn('name', ['admin', 'funcionario', 'ciudadano'])),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplicar')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->visible(fn(Role $record): bool => !in_array($record->name, ['admin', 'funcionario', 'ciudadano']))
                        ->form([
                            Forms\Components\TextInput::make('name')
                                ->label('Nombre del nuevo rol')
                                ->required()
                                ->unique(Role::class, 'name'),
                        ])
                        ->action(function (Role $record, array $data): void {
                            $newRole = Role::create(['name' => $data['name']]);
                            $newRole->syncPermissions($record->permissions);

                            \Filament\Notifications\Notification::make()
                                ->title('Rol duplicado exitosamente')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn(Role $record): bool => !in_array($record->name, ['admin', 'funcionario', 'ciudadano'])),
                ])
                    ->button()
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasRole('admin')),
                ]),
            ])
            ->emptyStateHeading('No hay roles creados')
            ->emptyStateDescription('Los roles definen qué pueden hacer los usuarios en el sistema')
            ->emptyStateIcon('heroicon-o-shield-check');
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['users', 'permissions']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->can('gestionar_usuarios') ||
            Auth::user()->hasRole('admin');
    }
}
