<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\RelationManagers\CompaniesRelationManager;
use App\Filament\Resources\Users\RelationManagers\ProjectsRelationManager;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('roles')
                    ->label('Role aplikasi')
                    ->relationship(
                        'roles',
                        'name',
                        modifyQueryUsing: function (Builder $query, Select $component): Builder {
                            if (! $component->getRecord()?->isProtectedSystemAccount()) {
                                $query->where('name', '!=', 'super_admin');
                            }

                            return $query;
                        },
                    )
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->disabled(fn (Select $component): bool => $component->getRecord()?->isProtectedSystemAccount() ?? false)
                    ->dehydrated(fn (Select $component): bool => ! ($component->getRecord()?->isProtectedSystemAccount() ?? false))
                    ->hintIcon(
                        'heroicon-m-information-circle',
                        'Menentukan hak akses global user di aplikasi, seperti menu dan aksi administrasi yang dapat digunakan. Role ini berlaku lintas perusahaan.',
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->tooltip(fn (User $record): string => $record->roles->pluck('name')->join(', ') ?: 'No Roles')
                    ->sortable(),

                TextColumn::make('projects_count')
                    ->label('Projects')
                    ->counts('projects')
                    ->tooltip(fn (User $record): string => $record->projects->pluck('name')->join(', ') ?: 'No Projects')
                    ->sortable(),

                TextColumn::make('assigned_tickets_count')
                    ->label('Assigned Tickets')
                    ->getStateUsing(fn (User $record): int => DB::connection('pgsql')
                        ->table('ticket_users')
                        ->where('user_id', $record->getKey())
                        ->count())
                    ->tooltip('Number of tickets assigned to this user')
                    ->sortable(),

                TextColumn::make('created_tickets_count')
                    ->label('Created Tickets')
                    ->getStateUsing(fn (User $record): int => DB::connection('pgsql')
                        ->table('tickets')
                        ->where('created_by', $record->getKey())
                        ->count())
                    ->tooltip('Number of tickets created by this user')
                    ->sortable(),

                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_projects')
                    ->label('Has Projects')
                    ->query(fn (Builder $query): Builder => $query->whereHas('projects')),

                Filter::make('has_assigned_tickets')
                    ->label('Has Assigned Tickets')
                    ->query(fn (Builder $query): Builder => $query->whereIn(
                        $query->getModel()->getTable().'.id',
                        DB::connection('pgsql')->table('ticket_users')->distinct()->pluck('user_id'),
                    )),

                Filter::make('has_created_tickets')
                    ->label('Has Created Tickets')
                    ->query(fn (Builder $query): Builder => $query->whereIn(
                        $query->getModel()->getTable().'.id',
                        DB::connection('pgsql')->table('tickets')->distinct()->pluck('created_by'),
                    )),

                // Filter by role
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Filter::make('email_unverified')
                    ->label('Email Unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    // NEW: Bulk action to assign role
                    BulkAction::make('assignRole')
                        ->label('Assign Role')
                        ->icon('heroicon-o-shield-check')
                        ->form([
                            Select::make('roles')
                                ->label('Role aplikasi')
                                ->relationship(
                                    'roles',
                                    'name',
                                    modifyQueryUsing: fn (Builder $query): Builder => $query->where('name', '!=', 'super_admin'),
                                )
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->hintIcon(
                                    'heroicon-m-information-circle',
                                    'Role aplikasi berlaku secara global. Scope perusahaan tetap diatur pada daftar perusahaan user.',
                                )
                                ->required(),

                            Radio::make('role_mode')
                                ->label('Assignment Mode')
                                ->options([
                                    'replace' => 'Replace existing roles',
                                    'add' => 'Add to existing roles',
                                ])
                                ->default('add')
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                if ($data['role_mode'] === 'replace') {
                                    $record->roles()->sync($data['roles']);
                                } else {
                                    $record->roles()->syncWithoutDetaching($data['roles']);
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CompaniesRelationManager::class,
            ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
