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


class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Livro';
    protected static ?string $pluralModelLabel = 'Livros';
    protected static ?string $navigationLabel = 'Livros';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Grid::make(3)
                ->schema([
                    // 👈 Coluna esquerda (2 colunas)
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
                                        ->options([
                                            'Engenharia de Software' => 'Engenharia de Software',
                                            'Direito' => 'Direito',
                                            'Medicina' => 'Medicina',
                                            'Administração' => 'Administração',
                                            'Geral' => 'Geral',
                                        ])->required(),
                                        
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
                                        ->downloadable()->required(),

                                    Forms\Components\Placeholder::make('pdf_viewer')
                                        ->label('Pré-visualização')
                                        ->content(fn ($record) => $record && $record->file_path
                                            ? new \Illuminate\Support\HtmlString('
                                                <iframe src="' . asset('storage/' . $record->file_path) . '" 
                                                    width="100%" height="600px" class="border rounded-lg shadow-sm">
                                                </iframe>')
                                            : 'Nenhum PDF carregado.'),
                                ]),
                        ]),

                    // 👉 Coluna direita (1 coluna)
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
                                        ->label('Aprovado?')->disabled()
                                        ->onIcon('heroicon-s-check-circle')
                                        ->offIcon('heroicon-s-x-circle')
                                        ->onColor('success')->offColor('danger'),

                                    Forms\Components\Textarea::make('rejection_reason')
                                        ->label('Motivo da Recusa')
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
                    ->label('Capa:')
                    ->width(90)
                    ->height(150)
                    ->square(false),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título:')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->label('Autor:')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Enviado por:'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em:')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Book $record) {
                        $record->update(['is_verified' => true, 'rejection_reason' => null]);
                    })
                    ->visible(fn(Book $record) => !$record->is_verified),

                Action::make('rejeitar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('reason')
                            ->label('Motivo da Rejeição')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (Book $record, array $data) {
                        $record->update([
                            'is_verified' => false,
                            'rejection_reason' => $data['reason'],
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
