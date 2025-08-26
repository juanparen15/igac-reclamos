<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampoDinamicoResource\Pages;
use App\Models\CampoDinamico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CampoDinamicoResource extends Resource
{
    protected static ?string $model = CampoDinamico::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $label = 'Campo Dinámico';

    protected static ?string $pluralLabel = 'Campos Dinámicos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('seccion')
                    ->options([
                        'perfil' => 'Perfil de Ciudadano',
                        'reclamo' => 'Formulario de Reclamo',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    // ->alpha_dash()
                    ->helperText('Solo letras, números y guiones bajos'),
                Forms\Components\TextInput::make('etiqueta')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'text' => 'Texto',
                        'textarea' => 'Área de texto',
                        'select' => 'Lista desplegable',
                        'date' => 'Fecha',
                        'file' => 'Archivo',
                        'checkbox' => 'Casilla de verificación',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\KeyValue::make('opciones')
                    ->visible(fn(callable $get) => $get('tipo') === 'select')
                    ->helperText('Ingrese las opciones para la lista desplegable'),
                Forms\Components\Toggle::make('requerido')
                    ->default(false),
                Forms\Components\TextInput::make('orden')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seccion')
                    ->badge()
                    ->colors([
                        'primary' => 'perfil',
                        'success' => 'reclamo',
                    ]),
                Tables\Columns\TextColumn::make('etiqueta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->badge(),
                Tables\Columns\IconColumn::make('requerido')
                    ->boolean(),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('orden')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('seccion')
                    ->options([
                        'perfil' => 'Perfil de Ciudadano',
                        'reclamo' => 'Formulario de Reclamo',
                    ]),
                Tables\Filters\TernaryFilter::make('activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('orden')
            ->defaultSort('orden');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCampoDinamicos::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->can('gestionar_campos_dinamicos') ||
            Auth::user()->hasRole('admin');
    }
}
