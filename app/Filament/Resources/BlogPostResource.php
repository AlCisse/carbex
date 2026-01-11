<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $navigationLabel = 'Articles de blog';

    protected static ?string $modelLabel = 'Article';

    protected static ?string $pluralModelLabel = 'Articles';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contenu')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Forms\Components\Textarea::make('excerpt')
                            ->label('Extrait')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Resume court affiche dans les listes'),

                        Forms\Components\RichEditor::make('content')
                            ->label('Contenu')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'orderedList',
                                'bulletList',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                                'undo',
                                'redo',
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Image a la une')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('675')
                            ->directory('blog')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Publication')
                    ->schema([
                        Forms\Components\Select::make('author_id')
                            ->label('Auteur')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => auth()->id()),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'draft' => 'Brouillon',
                                'published' => 'Publie',
                            ])
                            ->required()
                            ->default('draft'),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Date de publication')
                            ->helperText('Laissez vide pour publier immediatement'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Titre SEO')
                            ->maxLength(60)
                            ->helperText('60 caracteres max'),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Description SEO')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('160 caracteres max'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->circular(false)
                    ->width(80)
                    ->height(45),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Auteur')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Brouillon',
                        'published' => 'Publie',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publication')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cree le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Brouillon',
                        'published' => 'Publie',
                    ]),

                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_site')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn (BlogPost $record): string => route('blog.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (BlogPost $record): bool => $record->isPublished()),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'content'];
    }
}
