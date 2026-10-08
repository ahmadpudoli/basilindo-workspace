<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\Company;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'companies';

    protected static ?string $title = 'Perusahaan yang dapat diakses user';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Perusahaan / pihak')->searchable()->sortable(),
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('pivot.scope_role')
                    ->label('Peran di perusahaan')
                    ->badge()
                    ->tooltip('Peran ini hanya berlaku pada perusahaan di baris ini. Role aplikasi user tetap menentukan hak akses globalnya.'),
            ])
            ->headerActions([
                Action::make('attachCompany')
                    ->label('Tambah perusahaan')
                    ->modalHeading('Tambahkan perusahaan ke user')
                    ->slideOver()
                    ->modalSubmitActionLabel('Tambahkan')
                    ->modalCancelActionLabel('Tutup')
                    ->action(function (array $data): void {
                        $this->getOwnerRecord()->companies()->syncWithoutDetaching([
                            $data['company_id'] => ['scope_role' => $data['scope_role']],
                        ]);
                        $this->refreshCompanyTable();
                    })
                    ->schema(fn (): array => [
                        Select::make('company_id')
                            ->label('Perusahaan')
                            ->options(fn (): array => Company::query()
                                ->where('classification', 'internal')
                                ->where('entity_type', 'company')
                                ->whereDoesntHave('users', fn (Builder $query): Builder => $query->whereKey($this->getOwnerRecord()->getKey()))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload()
                            ->hintIcon('heroicon-m-information-circle', 'Perusahaan yang menjadi cakupan akses user.' )
                            ->required(),
                        Placeholder::make('scope_role_help')
                            ->label('Panduan singkat')
                            ->content(new HtmlString(
                                '<div class="rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-white/10 dark:bg-white/5">'
                                . '<div class="text-xs text-gray-600 dark:text-gray-300">Peran di perusahaan berbeda dari Role aplikasi. Peran ini hanya mengatur tanggung jawab user pada perusahaan yang dipilih.</div>'
                                . '</div>',
                            )),
                        Select::make('scope_role')
                            ->label('Peran user di perusahaan')
                            ->options([
                                'member' => 'Member',
                                'finance_reviewer' => 'Finance Reviewer',
                                'company_admin' => 'Company Admin',
                            ])
                            ->default('member')
                            ->hintIcon('heroicon-m-information-circle', 'Peran ini hanya berlaku pada perusahaan yang dipilih.')
                            ->required(),
                    ]),
            ])
            ->recordActions([
                DetachAction::make()->after(fn (): mixed => $this->refreshCompanyTable()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()->after(fn (): mixed => $this->refreshCompanyTable()),
                ]),
            ]);
    }

    protected function refreshCompanyTable(): void
    {
        $this->getOwnerRecord()->refresh()->unsetRelation('companies');
        $this->resetTable();
    }
}
