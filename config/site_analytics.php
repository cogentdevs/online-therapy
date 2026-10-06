<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;

return [
    'types' => [
        'pages' => ['label' => 'Pages', 'column_label' => 'Page Name', 'model' => null, 'title_column' => null],
        'articles' => ['label' => 'Articles', 'column_label' => 'Article Title', 'model' => Article::class, 'title_column' => 'title'],
        'magazines' => ['label' => 'Magazines', 'column_label' => 'Magazine Title', 'model' => Magazine::class, 'title_column' => 'title'],
        'authors' => ['label' => 'Authors', 'column_label' => 'Author Name', 'model' => Author::class, 'title_column' => 'name'],
        'categories' => ['label' => 'Categories', 'column_label' => 'Category Name', 'model' => Category::class, 'title_column' => 'name'],
    ],
    'page_labels' => [
        'home' => 'Home',
        'subscriptions' => 'Subscriptions',
        'taza-shumara' => 'Taza Shumara',
        'sabqa-shumare' => 'Sabqa Shumare',
        'mazameen' => 'Mazameen',
        'about' => 'About',
        'categories' => 'Categories',
        'authors' => 'Authors',
    ],
];
