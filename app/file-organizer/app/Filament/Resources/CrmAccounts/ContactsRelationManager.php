<?php

namespace App\Filament\Resources\CrmAccounts;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    protected static ?string $title = 'Contact person';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama contact')->required()->maxLength(255),
            TextInput::make('email')->label('Email')->email()->maxLength(255),
            TextInput::make('phone')->label('Nomor telepon')->tel()->maxLength(50),
            TextInput::make('job_title')->label('Jabatan')->maxLength(150),
            Toggle::make('is_primary')->label('Contact utama'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('job_title')->label('Jabatan')->searchable(),
                TextColumn::make('email')->label('Email')->copyable(),
                TextColumn::make('phone')->label('Telepon'),
                IconColumn::make('is_primary')->label('Utama')->boolean(),
            ])
            ->headerActions([CreateAction::make()->label('Contact baru')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
