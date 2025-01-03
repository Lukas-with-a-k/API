<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/auth/register' => [[['_route' => 'api_register', '_controller' => 'App\\Controller\\AuthController::register'], null, ['POST' => 0], null, false, false, null]],
        '/auth/login' => [[['_route' => 'api_login', '_controller' => 'App\\Controller\\AuthController::login'], null, ['POST' => 0], null, false, false, null]],
        '/categories' => [
            [['_route' => 'get_categories', '_controller' => 'App\\Controller\\CategorieController::getCategories'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'create_category', '_controller' => 'App\\Controller\\CategorieController::createCategory'], null, ['POST' => 0], null, false, false, null],
        ],
        '/films' => [
            [['_route' => 'get_films', '_controller' => 'App\\Controller\\FilmController::getFilms'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'create_film', '_controller' => 'App\\Controller\\FilmController::createFilm'], null, ['POST' => 0], null, false, false, null],
        ],
        '/likes' => [[['_route' => 'add_like', '_controller' => 'App\\Controller\\LikeController::addLike'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/c(?'
                    .'|ategories/([^/]++)(?'
                        .'|(*:68)'
                    .')'
                    .'|omments/([^/]++)(*:92)'
                .')'
                .'|/films/([^/]++)(?'
                    .'|(*:118)'
                    .'|/comments(?'
                        .'|(*:138)'
                    .')'
                .')'
                .'|/likes/(?'
                    .'|([^/]++)(*:166)'
                    .'|count/([^/]++)(*:188)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        68 => [
            [['_route' => 'get_category', '_controller' => 'App\\Controller\\CategorieController::getCategory'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'update_category', '_controller' => 'App\\Controller\\CategorieController::updateCategory'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'delete_category', '_controller' => 'App\\Controller\\CategorieController::deleteCategory'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        92 => [[['_route' => 'delete_comment', '_controller' => 'App\\Controller\\CommentController::deleteComment'], ['id'], ['DELETE' => 0], null, false, true, null]],
        118 => [
            [['_route' => 'get_film', '_controller' => 'App\\Controller\\FilmController::getFilm'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'update_film', '_controller' => 'App\\Controller\\FilmController::updateFilm'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'delete_film', '_controller' => 'App\\Controller\\FilmController::deleteFilm'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        138 => [
            [['_route' => 'add_comment', '_controller' => 'App\\Controller\\FilmController::addComment'], ['id'], ['POST' => 0], null, false, false, null],
            [['_route' => 'get_comments', '_controller' => 'App\\Controller\\FilmController::getComments'], ['id'], ['GET' => 0], null, false, false, null],
        ],
        166 => [[['_route' => 'remove_like', '_controller' => 'App\\Controller\\LikeController::removeLike'], ['id'], ['DELETE' => 0], null, false, true, null]],
        188 => [
            [['_route' => 'count_likes', '_controller' => 'App\\Controller\\LikeController::countLikes'], ['id'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
