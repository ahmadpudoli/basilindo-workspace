<?php

namespace App\Filament\Resources\Notifications;

use App\Filament\Support\HasWorkspaceModuleNavigation;
use App\Filament\Resources\Notifications\Pages\CreateNotification;
use App\Filament\Resources\Notifications\Pages\EditNotification;
use App\Filament\Resources\Notifications\Pages\ListNotifications;
use App\Models\Notification;
use Core\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    use HasWorkspaceModuleNavigation;
    protected static ?string $model = Notification::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';
    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Notifications';

    public static function workspaceModule(): string
    {
        return 'project-management';
    }
    public static function form(Schema $schema): Schema { return $schema->components([Select::make('user_id')->label('User')->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())->searchable()->required(), TextInput::make('type')->required(), TextInput::make('title')->required(), Textarea::make('message')->required(), Textarea::make('data')->helperText('JSON opsional')]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('user.name')->label('User'), TextColumn::make('title')->searchable(), TextColumn::make('type'), TextColumn::make('read_at')->dateTime()->placeholder('Belum dibaca'), TextColumn::make('created_at')->dateTime()->sortable()])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]); }
    public static function getPages(): array { return ['index' => ListNotifications::route('/'), 'create' => CreateNotification::route('/create'), 'edit' => EditNotification::route('/{record}/edit')]; }
}
