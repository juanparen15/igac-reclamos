<?php

namespace App\Filament\Ciudadano\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Filament\Notifications\Notification;
use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;

class Register extends BaseRegister
{
    protected static string $view = 'filament-panels::pages.auth.register';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        Select::make('tipo_documento')
                            ->label('Tipo de Documento')
                            ->options([
                                'CC' => 'Cédula de Ciudadanía',
                                'CE' => 'Cédula de Extranjería',
                                'TI' => 'Tarjeta de Identidad',
                                'PAS' => 'Pasaporte',
                                'NIT' => 'NIT',
                            ])
                            ->required()
                            ->default('CC')
                            ->native(false),
                        TextInput::make('numero_documento')
                            ->label('Número de Documento')
                            ->required()
                            ->unique(table: User::class, column: 'numero_documento')
                            ->minLength(6)
                            ->maxLength(20)
                            ->alphaNum()
                            ->helperText('Ingrese su número de documento sin puntos ni espacios')
                            ->validationMessages([
                                'unique' => 'Este número de documento ya está registrado.',
                                'alpha_num' => 'El número de documento solo debe contener letras y números.',
                            ]),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function handleRegistration(array $data): Model
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'tipo_documento' => $data['tipo_documento'],
            'numero_documento' => $data['numero_documento'],
            'password' => $data['password'],
        ]);

        $user->assignRole('ciudadano');

        $nombreParts = preg_split('/\s+/', trim($data['name']));
        
        $primerNombre = $nombreParts[0] ?? '';
        $segundoNombre = '';
        $primerApellido = '';
        $segundoApellido = '';

        if (count($nombreParts) === 2) {
            $primerApellido = $nombreParts[1];
        } elseif (count($nombreParts) === 3) {
            $segundoNombre = $nombreParts[1];
            $primerApellido = $nombreParts[2];
        } elseif (count($nombreParts) >= 4) {
            $segundoNombre = $nombreParts[1];
            $primerApellido = $nombreParts[2];
            $segundoApellido = implode(' ', array_slice($nombreParts, 3));
        }

        $user->ciudadano()->create([
            'primer_nombre' => $primerNombre,
            'segundo_nombre' => $segundoNombre,
            'primer_apellido' => $primerApellido ?: 'Por completar',
            'segundo_apellido' => $segundoApellido,
            'perfil_completo' => false,
        ]);

        // ✅ Notificación de bienvenida
        Notification::make()
            ->success()
            ->title('¡Bienvenido!')
            ->body('Por favor complete su perfil para poder crear reclamos.')
            ->persistent()
            ->send();

        return $user;
    }

    // ✅ MÉTODO CLAVE: Redirige al perfil después del registro
    protected function getRedirectUrl(): string
    {
        $ciudadano = auth()->user()->ciudadano;
        
        return PerfilCiudadanoResource::getUrl('edit', ['record' => $ciudadano->id]);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Nombre Completo')
            ->required()
            ->maxLength(255)
            ->helperText('Ingrese sus nombres y apellidos completos')
            ->placeholder('Ej: Juan Carlos Pérez Gómez')
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }
}