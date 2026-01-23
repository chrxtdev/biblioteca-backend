<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class LatestBooksWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '📚 Últimos Livros Enviados';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Book::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                ImageColumn::make('cover_path')
                    ->label('Capa')
                    ->width(60)
                    ->height(80),

                TextColumn::make('title')
                    ->label('Título')
                    ->weight('bold')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('author')
                    ->label('Autor')
                    ->color('gray')
                    ->limit(25),

                TextColumn::make('user.name')
                    ->label('Enviado por')
                    ->icon('heroicon-m-user')
                    ->color('info'),

                TextColumn::make('course')
                    ->label('Curso')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('created_at')
                    ->label('Enviado em')
                    ->since()
                    ->color('gray'),

                IconColumn::make('is_verified')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Book $record): string => route('filament.admin.resources.books.edit', $record))
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
