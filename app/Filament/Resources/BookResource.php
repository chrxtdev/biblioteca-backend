<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;



class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $activeNavigationIcon = 'heroicon-s-book-open';

    protected static ?string $modelLabel = 'Livro';
    protected static ?string $pluralModelLabel = 'Livros';
    protected static ?string $navigationLabel = 'Livros';
    protected static ?string $navigationGroup = 'Biblioteca';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::pending()->count();
        
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getNavigationBadge() > 0 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Grid::make(3)
                ->schema([
                    Forms\Components\Group::make()
                        ->columnSpan(2)
                        ->schema([
                            Forms\Components\Section::make('Informações do Livro')
                                ->icon('heroicon-o-information-circle')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('Título')->required(),

                                    Forms\Components\TextInput::make('author')
                                        ->label('Autor')->required(),

                                    Forms\Components\Select::make('course')
                                        ->label('Curso / Categoria')
                                        ->options(\App\Enums\Course::options())
                                        ->required(),

                                    Forms\Components\Textarea::make('description')
                                        ->label('Descrição')->columnSpanFull()->rows(3),
                                ]),

                            Forms\Components\Section::make('Conteúdo do Livro (PDF)')
                                ->icon('heroicon-o-document-text')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\FileUpload::make('file_path')
                                        ->label('Arquivo PDF')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->directory('livros_pdfs')
                                        ->downloadable()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                            if (!$state) return;
                                            
                                            try {
                                                $path = Storage::disk('public')->path($state);
                                                if (file_exists($path)) {
                                                    $pdfService = app(\App\Services\PdfService::class);
                                                    $pages = $pdfService->countPages($path);
                                                    $set('total_pages', $pages);
                                                }
                                            } catch (\Exception $e) {
                                                // Log error
                                                \Illuminate\Support\Facades\Log::error('Erro ao contar páginas no BookResource: ' . $e->getMessage());
                                            }
                                        }),

                                    Forms\Components\Hidden::make('total_pages'),
                                    Forms\Components\Hidden::make('user_id')
                                        ->default(auth()->id()),

                                    Forms\Components\Placeholder::make('pdf_viewer')
                                        ->label('Pré-visualização')
                                        ->content(fn ($record, Get $get) => 
                                            ($record && $record->file_path)
                                                ? new \Illuminate\Support\HtmlString('
                                                    <iframe src="' . asset('storage/' . $record->file_path) . '"
                                                        width="100%" height="600px" style="border: none;">
                                                    </iframe>')
                                                : (($get('file_path')) 
                                                    ? 'Salve o livro depois visualize o PDF.' 
                                                    : 'Nenhum PDF carregado.')
                                        ),
                                ]),
                        ]),

                    Forms\Components\Group::make()
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\Section::make('Capa e Status')
                                ->schema([
                                    Forms\Components\FileUpload::make('cover_path')
                                        ->label('Capa do Livro')
                                        ->image()->imageEditor()
                                        ->directory('livros_capas')->columnSpanFull(),

                                    Forms\Components\Toggle::make('is_verified')
                                        ->label('Aprovado?')
                                        ->onIcon('heroicon-s-check-circle')
                                        ->offIcon('heroicon-s-x-circle')
                                        ->onColor('success')->offColor('danger'),

                                    Forms\Components\Textarea::make('rejection_reason')
                                        ->label('Motivo da Rejeição')
                                        ->visible(fn ($get) => $get('rejection_reason'))->disabled(),

                                    Forms\Components\Placeholder::make('created_at')
                                        ->label('Enviado em')
                                        ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i')),
                                ]),
                        ]),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_path')
                    ->label('Capa')
                    ->width(50)
                    ->height(75)
                    ->extraImgAttributes(['class' => 'rounded-md shadow-sm'])
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) => $record->author) // Combine Author as description
                    ->toggleable(),

                Tables\Columns\TextColumn::make('author')
                    ->label('Autor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true) // Hidden because it's now subtext of Title
                    ->color('gray')
                    ->limit(20),

                Tables\Columns\TextColumn::make('course')
                    ->label('Curso')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Enviado por')
                    ->icon('heroicon-m-user')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->is_verified) return 'Aprovado';
                        if ($record->rejection_reason) return 'Rejeitado';
                        return 'Pendente';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Aprovado' => 'success',
                        'Rejeitado' => 'danger',
                        'Pendente' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Aprovado' => 'heroicon-m-check-badge',
                        'Rejeitado' => 'heroicon-m-x-circle',
                        'Pendente' => 'heroicon-m-clock',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => '⏳ Pendentes',
                        'approved' => '✅ Aprovados',
                        'rejected' => '❌ Rejeitados',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'pending' => $query->where('is_verified', false)->whereNull('rejection_reason'),
                            'approved' => $query->where('is_verified', true),
                            'rejected' => $query->where('is_verified', false)->whereNotNull('rejection_reason'),
                            default => $query,
                        };
                    }),

                SelectFilter::make('course')
                    ->label('Curso')
                    ->options(\App\Enums\Course::options()),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprovar Livro')
                    ->modalDescription('Tem certeza que deseja aprovar este livro? Ele ficará disponível para todos os alunos.')
                    ->modalSubmitActionLabel('Sim, aprovar')
                    ->action(function (Book $record) {
                        $record->update(['is_verified' => true, 'rejection_reason' => null]);
                        app(\App\Services\BookService::class)->clearBookCache();
                    })
                    ->visible(fn(Book $record) => !$record->is_verified),

                Action::make('rejeitar')
                    ->label('Rejeitar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('reason')
                            ->label('Motivo da Rejeição')
                            ->required()
                            ->rows(4)
                            ->placeholder('Explique o motivo da rejeição...'),
                    ])
                    ->modalHeading('Rejeitar Livro')
                    ->modalSubmitActionLabel('Rejeitar')
                    ->action(function (Book $record, array $data) {
                        $record->update([
                            'is_verified' => false,
                            'rejection_reason' => $data['reason'],
                        ]);
                        app(\App\Services\BookService::class)->clearBookCache();
                    })
                    ->visible(fn (Book $record) => !$record->rejection_reason),

                Action::make('undo_rejection')
                    ->label('Voltar para Análise')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Retornar para Análise')
                    ->modalDescription('Deseja retornar este livro para o status de "Pendente"?')
                    ->action(function (Book $record) {
                        $record->update([
                            'is_verified' => false,
                            'rejection_reason' => null,
                        ]);
                        app(\App\Services\BookService::class)->clearBookCache();
                    })
                    ->visible(fn (Book $record) => $record->rejection_reason !== null || $record->is_verified),
                    
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }

    /**
     * Customiza a query base para incluir eager loading.
     * Evita problema N+1 ao carregar o nome do usuário.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}

