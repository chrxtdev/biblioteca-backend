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
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('author')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('file_path')
                    ->label('Arquivo PDF')
                    ->directory('livros_pdfs')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(),

                Forms\Components\FileUpload::make('cover_path')
                    ->label('Capa do Livro')
                    ->image()
                    ->directory('livros_capas'),

                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

//                Forms\Components\Toggle::make('is_verified')
//                    ->label('Verificado pela Faculdade?')
//                    ->default(false),

                Forms\Components\Select::make('course')
                    ->label('Curso Relacionado')
                    ->options(Book::COURSES)
                    ->searchable()
                    ->required(),
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
