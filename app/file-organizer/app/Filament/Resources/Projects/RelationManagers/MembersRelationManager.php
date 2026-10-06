<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->members_count ?? $ownerRecord->members()->count();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->recordSelect(fn (Select $select) => $select
                        ->createOptionForm(fn (Schema $form) => UserResource::form($form)
                            ->model(User::class)
                        )
                        ->createOptionUsing(function (array $data, Schema $form) {
                            $user = User::create($data);
                            $form->model($user)->saveRelationships();

                            return $user->id;
                        })
                        ->createOptionAction(fn (Action $action) => $action
                            ->modalWidth('lg')
                        )
                    )
                    ->label('Add Member'),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Remove'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Remove Selected'),
                ]),
            ]);
    }
}
