<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\PermissionResource\Pages;
// use Spatie\Permission\Models\Permission;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;

// class PermissionResource extends Resource
// {
//     protected static ?string $model = Permission::class;

//     protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

//     protected static ?string $navigationLabel = 'Permisos';
    
//     protected static ?string $navigationGroup = 'Administración';
    
//     protected static ?int $navigationSort = 3;

//     public static function form(Form $form): Form
//     {
//         return $form
//             ->schema([
//                 Forms\Components\Section::make()
//                     ->schema([
//                         Forms\Components\TextInput::make('name')
//                             ->label('Nombre del Permiso')
//                             ->required()
//                             ->unique(ignoreRecord: true)
//                             ->maxLength(255),
//                         Forms\Components\Select::make('guard_name')
//                             ->label('Guard')
//                             ->options([
//                                 'web' => 'Web',
//                             ])
//                             ->default('web')
//                             ->required(),
//                     ])
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 Tables\Columns\TextColumn::make('name')
//                     ->label('Nombre')
//                     ->searchable()
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('guard_name')
//                     ->label('Guard')
//                     ->badge(),
//                 Tables\Columns\TextColumn::make('roles.name')
//                     ->label('Roles Asignados')
//                     ->badge()
//                     ->separator(','),
//                 Tables\Columns\TextColumn::make('created_at')
//                     ->label('Creado')
//                     ->dateTime('d/m/Y')
//                     ->sortable()
//                     ->toggleable(isToggledHiddenByDefault: true),
//             ])
//             ->filters([
//                 //
//             ])
//             ->actions([
//                 Tables\Actions\EditAction::make(),
//                 Tables\Actions\DeleteAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make()
//                         ->visible(fn () => auth()->user()->hasRole('admin')),
//                 ]),
//             ]);
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ManagePermissions::route('/'),
//         ];
//     }
// }