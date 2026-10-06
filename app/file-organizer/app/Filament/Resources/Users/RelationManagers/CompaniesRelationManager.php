<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('pivot.scope_role')->label('Scope role')->badge(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah perusahaan')
                    ->modalHeading('Tambahkan perusahaan ke user')
                    ->slideOver()
                    ->modalSubmitActionLabel('Tambahkan')
                    ->after(function (): void {
                        $ownerRecord = $this->getOwnerRecord()->refresh();
                        $ownerRecord->unsetRelation('companies');
                        $this->resetTable();
                    })
                    ->extraModalFooterActions(fn (AttachAction $action): array => [
                        $action->makeModalSubmitAction('attachAnother', ['another' => true])
                            ->label('Tambahkan & Tambahkan Lainnya'),
                    ])
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query): Builder => $query
                        ->where('classification', 'internal')
                        ->where('entity_type', 'company'))
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Perusahaan')
                            ->required(),
                        Placeholder::make('scope_role_help')
                            ->label('Panduan singkat')
                            ->content(new HtmlString(
                                '<div class="rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-white/10 dark:bg-white/5">'
                                . '<div class="mb-2 flex items-center gap-2 text-xs font-medium text-gray-600 dark:text-gray-300">'
                                . '<span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-primary-100 text-primary-600 dark:bg-primary-400/15 dark:text-primary-300">i</span>'
                                . '<span>Pilih peran sesuai tanggung jawab user pada perusahaan ini.</span>'
                                . '</div>'
                                . '<div class="grid gap-2 sm:grid-cols-3" role="list">'
                                . '<div class="group rounded-lg border border-gray-200 bg-white p-2.5 dark:border-white/10 dark:bg-white/5" title="Akses operasional dasar pada data perusahaan yang ditugaskan." role="listitem">'
                                . '<div class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white"><span class="text-primary-600">●</span>Member</div>'
                                . '<div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Operasional dasar</div>'
                                . '</div>'
                                . '<div class="group rounded-lg border border-gray-200 bg-white p-2.5 dark:border-white/10 dark:bg-white/5" title="Memeriksa dan mereview dokumen keuangan perusahaan." role="listitem">'
                                . '<div class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white"><span class="text-warning-600">●</span>Finance Reviewer</div>'
                                . '<div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Review keuangan</div>'
                                . '</div>'
                                . '<div class="group rounded-lg border border-gray-200 bg-white p-2.5 dark:border-white/10 dark:bg-white/5" title="Mengelola user dan administrasi dalam cakupan perusahaan." role="listitem">'
                                . '<div class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white"><span class="text-danger-600">●</span>Company Admin</div>'
                                . '<div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Administrasi perusahaan</div>'
                                . '</div>'
                                . '</div>'
                                . '<div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">Arahkan kursor ke kartu untuk melihat detail.</div>'
                                . '</div>',
                            )),
                        Select::make('scope_role')
                            ->label('Peran user di perusahaan')
                            ->options(['member' => 'Member', 'finance_reviewer' => 'Finance Reviewer', 'company_admin' => 'Company Admin'])
                            ->default('member')
                            ->required(),
                    ]),
            ])
            ->recordActions([DetachAction::make()])
            ->toolbarActions([BulkActionGroup::make([DetachBulkAction::make()])]);
    }
}
