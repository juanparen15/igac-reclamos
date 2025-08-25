<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Section;
use Spatie\Permission\Models\Role;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Support\HtmlString;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Gestión de Usuarios';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\FileUpload::make('avatar')
                                    ->label('Foto de Perfil')
                                    ->image()
                                    ->avatar()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->directory('avatars')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-user')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Section::make('Información Personal')
                                    ->icon('heroicon-o-identification')
                                    ->description('Datos de identificación del usuario')
                                    ->schema([
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
                                                    ->required()
                                                    ->native(false)
                                                    ->prefixIcon('heroicon-o-identification'),
                                                Forms\Components\TextInput::make('numero_documento')
                                                    ->label('Número de Documento')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(20)
                                                    ->prefixIcon('heroicon-o-hashtag'),
                                            ]),
                                    ]),

                                Section::make('Seguridad y Acceso')
                                    ->icon('heroicon-o-shield-check')
                                    ->description('Configuración de contraseña y roles')
                                    ->schema([
                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required(fn(string $context): bool => $context === 'create')
                                            ->maxLength(255)
                                            ->label(fn(string $context): string => $context === 'create' ? 'Contraseña' : 'Nueva Contraseña')
                                            ->helperText(fn(string $context): string => $context === 'edit' ? 'Dejar en blanco para mantener la contraseña actual' : 'Mínimo 8 caracteres')
                                            ->prefixIcon('heroicon-o-lock-closed')
                                            ->revealable(),

                                        Forms\Components\CheckboxList::make('roles')
                                            ->relationship('roles', 'name')
                                            ->options(function () {
                                                return Role::where('name', '!=', 'ciudadano')
                                                    ->get()
                                                    ->mapWithKeys(function ($role) {
                                                        $icon = match ($role->name) {
                                                            'admin' => '🛡️',
                                                            'funcionario' => '💼',
                                                            default => '👤'
                                                        };
                                                        return [$role->id => $icon . ' ' . ucfirst($role->name)];
                                                    });
                                            })
                                            ->columns(2)
                                            ->gridDirection('row')
                                            ->bulkToggleable()
                                            ->helperText('Seleccione los roles del usuario'),
                                    ]),
                            ])
                            ->columnSpan(2),
                    ]),

                Section::make('Estado de la Cuenta')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('email_verified_at')
                                    ->label('Email Verificado')
                                    ->onIcon('heroicon-m-check-badge')
                                    ->offIcon('heroicon-m-x-circle')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Estado de verificación del email')
                                    ->afterStateHydrated(function (Forms\Components\Toggle $component, $state) {
                                        $component->state(filled($state));
                                    })
                                    ->dehydrateStateUsing(fn($state) => $state ? now() : null),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Cuenta Activa')
                                    ->default(true)
                                    ->onIcon('heroicon-m-check-circle')
                                    ->offIcon('heroicon-m-x-circle')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Permite o bloquea el acceso del usuario'),

                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Miembro desde')
                                    ->content(fn($record): string => $record ? $record->created_at->diffForHumans() : 'Nuevo usuario'),
                            ]),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Tables\Columns\ImageColumn::make('avatar')
                        ->label('Avatar')
                        ->circular()
                        ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                        ->grow(false),
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable(),
                        Tables\Columns\TextColumn::make('email')
                            ->color('gray')
                            ->searchable()
                            ->icon('heroicon-m-envelope')
                            ->iconPosition('before')
                            ->size('sm'),
                        Tables\Columns\TextColumn::make('documento_info')
                            ->getStateUsing(fn($record) => "{$record->tipo_documento} {$record->numero_documento}")
                            ->icon('heroicon-m-identification')
                            ->iconPosition('before')
                            ->color('gray')
                            ->size('sm'),
                    ]),
                ]),
                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Roles')
                    ->separator(',')
                    ->colors([
                        'danger' => 'admin',
                        'warning' => 'funcionario',
                        'info' => 'ciudadano',
                    ])
                    ->icons([
                        'heroicon-m-shield-check' => 'admin',
                        'heroicon-m-briefcase' => 'funcionario',
                        'heroicon-m-user' => 'ciudadano',
                    ]),
                Tables\Columns\ToggleColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->onIcon('heroicon-m-check-badge')
                    ->offIcon('heroicon-m-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
                    ->beforeStateUpdated(function ($record, $state) {
                        $record->update([
                            'email_verified_at' => $state ? now() : null
                        ]);
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        \Filament\Notifications\Notification::make()
                            ->title($state ? 'Email verificado' : 'Verificación removida')
                            ->success()
                            ->send();
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->indicator('Rol'),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verificado')
                    ->nullable()
                    ->trueLabel('Verificados')
                    ->falseLabel('No verificados')
                    ->placeholder('Todos'),
                Tables\Filters\SelectFilter::make('tipo_documento')
                    ->options([
                        'CC' => 'Cédula de Ciudadanía',
                        'CE' => 'Cédula de Extranjería',
                        'TI' => 'Tarjeta de Identidad',
                        'PAS' => 'Pasaporte',
                        'NIT' => 'NIT',
                    ])
                    ->indicator('Tipo Doc.'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-m-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-m-pencil-square'),
                    Tables\Actions\Action::make('changePassword')
                        ->label('Cambiar Contraseña')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->modalHeading('Cambiar Contraseña')
                        ->modalWidth('md')
                        ->form([
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->label('Nueva Contraseña')
                                ->required()
                                ->validationMessages([
                                    'required' => 'El campo :attribute es obligatorio' // Tu mensaje personalizado
                                ])
                                ->minLength(8)
                                ->confirmed()
                                ->prefixIcon('heroicon-o-lock-closed'),
                            Forms\Components\TextInput::make('password_confirmation')
                                ->password()
                                ->label('Confirmar Contraseña')
                                ->required()
                                ->validationMessages([
                                    'required' => 'El campo :attribute es obligatorio' // Tu mensaje personalizado
                                ])
                                ->prefixIcon('heroicon-o-lock-closed'),
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->update([
                                'password' => Hash::make($data['password']),
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Contraseña actualizada')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('toggleActive')
                        ->label(fn(User $record) => $record->active ? 'Desactivar' : 'Activar')
                        ->icon(fn(User $record) => $record->active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(User $record) => $record->active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function (User $record): void {
                            $record->update(['active' => !$record->active]);

                            \Filament\Notifications\Notification::make()
                                ->title($record->active ? 'Usuario activado' : 'Usuario desactivado')
                                ->success()
                                ->send();
                        }),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('verify_emails')
                        ->label('Verificar Emails')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(function ($records): void {
                            $records->each->update(['email_verified_at' => now()]);

                            \Filament\Notifications\Notification::make()
                                ->title('Emails verificados')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('assign_role')
                        ->label('Asignar Rol')
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('role')
                                ->label('Rol')
                                ->options(Role::where('name', '!=', 'ciudadano')->pluck('name', 'id'))
                                ->validationMessages([
                                    'required' => 'El campo :attribute es obligatorio' // Tu mensaje personalizado
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            $role = Role::find($data['role']);
                            $records->each->assignRole($role);

                            \Filament\Notifications\Notification::make()
                                ->title('Rol asignado exitosamente')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasRole('admin')),
                ]),
            ])
            ->emptyStateHeading('No hay usuarios registrados')
            ->emptyStateDescription('Comienza creando el primer usuario del sistema')
            ->emptyStateIcon('heroicon-o-user-group')
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make()
                    ->schema([
                        \Filament\Infolists\Components\Split::make([
                            \Filament\Infolists\Components\ImageEntry::make('avatar')
                                ->label('Foto')
                                ->circular()
                                ->grow(false),
                            \Filament\Infolists\Components\Grid::make(2)
                                ->schema([
                                    \Filament\Infolists\Components\Group::make([
                                        \Filament\Infolists\Components\TextEntry::make('name')
                                            ->label('Nombre')
                                            ->weight(FontWeight::Bold),
                                        \Filament\Infolists\Components\TextEntry::make('email')
                                            ->label('Email')
                                            ->icon('heroicon-m-envelope')
                                            ->iconColor('primary')
                                            ->copyable()
                                            ->copyMessage('Email copiado'),
                                    ]),
                                    \Filament\Infolists\Components\Group::make([
                                        \Filament\Infolists\Components\TextEntry::make('tipo_documento')
                                            ->label('Tipo de Documento')
                                            ->badge(),
                                        \Filament\Infolists\Components\TextEntry::make('numero_documento')
                                            ->label('Número de Documento')
                                            ->copyable(),
                                    ]),
                                ]),
                        ]),
                    ])
                    ->columns(1),
                \Filament\Infolists\Components\Section::make('Roles y Permisos')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('roles.name')
                            ->label('Roles Asignados')
                            ->badge()
                            ->separator(',')
                            ->colors([
                                'danger' => 'admin',
                                'warning' => 'funcionario',
                                'info' => 'ciudadano',
                            ]),
                        \Filament\Infolists\Components\TextEntry::make('email_verified_at')
                            ->label('Email Verificado')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn($state) => $state ? 'Verificado' : 'No Verificado'),
                    ])
                    ->columns(2),
                \Filament\Infolists\Components\Section::make('Información Adicional')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Registro')
                            ->dateTime(),
                        \Filament\Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsed(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}
