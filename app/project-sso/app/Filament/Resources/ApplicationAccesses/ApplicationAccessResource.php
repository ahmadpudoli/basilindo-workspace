<?php

namespace App\Filament\Resources\ApplicationAccesses;

use App\Filament\Resources\ApplicationAccesses\Pages\CreateApplicationAccess;
use App\Filament\Resources\ApplicationAccesses\Pages\EditApplicationAccess;
use App\Filament\Resources\ApplicationAccesses\Pages\ListApplicationAccesses;
use App\Models\ApplicationUserAccess;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApplicationAccessResource extends Resource
{
    protected static ?string $model = ApplicationUserAccess::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';
    protected static string|\UnitEnum|null $navigationGroup = 'SSO Administration';
    protected static ?string $navigationLabel = 'Application Access';
    protected static ?string $modelLabel = 'Application Access';
    protected static ?string $pluralModelLabel = 'Application Access';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->label('User')->relationship('user', 'email')->searchable()->preload()->required(),
            Select::make('application_id')->label('Application')->relationship('application', 'name')->searchable()->preload()->required(),
            TextInput::make('role_code')->label('Application role')->required()->alphaDash()->maxLength(100),
            Select::make('status')->options(['active' => 'Active', 'suspended' => 'Suspended'])->default('active')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')->label('User')->searchable(),
                TextColumn::make('application.name')->label('Application')->searchable(),
                TextColumn::make('role_code')->label('Role')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->recordActions([
                EditAction::make()->label('Edit'),
                Action::make('toggleStatus')
                    ->label(fn (ApplicationUserAccess $record): string => $record->status === 'active' ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn (ApplicationUserAccess $record): string => $record->status === 'active' ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                    ->color(fn (ApplicationUserAccess $record): string => $record->status === 'active' ? 'warning' : 'success')
                    ->authorize('update')
                    ->requiresConfirmation()
                    ->modalHeading(fn (ApplicationUserAccess $record): string => $record->status === 'active' ? 'Nonaktifkan akses?' : 'Aktifkan akses?')
                    ->modalDescription('Perubahan ini langsung memengaruhi kemampuan user untuk masuk ke aplikasi.')
                    ->action(function (ApplicationUserAccess $record): void {
                        $record->update(['status' => $record->status === 'active' ? 'suspended' : 'active']);
                    }),
                DeleteAction::make()->label('Hapus'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplicationAccesses::route('/'),
            'create' => CreateApplicationAccess::route('/create'),
            'edit' => EditApplicationAccess::route('/{record}/edit'),
        ];
    }
}
